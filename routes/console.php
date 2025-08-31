
<?php


use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\ExpirePendingOrders;
use App\Console\Commands\PurgeExpiredInventory;


// Scheduler registrations
Schedule::command(ExpirePendingOrders::class, ['--minutes=30'])->everyFifteenMinutes();

// Inventar-Purge einmal täglich am Abend (23:30). Standard 60 Tage (≈ 2 Monate)
Schedule::command(PurgeExpiredInventory::class)->dailyAt('23:30');




