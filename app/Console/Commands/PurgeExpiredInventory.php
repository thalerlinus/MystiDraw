<?php

namespace App\Console\Commands;

use App\Jobs\SendInventoryExpiryReminder;
use App\Models\TicketOutcome;
use App\Models\UserItem;
use App\Models\User;
use App\Models\InventoryRecovery;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurgeExpiredInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:purge-expired
                            {--days=60 : Anzahl Tage bis Items verfallen (Standard 60 ≈ 2 Monate)}
                            {--dry-run : Nur zählen und berichten, nichts löschen}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Löscht Inventar-Items, die älter als die Frist sind und nicht versendet wurden (entspricht AGB: max. 2 Monate).';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dry = (bool) $this->option('dry-run');
        $cutoff = Carbon::now()->subDays($days);

        $this->info("Inventar-Aufräumung starten (älter als {$days} Tage, Cutoff: {$cutoff->toDateTimeString()})" );

        // Vorab: Erinnerungen 7 Tage vor Verfall senden (nur einmal pro User pro Lauf)
        $reminderCutoffStart = Carbon::now()->subDays($days - 7);
        $reminderCutoffEnd = Carbon::now()->subDays($days - 6); // engeres Fenster, um Double-Sends zu reduzieren
        $this->line("→ Sende Erinnerungen für Items mit owned/assigned zwischen {$reminderCutoffStart->toDateString()} und {$reminderCutoffEnd->toDateString()}");

        // Nutzer mit TicketOutcomes (assigned), die in das 7-Tage-Fenster fallen
        $userIdsToRemindFromOutcomes = TicketOutcome::where('status','assigned')
            ->where(function ($q) use ($reminderCutoffStart, $reminderCutoffEnd) {
                $q->whereBetween('assigned_at', [$reminderCutoffStart, $reminderCutoffEnd])
                  ->orWhere(function ($q2) use ($reminderCutoffStart, $reminderCutoffEnd) {
                      $q2->whereNull('assigned_at')->whereBetween('created_at', [$reminderCutoffStart, $reminderCutoffEnd]);
                  });
            })
            ->whereHas('ticket')
            ->pluck('ticket.user_id')
            ->unique()
            ->values();

        // Nutzer mit UserItems (owned) im 7-Tage-Fenster (Fallback)
        $userIdsToRemindFromUserItems = UserItem::where('status','owned')
            ->whereBetween('owned_at', [$reminderCutoffStart, $reminderCutoffEnd])
            ->whereDoesntHave('shipmentItems')
            ->pluck('user_id')
            ->unique()
            ->values();

        $userIdsToRemind = $userIdsToRemindFromOutcomes->merge($userIdsToRemindFromUserItems)->unique()->values();
        $reminded = 0;
        if ($userIdsToRemind->isNotEmpty()) {
            $users = User::whereIn('id', $userIdsToRemind)->get();
            foreach ($users as $user) {
                SendInventoryExpiryReminder::dispatch($user);
                $reminded++;
            }
        }
        $this->line("→ Erinnerungs-Emails dispatcht: {$reminded}");

        // 1) TicketOutcomes: status = assigned, älter als Cutoff (assigned_at bevorzugt, sonst created_at)
        $assignedQuery = TicketOutcome::where('status', 'assigned')
            ->where(function ($q) use ($cutoff) {
                $q->whereNotNull('assigned_at')->where('assigned_at', '<', $cutoff)
                  ->orWhere(function ($q2) use ($cutoff) {
                      $q2->whereNull('assigned_at')->where('created_at', '<', $cutoff);
                  });
            });

        $assignedCount = (clone $assignedQuery)->count();

        // 2) UserItems: status = owned, älter als Cutoff, ohne Shipments
        // (Hinweis: Die meisten nicht reservierten Items existieren als TicketOutcome 'assigned'.
        //  Dieser Schritt fängt seltene Fälle ab, in denen UserItem bereits erzeugt, aber nicht reserviert wurde.)
        $ownedQuery = UserItem::where('status', 'owned')
            ->where('owned_at', '<', $cutoff)
            ->whereDoesntHave('shipmentItems');

        $ownedCount = (clone $ownedQuery)->count();

        $this->line("→ Kandidaten: {$assignedCount} TicketOutcome(s) (assigned), {$ownedCount} UserItem(s) (owned)");

        if ($dry) {
            $this->info('Dry-Run aktiv – es wurden keine Daten gelöscht.');
            return Command::SUCCESS;
        }

        DB::transaction(function () use ($assignedQuery, $ownedQuery, &$assignedCount, &$ownedCount) {
            // Vor dem Löschen: gruppiere Outcomes nach raffle_item_id und product_id, um zu dekrementieren und zu loggen
            $now = now();
            $grouped = (clone $assignedQuery)
                ->join('tickets', 'tickets.id', '=', 'ticket_outcomes.ticket_id')
                ->select('ticket_outcomes.raffle_item_id','ticket_outcomes.product_id','tickets.user_id', DB::raw('COUNT(*) as qty'))
                ->groupBy('ticket_outcomes.raffle_item_id','ticket_outcomes.product_id','tickets.user_id')
                ->get();

            foreach ($grouped as $g) {
                if ($g->raffle_item_id) {
                    $dec = (int) $g->qty;
                    DB::table('raffle_items')
                        ->where('id', $g->raffle_item_id)
                        ->update(['quantity_awarded' => DB::raw("CASE WHEN quantity_awarded >= {$dec} THEN quantity_awarded - {$dec} ELSE 0 END")]);
                }
                InventoryRecovery::create([
                    'user_id' => $g->user_id,
                    'product_id' => $g->product_id,
                    'raffle_item_id' => $g->raffle_item_id,
                    'quantity' => (int)$g->qty,
                    'purged_at' => $now,
                ]);
            }

            // Lösche zuerst TicketOutcomes – CASCADE löscht ggf. verknüpfte UserItems automatisch
            $deletedOutcomes = (clone $assignedQuery)->delete();
            // Danach noch übrig gebliebene orphan/owned UserItems löschen (z. B. ohne Outcome-Ref)
            $deletedOwned = (clone $ownedQuery)->whereNull('ticket_outcome_id')->delete();

            // Aktualisierte effektive Zahlen zurückgeben (falls sich Counts durch CASCADE ändern)
            $assignedCount = $deletedOutcomes;
            $ownedCount = $deletedOwned;
        });

        $this->info("Gelöscht: {$assignedCount} TicketOutcome(s) (assigned)");
        if ($ownedCount > 0) {
            $this->info("Gelöscht: {$ownedCount} UserItem(s) (owned, ohne Outcome)");
        }

        Log::info('inventory:purge-expired ausgeführt', [
            'days' => $days,
            'deleted_assigned_outcomes' => $assignedCount,
            'deleted_owned_user_items' => $ownedCount,
        ]);

        $this->info('Inventar-Aufräumung abgeschlossen.');
        return Command::SUCCESS;
    }
}
