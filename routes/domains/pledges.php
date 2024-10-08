<?php

declare(strict_types=1);

use App\Domains\Pledges\Actions\CreatePledge;
use App\Domains\Pledges\Actions\Queries\GetPledge;
use App\Domains\Pledges\Actions\Queries\GetPledges;
use App\Domains\Pledges\Actions\RestorePledge;
use App\Domains\Pledges\Actions\TrashPledge;
use App\Domains\Pledges\Actions\UpdatePledge;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->prefix('pledges')->group(function (): void {
    Route::get('/', GetPledges::class);
    Route::get('/{id}', GetPledge::class);
    Route::post('/', CreatePledge::class);
    Route::put('/{id}', UpdatePledge::class);
    Route::post('/{id}/trash', TrashPledge::class);
    Route::post('/{id}/restore', RestorePledge::class);
});