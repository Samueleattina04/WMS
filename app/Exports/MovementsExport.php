<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MovementsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private Collection $movements) {}

    public function collection(): Collection
    {
        return $this->movements;
    }

    public function headings(): array
    {
        return [
            'ID', 'Prodotto', 'SKU', 'Tipo', 'Quantità',
            'Da Slot', 'A Slot', 'Lotto', 'Scadenza',
            'N. Documento', 'Tipo Documento', 'Note',
            'Operatore', 'Data/Ora',
        ];
    }

    public function map($movement): array
    {
        return [
            $movement->id,
            $movement->product->name ?? '',
            $movement->product->sku ?? '',
            $movement->type_label,
            $movement->quantity,
            $movement->slotFrom?->full_code ?? '',
            $movement->slotTo?->full_code ?? '',
            $movement->lot_number ?? '',
            $movement->expiry_date?->format('d/m/Y') ?? '',
            $movement->document_number ?? '',
            $movement->document_type_label ?? '',
            $movement->notes ?? '',
            $movement->createdBy?->name ?? '',
            $movement->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
