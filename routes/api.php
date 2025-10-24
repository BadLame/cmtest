<?php

use App\Http\Controllers\OperationsController;
use Illuminate\Support\Facades\Route;

Route::name('operations.')->group(function () {
    Route::get('/balance/{ub:user_id}', [OperationsController::class, 'balance'])
        ->where('ub', '[0-9]+')
        ->name('balance');
});
