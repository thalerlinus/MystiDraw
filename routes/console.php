
<?php


use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\ExpirePendingOrders;



Schedule::command(ExpirePendingOrders::class)->everyMinute();

