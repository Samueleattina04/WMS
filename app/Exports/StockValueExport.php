<?php

namespace App\Exports;

use App\Models\Company;
use App\Models\StockLocation;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockValueExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private Company $company) {}

    public function query()
    {
        return StockLocation::where('stock_locations.company_id', $this->company->id)
            ->join('products', 'stock_locations.product_id', '=', 'products.id')
            ->join('slots', 'stock_locations.slot_id', '=', 'slots.id')
            ->join('shelves', 'slots.shelf_id', '=', 'shelves.id')
            ->join('zones', 'shelves.zone_id', '=', 'zones.id')
            ->join('warehouses', 'zones.warehouse_id', '=', 'warehouses.id')
            ->select([
                'products.name as product_name',
                'products.sku',
                'warehouses.name as warehouse_name',
                'zones.name as zone_name',
                'shelves.code as shelf_code',
                'slots.code as slot_code',
                'stock_locations.lot_number',
                'stock_locations.expiry_date',
                'stock_locations.quantity',
                'products.cost_price',
                DB::raw('stock_locations.quantity * products.cost_price as total_value'),
            ])
            ->orderBy('products.name');
    }

    public function headings(): array
    {
        return [
            'Prodotto', 'SKU', 'Magazzino', 'Zona', 'Scaffale', 'Slot',
            'Lotto', 'Scadenza', 'Quantità', 'Costo Unitario', 'Valore Totale',
        ];
    }

    public function map($row): array
    {
        return [
            $row->product_name,
            $row->sku ?? '',
            $row->warehouse_name,
            $row->zone_name,
            $row->shelf_code,
            $row->slot_code,
            $row->lot_number ?? '',
            $row->expiry_date ? \Carbon\Carbon::parse($row->expiry_date)->format('d/m/Y') : '',
            $row->quantity,
            number_format($row->cost_price, 2, ',', '.'),
            number_format($row->total_value, 2, ',', '.'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
