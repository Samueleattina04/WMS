<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Company;
use App\Models\Import;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\UnitOfMeasure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class ProductsImport implements ToCollection, WithHeadingRow
{
    private int $rowCount = 0;
    private int $importedCount = 0;
    private int $failedCount = 0;
    private array $errors = [];

    public function __construct(
        private Company $company,
        private Import $importRecord
    ) {}

    public function collection(Collection $rows): void
    {
        $this->rowCount = $rows->count();

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // +2 for header row and 1-indexing

            try {
                $name = trim($row['nome'] ?? $row['name'] ?? '');
                if (empty($name)) {
                    throw new \Exception('Nome prodotto mancante');
                }

                $categoryId = null;
                if (! empty($row['categoria'])) {
                    $cat = Category::where('company_id', $this->company->id)
                        ->where('name', trim($row['categoria']))
                        ->first();
                    $categoryId = $cat?->id;
                }

                $supplierId = null;
                if (! empty($row['fornitore'])) {
                    $sup = Supplier::where('company_id', $this->company->id)
                        ->where('name', trim($row['fornitore']))
                        ->first();
                    $supplierId = $sup?->id;
                }

                $unitId = null;
                if (! empty($row['unita_misura'] ?? $row['unit'])) {
                    $unit = UnitOfMeasure::where('company_id', $this->company->id)
                        ->where('abbreviation', trim($row['unita_misura'] ?? $row['unit']))
                        ->first();
                    $unitId = $unit?->id;
                }

                $sku = trim($row['sku'] ?? '');
                if ($sku) {
                    $existing = Product::where('company_id', $this->company->id)->where('sku', $sku)->first();
                    if ($existing) {
                        $existing->update([
                            'name' => $name,
                            'category_id' => $categoryId,
                            'supplier_id' => $supplierId,
                            'unit_id' => $unitId,
                            'barcode' => $row['barcode'] ?? $existing->barcode,
                            'cost_price' => $this->parseDecimal($row['prezzo_costo'] ?? $row['cost_price'] ?? 0),
                            'selling_price' => $this->parseDecimal($row['prezzo_vendita'] ?? $row['selling_price'] ?? 0),
                            'min_stock_alert' => (int) ($row['soglia_minima'] ?? $row['min_stock'] ?? 0),
                            'description' => $row['descrizione'] ?? $row['description'] ?? null,
                        ]);
                    } else {
                        Product::create([
                            'company_id' => $this->company->id,
                            'name' => $name,
                            'sku' => $sku ?: null,
                            'barcode' => trim($row['barcode'] ?? '') ?: null,
                            'category_id' => $categoryId,
                            'supplier_id' => $supplierId,
                            'unit_id' => $unitId,
                            'cost_price' => $this->parseDecimal($row['prezzo_costo'] ?? $row['cost_price'] ?? 0),
                            'selling_price' => $this->parseDecimal($row['prezzo_vendita'] ?? $row['selling_price'] ?? 0),
                            'min_stock_alert' => (int) ($row['soglia_minima'] ?? $row['min_stock'] ?? 0),
                            'description' => $row['descrizione'] ?? $row['description'] ?? null,
                            'is_active' => true,
                        ]);
                    }
                } else {
                    Product::create([
                        'company_id' => $this->company->id,
                        'name' => $name,
                        'barcode' => trim($row['barcode'] ?? '') ?: null,
                        'category_id' => $categoryId,
                        'supplier_id' => $supplierId,
                        'unit_id' => $unitId,
                        'cost_price' => $this->parseDecimal($row['prezzo_costo'] ?? $row['cost_price'] ?? 0),
                        'selling_price' => $this->parseDecimal($row['prezzo_vendita'] ?? $row['selling_price'] ?? 0),
                        'min_stock_alert' => (int) ($row['soglia_minima'] ?? $row['min_stock'] ?? 0),
                        'description' => $row['descrizione'] ?? $row['description'] ?? null,
                        'is_active' => true,
                    ]);
                }

                $this->importedCount++;
            } catch (\Exception $e) {
                $this->failedCount++;
                $this->errors[] = ['row' => $rowNum, 'error' => $e->getMessage()];
            }
        }
    }

    private function parseDecimal($value): float
    {
        $value = str_replace(',', '.', (string) $value);
        return (float) $value;
    }

    public function getRowCount(): int { return $this->rowCount; }
    public function getImportedCount(): int { return $this->importedCount; }
    public function getFailedCount(): int { return $this->failedCount; }
    public function getErrors(): array { return $this->errors; }
}
