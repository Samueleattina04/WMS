<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Import;
use App\Models\InventorySession;
use App\Models\Movement;
use App\Models\PickingList;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\Shelf;
use App\Models\Slot;
use App\Models\Supplier;
use App\Models\UnitOfMeasure;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Zone;
use App\Policies\CategoryPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\ImportPolicy;
use App\Policies\InventorySessionPolicy;
use App\Policies\MovementPolicy;
use App\Policies\PickingListPolicy;
use App\Policies\ProductPolicy;
use App\Policies\PurchaseOrderPolicy;
use App\Policies\SalesOrderPolicy;
use App\Policies\ShelfPolicy;
use App\Policies\SlotPolicy;
use App\Policies\SupplierPolicy;
use App\Policies\UnitOfMeasurePolicy;
use App\Policies\UserPolicy;
use App\Policies\WarehousePolicy;
use App\Policies\ZonePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Customer::class, CustomerPolicy::class);
        Gate::policy(Import::class, ImportPolicy::class);
        Gate::policy(InventorySession::class, InventorySessionPolicy::class);
        Gate::policy(Movement::class, MovementPolicy::class);
        Gate::policy(PickingList::class, PickingListPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(PurchaseOrder::class, PurchaseOrderPolicy::class);
        Gate::policy(SalesOrder::class, SalesOrderPolicy::class);
        Gate::policy(Shelf::class, ShelfPolicy::class);
        Gate::policy(Slot::class, SlotPolicy::class);
        Gate::policy(Supplier::class, SupplierPolicy::class);
        Gate::policy(UnitOfMeasure::class, UnitOfMeasurePolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Warehouse::class, WarehousePolicy::class);
        Gate::policy(Zone::class, ZonePolicy::class);
    }
}
