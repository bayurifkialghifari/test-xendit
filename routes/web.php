<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\VaController::class, 'index'])->name('index');
Route::get('/create-va', [App\Http\Controllers\VaController::class, 'createVirtualAccount'])->name('create-va');
Route::get('/get-va/{id}', [App\Http\Controllers\VaController::class, 'getVirtualAccount'])->name('get-va');
Route::post('/callback-virtual-account-paid', [App\Http\Controllers\VaController::class, 'virtualAccountPaidWebhookUrl'])->name('callback-virtual-account-paid');
