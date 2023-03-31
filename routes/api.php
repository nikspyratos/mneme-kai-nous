<?php

use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('transactions/investec', [TransactionController::class, 'createTransactionFromInvestec']);
    Route::post('transactions/sms', [TransactionController::class, 'createTransactionFromSms']);
    Route::post('transactions/push', [TransactionController::class, 'createTransactionFromPush']);
});
