<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpirePendingOrders extends Command
{
    protected $signature = 'orders:expire-pending {--minutes=15 : Minuten bis eine pending Order ausläuft}';
    protected $description = 'Setzt alte pending Orders auf cancelled und zugehörige Payments auf failed/cancelled nach Ablauf der Frist.';

    public function handle(): int
    {
        $minutes = (int)$this->option('minutes');
        $cutoff = Carbon::now()->subMinutes($minutes);

        $orders = Order::where('status', Order::STATUS_PENDING)
            ->where('created_at', '<', $cutoff)
            ->with(['payments' => function($q){ $q->where('status','pending'); }])
            ->get();

        if ($orders->isEmpty()) {
            $this->info('Keine abgelaufenen pending Orders.');
            return Command::SUCCESS;
        }

        $countOrders = 0; $countPayments = 0;

        DB::transaction(function () use ($orders, &$countOrders, &$countPayments) {
            foreach ($orders as $order) {
                $order->update(['status' => Order::STATUS_CANCELLED]);
                $countOrders++;
                foreach ($order->payments as $payment) {
                    $payment->update(['status' => 'failed']);
                    $countPayments++;
                }
                Log::info('Order automatisch abgelaufen', ['order_id'=>$order->id,'payments_failed'=>$order->payments->count()]);
            }
        });

        $this->info("Abgelaufen: {$countOrders} Orders, {$countPayments} Payments aktualisiert.");
        return Command::SUCCESS;
    }
}
