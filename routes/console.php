<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('update-prices')->everyOddHour();