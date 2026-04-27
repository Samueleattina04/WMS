<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\PickingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitOfMeasureController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::middleware(['auth'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', ProductController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('units', UnitOfMeasureController::class)->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['units' => 'unitOfMeasure']);

    // Warehouses & Structure
    Route::resource('warehouses', WarehouseController::class);
    Route::get('warehouses/{warehouse}/zones/create', [WarehouseController::class, 'createZone'])->name('warehouses.zones.create');
    Route::post('warehouses/{warehouse}/zones', [WarehouseController::class, 'storeZone'])->name('warehouses.zones.store');
    Route::get('warehouses/{warehouse}/zones/{zone}/edit', [WarehouseController::class, 'editZone'])->name('warehouses.zones.edit');
    Route::put('warehouses/{warehouse}/zones/{zone}', [WarehouseController::class, 'updateZone'])->name('warehouses.zones.update');
    Route::get('warehouses/{warehouse}/zones/{zone}/shelves/create', [WarehouseController::class, 'createShelf'])->name('warehouses.shelves.create');
    Route::post('warehouses/{warehouse}/zones/{zone}/shelves', [WarehouseController::class, 'storeShelf'])->name('warehouses.shelves.store');
    Route::get('warehouses/{warehouse}/zones/{zone}/shelves/{shelf}/slots/create', [WarehouseController::class, 'createSlot'])->name('warehouses.slots.create');
    Route::post('warehouses/{warehouse}/zones/{zone}/shelves/{shelf}/slots', [WarehouseController::class, 'storeSlot'])->name('warehouses.slots.store');
    Route::get('slots/{slot}', [WarehouseController::class, 'slotDetail'])->name('slots.show');

    Route::resource('movements', MovementController::class)->only(['index', 'create', 'store', 'show']);

    Route::resource('purchase-orders', PurchaseOrderController::class)->except(['edit', 'update']);
    Route::get('purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
    Route::post('purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'processReceiving'])->name('purchase-orders.process-receiving');

    Route::resource('sales-orders', SalesOrderController::class)->except(['edit', 'update']);
    Route::post('sales-orders/{salesOrder}/generate-picking', [SalesOrderController::class, 'generatePickingList'])->name('sales-orders.generate-picking');
    Route::patch('sales-orders/{salesOrder}/status', [SalesOrderController::class, 'updateStatus'])->name('sales-orders.update-status');

    Route::get('picking', [PickingController::class, 'index'])->name('picking.index');
    Route::get('picking/{pickingList}', [PickingController::class, 'show'])->name('picking.show');
    Route::post('picking/{pickingList}/start', [PickingController::class, 'start'])->name('picking.start');
    Route::post('picking/{pickingList}/items/{item}/confirm', [PickingController::class, 'confirmItem'])->name('picking.confirm-item');
    Route::post('picking/{pickingList}/complete', [PickingController::class, 'complete'])->name('picking.complete');

    Route::resource('inventory', InventoryController::class)->except(['edit', 'update', 'destroy']);
    Route::post('inventory/{inventorySession}/start', [InventoryController::class, 'start'])->name('inventory.start');
    Route::patch('inventory/{inventorySession}/items/{item}', [InventoryController::class, 'updateItem'])->name('inventory.update-item');
    Route::post('inventory/{inventorySession}/complete', [InventoryController::class, 'complete'])->name('inventory.complete');

    Route::get('alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::post('alerts/{alert}/resolve', [AlertController::class, 'resolve'])->name('alerts.resolve');
    Route::post('alerts/resolve-all', [AlertController::class, 'resolveAll'])->name('alerts.resolve-all');

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('movements', [ReportController::class, 'movements'])->name('movements');
        Route::get('stock-value', [ReportController::class, 'stockValue'])->name('stock-value');
        Route::get('purchase-orders', [ReportController::class, 'purchaseOrders'])->name('purchase-orders');
        Route::get('sales-orders', [ReportController::class, 'salesOrders'])->name('sales-orders');
    });

    Route::get('documents/ddt-entrata/{purchaseOrder}', [DocumentController::class, 'ddtEntrata'])->name('documents.ddt-entrata');
    Route::get('documents/picking-list/{pickingList}', [DocumentController::class, 'pickingListPdf'])->name('documents.picking-list');

    Route::resource('imports', ImportController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('imports/template/{type}', [ImportController::class, 'downloadTemplate'])->name('imports.template');

    Route::middleware(['role:admin'])->prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('company', [SettingsController::class, 'updateCompany'])->name('company.update');
        Route::post('alerts', [SettingsController::class, 'updateAlertSettings'])->name('alerts.update');
        Route::get('users/create', [SettingsController::class, 'createUser'])->name('users.create');
        Route::post('users', [SettingsController::class, 'storeUser'])->name('users.store');
        Route::get('users/{user}/edit', [SettingsController::class, 'editUser'])->name('users.edit');
        Route::put('users/{user}', [SettingsController::class, 'updateUser'])->name('users.update');
        Route::delete('users/{user}', [SettingsController::class, 'destroyUser'])->name('users.destroy');
    });
});
