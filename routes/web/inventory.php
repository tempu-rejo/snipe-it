<?php

use App\Http\Controllers\Inventory\QuickScanCheckinController;
use App\Http\Controllers\Inventory\BulkCheckoutController;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Inventory Routes
|--------------------------------------------------------------------------
|
| Register all the inventory transaction routes.
|
*/

Route::group([
    'prefix' => 'inventory',
    'middleware' => ['auth'],
], function () {

    // Quick Scan CheckIN Routes
    Route::get('quickscan-checkin', [QuickScanCheckinController::class, 'index'])
        ->name('inventory.quickscan-checkin.index')
        ->breadcrumbs(fn (Trail $trail) =>
            $trail->parent('hardware.index')
                ->push('Quick Scan CheckIN', route('inventory.quickscan-checkin.index'))
        );

    Route::get('quickscan-checkin/data', [QuickScanCheckinController::class, 'data'])
        ->name('inventory.quickscan-checkin.data');

    Route::get('quickscan-checkin/create', [QuickScanCheckinController::class, 'create'])
        ->name('inventory.quickscan-checkin.create')
        ->breadcrumbs(fn (Trail $trail) =>
            $trail->parent('inventory.quickscan-checkin.index')
                ->push('Buat Transaksi CheckIN', route('inventory.quickscan-checkin.create'))
        );

    Route::post('quickscan-checkin', [QuickScanCheckinController::class, 'store'])
        ->name('inventory.quickscan-checkin.store');

    Route::get('quickscan-checkin/{id}', [QuickScanCheckinController::class, 'show'])
        ->name('inventory.quickscan-checkin.show')
        ->breadcrumbs(fn (Trail $trail, $id) =>
            $trail->parent('inventory.quickscan-checkin.index')
                ->push('Detail Transaksi CheckIN', route('inventory.quickscan-checkin.show', $id))
        );

    Route::get('quickscan-checkin/{id}/edit', [QuickScanCheckinController::class, 'edit'])
        ->name('inventory.quickscan-checkin.edit')
        ->breadcrumbs(fn (Trail $trail, $id) =>
            $trail->parent('inventory.quickscan-checkin.show', $id)
                ->push('Edit Transaksi CheckIN', route('inventory.quickscan-checkin.edit', $id))
        );

    Route::put('quickscan-checkin/{id}', [QuickScanCheckinController::class, 'update'])
        ->name('inventory.quickscan-checkin.update');

    Route::get('quickscan-checkin/{id}/print', [QuickScanCheckinController::class, 'print'])
        ->name('inventory.quickscan-checkin.print');

    // Bulk CheckOut Routes
    Route::get('bulk-checkout', [BulkCheckoutController::class, 'index'])
        ->name('inventory.bulk-checkout.index')
        ->breadcrumbs(fn (Trail $trail) =>
            $trail->parent('hardware.index')
                ->push('Bulk CheckOut', route('inventory.bulk-checkout.index'))
        );

    Route::get('bulk-checkout/data', [BulkCheckoutController::class, 'data'])
        ->name('inventory.bulk-checkout.data');

    Route::get('bulk-checkout/create', [BulkCheckoutController::class, 'create'])
        ->name('inventory.bulk-checkout.create')
        ->breadcrumbs(fn (Trail $trail) =>
            $trail->parent('inventory.bulk-checkout.index')
                ->push('Buat Transaksi CheckOut', route('inventory.bulk-checkout.create'))
        );

    Route::post('bulk-checkout', [BulkCheckoutController::class, 'store'])
        ->name('inventory.bulk-checkout.store');

    Route::get('bulk-checkout/{id}', [BulkCheckoutController::class, 'show'])
        ->name('inventory.bulk-checkout.show')
        ->breadcrumbs(fn (Trail $trail, $id) =>
            $trail->parent('inventory.bulk-checkout.index')
                ->push('Detail Transaksi CheckOut', route('inventory.bulk-checkout.show', $id))
        );

    Route::get('bulk-checkout/{id}/edit', [BulkCheckoutController::class, 'edit'])
        ->name('inventory.bulk-checkout.edit')
        ->breadcrumbs(fn (Trail $trail, $id) =>
            $trail->parent('inventory.bulk-checkout.show', $id)
                ->push('Edit Transaksi CheckOut', route('inventory.bulk-checkout.edit', $id))
        );

    Route::put('bulk-checkout/{id}', [BulkCheckoutController::class, 'update'])
        ->name('inventory.bulk-checkout.update');

    Route::get('bulk-checkout/{id}/print', [BulkCheckoutController::class, 'print'])
        ->name('inventory.bulk-checkout.print');
});
