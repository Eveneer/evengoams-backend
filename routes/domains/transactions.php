<?php

declare(strict_types=1);

use App\Domains\Transactions\Actions\CreateTransaction;
use App\Domains\Transactions\Actions\RestoreTransaction;
use App\Domains\Transactions\Actions\TrashTransaction;
use App\Domains\Transactions\Actions\UpdateTransaction;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('transactions')->group(function (): void {
    Route::post('/', CreateTransaction::class);
    Route::put('/{id}', UpdateTransaction::class);
    Route::post('/{id}/trash', TrashTransaction::class);
    Route::post('/{id}/restore', RestoreTransaction::class);
});