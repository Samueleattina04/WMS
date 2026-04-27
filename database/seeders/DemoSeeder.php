<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Movement;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Shelf;
use App\Models\Slot;
use App\Models\StockLocation;
use App\Models\Supplier;
use App\Models\UnitOfMeasure;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo company
        $company = Company::create([
            'name' => 'Demo Srl',
            'slug' => 'demo',
            'email' => 'info@demo.it',
            'phone' => '+39 02 1234567',
            'address' => 'Via del Commercio 15, 20100 Milano (MI)',
            'vat_number' => 'IT01234567890',
            'subscription_plan' => 'professional',
            'is_active' => true,
            'settings' => [
                'alert_days_before_expiry' => 30,
                'alert_emails' => ['admin@demo.it'],
            ],
        ]);

        // Create users
        $admin = User::create([
            'company_id' => $company->id,
            'name' => 'Mario Rossi',
            'email' => 'admin@demo.it',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'company_id' => $company->id,
            'name' => 'Luca Verdi',
            'email' => 'manager@demo.it',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'is_active' => true,
        ]);

        User::create([
            'company_id' => $company->id,
            'name' => 'Antonio Bianchi',
            'email' => 'magazziniere@demo.it',
            'password' => Hash::make('password'),
            'role' => 'warehouse',
            'is_active' => true,
        ]);

        // Units of measure
        $pz = UnitOfMeasure::create(['company_id' => $company->id, 'name' => 'Pezzo', 'abbreviation' => 'pz', 'type' => 'quantity']);
        $kg = UnitOfMeasure::create(['company_id' => $company->id, 'name' => 'Chilogrammo', 'abbreviation' => 'kg', 'type' => 'weight']);
        $lt = UnitOfMeasure::create(['company_id' => $company->id, 'name' => 'Litro', 'abbreviation' => 'lt', 'type' => 'volume']);
        $scatola = UnitOfMeasure::create(['company_id' => $company->id, 'name' => 'Scatola', 'abbreviation' => 'sc', 'type' => 'quantity']);

        // Categories
        $catElettro = Category::create(['company_id' => $company->id, 'name' => 'Elettronica', 'description' => 'Componenti e dispositivi elettronici']);
        $catComp = Category::create(['company_id' => $company->id, 'name' => 'Componenti', 'description' => 'Componenti meccanici', 'parent_id' => $catElettro->id]);
        $catAlim = Category::create(['company_id' => $company->id, 'name' => 'Alimentari', 'description' => 'Prodotti alimentari']);
        $catUfficio = Category::create(['company_id' => $company->id, 'name' => 'Ufficio', 'description' => 'Forniture per ufficio']);

        // Suppliers
        $sup1 = Supplier::create([
            'company_id' => $company->id,
            'name' => 'Tech Distribuzione Srl',
            'contact_person' => 'Paolo Neri',
            'email' => 'ordini@techdist.it',
            'phone' => '+39 02 9876543',
            'address' => 'Via Industriale 45, 20060 Cassina de\' Pecchi (MI)',
            'is_active' => true,
        ]);

        $sup2 = Supplier::create([
            'company_id' => $company->id,
            'name' => 'Forniture Generali Spa',
            'contact_person' => 'Maria Conti',
            'email' => 'commerciale@forgen.it',
            'phone' => '+39 011 5554321',
            'address' => 'Corso Vittorio Emanuele 89, 10100 Torino (TO)',
            'is_active' => true,
        ]);

        $sup3 = Supplier::create([
            'company_id' => $company->id,
            'name' => 'AlimExport Italia',
            'contact_person' => 'Giuseppe Ferraro',
            'email' => 'info@alimexport.it',
            'phone' => '+39 081 2223344',
            'address' => 'Via Napoli 120, 80100 Napoli (NA)',
            'is_active' => true,
        ]);

        // Customers
        $cust1 = Customer::create([
            'company_id' => $company->id,
            'name' => 'Cliente Alfa Srl',
            'contact_person' => 'Roberto Sala',
            'email' => 'ordini@clientealfa.it',
            'phone' => '+39 02 1111222',
            'is_active' => true,
        ]);

        $cust2 = Customer::create([
            'company_id' => $company->id,
            'name' => 'Beta Commerciale',
            'contact_person' => 'Francesca Mari',
            'email' => 'acquisti@betacomm.it',
            'phone' => '+39 055 3332211',
            'is_active' => true,
        ]);

        // Warehouse
        $warehouse = Warehouse::create([
            'company_id' => $company->id,
            'name' => 'Magazzino Principale',
            'address' => 'Via del Commercio 15, 20100 Milano',
            'description' => 'Magazzino principale sede legale',
            'is_active' => true,
        ]);

        // Zones
        $zoneA = Zone::create(['company_id' => $company->id, 'warehouse_id' => $warehouse->id, 'name' => 'Zona A - Ricezione', 'code' => 'A', 'type' => 'receiving']);
        $zoneB = Zone::create(['company_id' => $company->id, 'warehouse_id' => $warehouse->id, 'name' => 'Zona B - Stoccaggio', 'code' => 'B', 'type' => 'storage']);
        $zoneC = Zone::create(['company_id' => $company->id, 'warehouse_id' => $warehouse->id, 'name' => 'Zona C - Spedizione', 'code' => 'C', 'type' => 'shipping']);

        // Shelves for Zone B
        $shelfB1 = Shelf::create(['company_id' => $company->id, 'zone_id' => $zoneB->id, 'code' => 'B1', 'name' => 'Scaffale B1']);
        $shelfB2 = Shelf::create(['company_id' => $company->id, 'zone_id' => $zoneB->id, 'code' => 'B2', 'name' => 'Scaffale B2']);
        $shelfB3 = Shelf::create(['company_id' => $company->id, 'zone_id' => $zoneB->id, 'code' => 'B3', 'name' => 'Scaffale B3']);

        // Slots
        $slots = [];
        foreach ([$shelfB1, $shelfB2, $shelfB3] as $shelf) {
            foreach (['01', '02', '03', '04', '05'] as $col) {
                foreach (['A', 'B', 'C'] as $level) {
                    $slots[] = Slot::create([
                        'company_id' => $company->id,
                        'shelf_id' => $shelf->id,
                        'code' => $shelf->code . $col . $level,
                        'level' => $level,
                        'column' => $col,
                        'max_quantity' => 100,
                        'is_active' => true,
                    ]);
                }
            }
        }

        // Zone A slots
        $slotA = Slot::create(['company_id' => $company->id, 'shelf_id' => Shelf::create(['company_id' => $company->id, 'zone_id' => $zoneA->id, 'code' => 'A1', 'name' => 'Area Ricezione'])->id, 'code' => 'A1-01A', 'is_active' => true]);

        // Products
        $products = [
            ['name' => 'Resistenza 10kΩ 1/4W', 'sku' => 'EL-RES-10K', 'barcode' => '8001000001', 'category_id' => $catComp->id, 'supplier_id' => $sup1->id, 'unit_id' => $pz->id, 'cost_price' => 0.05, 'selling_price' => 0.15, 'min_stock_alert' => 500],
            ['name' => 'Condensatore 100μF 25V', 'sku' => 'EL-CAP-100', 'barcode' => '8001000002', 'category_id' => $catComp->id, 'supplier_id' => $sup1->id, 'unit_id' => $pz->id, 'cost_price' => 0.12, 'selling_price' => 0.40, 'min_stock_alert' => 200],
            ['name' => 'Arduino Nano v3', 'sku' => 'EL-ARD-NANO', 'barcode' => '8001000003', 'category_id' => $catElettro->id, 'supplier_id' => $sup1->id, 'unit_id' => $pz->id, 'cost_price' => 3.50, 'selling_price' => 9.90, 'min_stock_alert' => 20],
            ['name' => 'Raspberry Pi 4 Model B 4GB', 'sku' => 'EL-RPI-4B4', 'barcode' => '8001000004', 'category_id' => $catElettro->id, 'supplier_id' => $sup1->id, 'unit_id' => $pz->id, 'cost_price' => 55.00, 'selling_price' => 79.90, 'min_stock_alert' => 5],
            ['name' => 'Carta A4 80g 500 fogli', 'sku' => 'UFF-CARTA-A4', 'barcode' => '8002000001', 'category_id' => $catUfficio->id, 'supplier_id' => $sup2->id, 'unit_id' => $scatola->id, 'cost_price' => 4.50, 'selling_price' => 7.90, 'min_stock_alert' => 10],
            ['name' => 'Penna Biro Blu BIC', 'sku' => 'UFF-PEN-BLU', 'barcode' => '8002000002', 'category_id' => $catUfficio->id, 'supplier_id' => $sup2->id, 'unit_id' => $pz->id, 'cost_price' => 0.18, 'selling_price' => 0.55, 'min_stock_alert' => 50],
            ['name' => 'Pasta Barilla Spaghetti 500g', 'sku' => 'ALI-PASTA-SP5', 'barcode' => '8003000001', 'category_id' => $catAlim->id, 'supplier_id' => $sup3->id, 'unit_id' => $pz->id, 'cost_price' => 0.85, 'selling_price' => 1.29, 'min_stock_alert' => 100, 'has_expiry' => true, 'has_lot_tracking' => true],
            ['name' => 'Olio Extra Vergine 1L', 'sku' => 'ALI-OLIO-EV1', 'barcode' => '8003000002', 'category_id' => $catAlim->id, 'supplier_id' => $sup3->id, 'unit_id' => $lt->id, 'cost_price' => 4.20, 'selling_price' => 7.50, 'min_stock_alert' => 30, 'has_expiry' => true, 'has_lot_tracking' => true],
        ];

        $createdProducts = [];
        foreach ($products as $data) {
            $createdProducts[] = Product::create(array_merge([
                'company_id' => $company->id,
                'is_active' => true,
                'has_expiry' => false,
                'has_lot_tracking' => false,
            ], $data));
        }

        // Add stock to slots
        $stockData = [
            [$createdProducts[0], $slots[0], 2000, null, null],
            [$createdProducts[0], $slots[1], 1500, null, null],
            [$createdProducts[1], $slots[3], 500, null, null],
            [$createdProducts[2], $slots[6], 45, null, null],
            [$createdProducts[3], $slots[9], 12, null, null],
            [$createdProducts[4], $slots[12], 25, null, null],
            [$createdProducts[5], $slots[15], 200, null, null],
            [$createdProducts[6], $slots[18], 300, 'LOT-2024-001', now()->addMonths(18)->toDateString()],
            [$createdProducts[6], $slots[19], 150, 'LOT-2024-002', now()->addMonths(6)->toDateString()],
            [$createdProducts[7], $slots[20], 80, 'LOT-2024-A', now()->addYear()->toDateString()],
        ];

        foreach ($stockData as [$product, $slot, $qty, $lot, $expiry]) {
            StockLocation::create([
                'company_id' => $company->id,
                'product_id' => $product->id,
                'slot_id' => $slot->id,
                'quantity' => $qty,
                'reserved_quantity' => 0,
                'lot_number' => $lot,
                'expiry_date' => $expiry,
            ]);
            $slot->update(['current_quantity' => $slot->current_quantity + $qty, 'is_occupied' => true]);

            // Record movement
            Movement::create([
                'company_id' => $company->id,
                'product_id' => $product->id,
                'slot_to_id' => $slot->id,
                'type' => 'incoming',
                'quantity' => $qty,
                'lot_number' => $lot,
                'expiry_date' => $expiry,
                'document_type' => 'ddt',
                'document_number' => 'DDT-2024-' . rand(100, 999),
                'notes' => 'Carico iniziale inventario',
                'created_by_user_id' => $admin->id,
                'created_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        // Create a purchase order
        $po = PurchaseOrder::create([
            'company_id' => $company->id,
            'supplier_id' => $sup1->id,
            'order_number' => 'OA-2024-001',
            'status' => 'pending',
            'expected_date' => now()->addWeek(),
            'notes' => 'Ordine di riassortimento Q4',
            'created_by_user_id' => $admin->id,
        ]);

        PurchaseOrderItem::create(['purchase_order_id' => $po->id, 'product_id' => $createdProducts[0]->id, 'quantity_ordered' => 5000, 'unit_price' => 0.05]);
        PurchaseOrderItem::create(['purchase_order_id' => $po->id, 'product_id' => $createdProducts[2]->id, 'quantity_ordered' => 20, 'unit_price' => 3.50]);

        $this->command->info('✓ Demo data seeded successfully!');
        $this->command->info('  Company: demo.gestionale.it (or localhost)');
        $this->command->info('  Admin: admin@demo.it / password');
        $this->command->info('  Manager: manager@demo.it / password');
        $this->command->info('  Warehouse: magazziniere@demo.it / password');
    }
}
