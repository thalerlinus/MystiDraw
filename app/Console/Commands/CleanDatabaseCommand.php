<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Ticket;
use App\Models\TicketOutcome;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Order;
use App\Models\UserInventory;
use App\Models\UserItem;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\Raffle;
use App\Models\RaffleItem;

class CleanDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean 
                           {--force : Force deletion without confirmation}
                           {--tickets-only : Only clean tickets and outcomes}
                           {--orders-only : Only clean orders and payments}
                           {--inventory-only : Only clean user inventory}
                           {--core : Keep only users + raffles + raffle items (reset counters)}
                           {--full : Legacy full wipe (everything incl. counters)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean database by removing tickets, outcomes, orders, payments and inventory data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Database Cleaning Tool');
        $this->info('========================');

        // Show current state
        $this->showCurrentState();

        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to proceed with cleaning?')) {
                $this->info('Cancelled.');
                return;
            }
        }

        $this->info('Starting database cleanup...');

        try {
            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            if ($this->option('tickets-only')) {
                $this->cleanTicketsAndOutcomes();
            } elseif ($this->option('orders-only')) {
                $this->cleanOrdersAndPayments();
            } elseif ($this->option('inventory-only')) {
                $this->cleanInventory();
            } elseif ($this->option('full')) {
                $this->info('➡️  Running FULL cleanup (legacy behavior)');
                $this->cleanAll();
                $this->resetRaffleCounters();
            } else {
                // Default jetzt Core Clean
                $this->info('➡️  Running default CORE cleanup (users + raffles + raffle items bleiben)');
                $this->cleanToCore();
            }

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->info('✅ Database cleanup completed successfully!');
            $this->showCurrentState();

        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->error('❌ Error during cleanup: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function showCurrentState()
    {
        $this->info('📊 Current Database State:');
        $this->table(['Table', 'Count'], [
            ['Tickets', Ticket::count()],
            ['Ticket Outcomes', TicketOutcome::count()],
            ['Orders', Order::count()],
            ['Order Items', OrderItem::count()],
            ['Payments', Payment::count()],
            ['User Inventory', UserInventory::count()],
            ['User Items', UserItem::count()],
            ['Shipments', Shipment::count()],
            ['Shipment Items', ShipmentItem::count()],
            ['Raffles', Raffle::count()],
            ['Raffle Items', RaffleItem::count()],
            ['Raffle Items (total qty)', RaffleItem::sum('quantity_total')],
            ['Raffle Items (awarded qty)', RaffleItem::sum('quantity_awarded')],
        ]);
        $this->newLine();
    }

    private function cleanAll()
    {
        $this->info('🗑️  Cleaning all data...');
        
        // Clean in dependency order (children first)
        $this->cleanInventory();
        $this->cleanTicketsAndOutcomes();
        $this->cleanOrdersAndPayments();
    }

    private function cleanToCore()
    {
        $this->info('🧪 Core Clean: Entferne alle dynamischen Daten außer Users, Raffles, Raffle Items & Products');
        // Reihenfolge wichtig wegen FKs
        $this->cleanInventory();
        $this->cleanTicketsAndOutcomes();
        $this->cleanOrdersAndPayments();
        $this->cleanRafflePurchases();
        // Sonstige Tabellen die evtl. später dazu kommen könnten hier ergänzen
        $this->resetRaffleCounters();
        $this->line('✔ Core Zustand hergestellt.');
    }

    private function cleanTicketsAndOutcomes()
    {
        $this->info('🎫 Cleaning tickets and outcomes...');
        
        $ticketOutcomes = TicketOutcome::count();
        $tickets = Ticket::count();

        if ($ticketOutcomes > 0) {
            TicketOutcome::truncate();
            $this->line("   - Deleted {$ticketOutcomes} ticket outcomes");
        }

        if ($tickets > 0) {
            Ticket::truncate();
            $this->line("   - Deleted {$tickets} tickets");
        }
    }

    private function cleanOrdersAndPayments()
    {
        $this->info('📋 Cleaning orders and payments...');
        
        $payments = Payment::count();
        $orderItems = OrderItem::count();
        $orders = Order::count();

        if ($payments > 0) {
            Payment::truncate();
            $this->line("   - Deleted {$payments} payments");
        }

        if ($orderItems > 0) {
            OrderItem::truncate();
            $this->line("   - Deleted {$orderItems} order items");
        }

        if ($orders > 0) {
            Order::truncate();
            $this->line("   - Deleted {$orders} orders");
        }
    }

    private function cleanInventory()
    {
        $this->info('📦 Cleaning user inventory...');
        
        $shipmentItems = ShipmentItem::count();
        $shipments = Shipment::count();
        $userItems = UserItem::count();
        $userInventory = UserInventory::count();

        if ($shipmentItems > 0) {
            ShipmentItem::truncate();
            $this->line("   - Deleted {$shipmentItems} shipment items");
        }

        if ($shipments > 0) {
            Shipment::truncate();
            $this->line("   - Deleted {$shipments} shipments");
        }

        if ($userItems > 0) {
            UserItem::truncate();
            $this->line("   - Deleted {$userItems} user items");
        }

        if ($userInventory > 0) {
            UserInventory::truncate();
            $this->line("   - Deleted {$userInventory} user inventory entries");
        }
    }

    private function cleanRafflePurchases()
    {
        if (DB::getSchemaBuilder()->hasTable('raffle_purchases')) {
            $count = DB::table('raffle_purchases')->count();
            if ($count > 0) {
                DB::table('raffle_purchases')->truncate();
                $this->line("   - Deleted {$count} raffle purchases");
            }
        }
    }

    private function resetRaffleCounters()
    {
        $this->info('♻️  Reset raffle counters & awarded quantities...');
        // quantity_awarded zurücksetzen (Konfiguration erhalten)
        DB::table('raffle_items')->update(['quantity_awarded' => 0]);
        // Ticket-Zähler zurücksetzen
        if (DB::getSchemaBuilder()->hasColumn('raffles','tickets_sold')) {
            DB::table('raffles')->update(['tickets_sold' => 0]);
        }
        if (DB::getSchemaBuilder()->hasColumn('raffles','next_ticket_serial')) {
            DB::table('raffles')->update(['next_ticket_serial' => 0]);
        }
        // Optional könnte man Status zurücksetzen – bewusst weggelassen.
        $this->line('   - raffle_items.quantity_awarded → 0');
        $this->line('   - raffles.tickets_sold / next_ticket_serial → 0');
    }
}
