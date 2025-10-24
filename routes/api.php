<?php

use App\Http\Controllers\OperationsController;
use Illuminate\Support\Facades\Route;

Route::name('operations.')->group(function () {
    Route::get('/balance/{user_id}', [OperationsController::class, 'balance'])
        ->where('user_id', '[0-9]+')
        ->name('balance');
    Route::post('/deposit', [OperationsController::class, 'deposit'])->name('deposit');
});
