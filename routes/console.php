<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('hirfati:archive-requests')->monthlyOn(1, '03:00');
