<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('update-prices')->everyOddHour();
Schedule::command('app:look-for-new-telegram-users')->everyFifteenMinutes();