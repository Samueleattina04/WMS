<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('wms:check-alerts')->hourly()->withoutOverlapping();
