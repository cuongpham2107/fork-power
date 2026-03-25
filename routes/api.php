<?php

use App\Http\Controllers\Api\BatteryUsageController;
use Illuminate\Support\Facades\Route;

Route::post('/battery-usages', [BatteryUsageController::class, 'store']);
