<?php

namespace App\Services;

use App\Models\Movement;
use App\Models\Product;
use App\Models\Slot;
use App\Models\StockLocation;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function addStock(
        Product $product,
        Slot $slot,
        int $quantity,
        ?string $lotNumber = null,
        ?\DateTime $expiryDate = null,
        array $movementData = []
    ): StockLocation {
        return DB::transaction(function () use ($product, $slot, $quantity, $lotNumber, $expiryDate, $movementData) {
            $stockLocation = StockLocation::firstOrCreate(
                [
                    'company_id' => $product->company_id,
                    'product_id' => $product->id,
                    'slot_id' => $slot->id,
                    'lot_number' => $lotNumber,
                    'expiry_date' => $expiryDate ? $expiryDate->format('Y-m-d') : null,
                ],
                ['quantity' => 0, 'reserved_quantity' => 0]
            );

            $stockLocation->increment('quantity', $quantity);

            $slot->increment('current_quantity', $quantity);
            $slot->update(['is_occupied' => $slot->fresh()->current_quantity > 0]);

            $this->recordMovement(array_merge([
                'company_id' => $product->company_id,
                'product_id' => $product->id,
                'slot_to_id' => $slot->id,
                'type' => 'incoming',
                'quantity' => $quantity,
                'lot_number' => $lotNumber,
                'expiry_date' => $expiryDate,
            ], $movementData));

            return $stockLocation->fresh();
        });
    }

    public function removeStock(
        Product $product,
        Slot $slot,
        int $quantity,
        string $type = 'outgoing',
        ?string $lotNumber = null,
        array $movementData = []
    ): void {
        DB::transaction(function () use ($product, $slot, $quantity, $type, $lotNumber, $movementData) {
            $query = StockLocation::where('company_id', $product->company_id)
                ->where('product_id', $product->id)
                ->where('slot_id', $slot->id);

            if ($lotNumber) {
                $query->where('lot_number', $lotNumber);
            }

            $stockLocation = $query->first();

            if (! $stockLocation || $stockLocation->quantity < $quantity) {
                throw new \Exception("Quantità insufficiente in questo slot (disponibile: {$stockLocation?->quantity}).");
            }

            $stockLocation->decrement('quantity', $quantity);

            if ($stockLocation->fresh()->quantity === 0) {
                $stockLocation->delete();
            }

            $slot->decrement('current_quantity', $quantity);
            $slot->update(['is_occupied' => $slot->fresh()->current_quantity > 0]);

            $this->recordMovement(array_merge([
                'company_id' => $product->company_id,
                'product_id' => $product->id,
                'slot_from_id' => $slot->id,
                'type' => $type,
                'quantity' => $quantity,
                'lot_number' => $lotNumber,
            ], $movementData));
        });
    }

    public function transferStock(
        Product $product,
        Slot $fromSlot,
        Slot $toSlot,
        int $quantity,
        ?string $lotNumber = null,
        array $movementData = []
    ): void {
        DB::transaction(function () use ($product, $fromSlot, $toSlot, $quantity, $lotNumber, $movementData) {
            $query = StockLocation::where('company_id', $product->company_id)
                ->where('product_id', $product->id)
                ->where('slot_id', $fromSlot->id);

            if ($lotNumber) {
                $query->where('lot_number', $lotNumber);
            }

            $fromStock = $query->first();

            if (! $fromStock || $fromStock->quantity < $quantity) {
                throw new \Exception("Quantità insufficiente per il trasferimento.");
            }

            $fromStock->decrement('quantity', $quantity);
            if ($fromStock->fresh()->quantity === 0) {
                $fromStock->delete();
            }

            $fromSlot->decrement('current_quantity', $quantity);
            $fromSlot->update(['is_occupied' => $fromSlot->fresh()->current_quantity > 0]);

            $toStock = StockLocation::firstOrCreate(
                [
                    'company_id' => $product->company_id,
                    'product_id' => $product->id,
                    'slot_id' => $toSlot->id,
                    'lot_number' => $lotNumber,
                ],
                ['quantity' => 0, 'reserved_quantity' => 0]
            );

            $toStock->increment('quantity', $quantity);
            $toSlot->increment('current_quantity', $quantity);
            $toSlot->update(['is_occupied' => true]);

            $this->recordMovement(array_merge([
                'company_id' => $product->company_id,
                'product_id' => $product->id,
                'slot_from_id' => $fromSlot->id,
                'slot_to_id' => $toSlot->id,
                'type' => 'transfer',
                'quantity' => $quantity,
                'lot_number' => $lotNumber,
            ], $movementData));
        });
    }

    public function adjustStock(
        Product $product,
        Slot $slot,
        int $newQuantity,
        array $movementData = []
    ): void {
        DB::transaction(function () use ($product, $slot, $newQuantity, $movementData) {
            $stockLocation = StockLocation::where('company_id', $product->company_id)
                ->where('product_id', $product->id)
                ->where('slot_id', $slot->id)
                ->first();

            $currentQty = $stockLocation?->quantity ?? 0;
            $difference = $newQuantity - $currentQty;

            if ($difference === 0) {
                return;
            }

            if ($stockLocation) {
                $stockLocation->update(['quantity' => $newQuantity]);
            } else {
                StockLocation::create([
                    'company_id' => $product->company_id,
                    'product_id' => $product->id,
                    'slot_id' => $slot->id,
                    'quantity' => $newQuantity,
                    'reserved_quantity' => 0,
                ]);
            }

            $slot->update([
                'current_quantity' => DB::raw('current_quantity + ' . $difference),
                'is_occupied' => $slot->fresh()->current_quantity + $difference > 0,
            ]);

            $this->recordMovement(array_merge([
                'company_id' => $product->company_id,
                'product_id' => $product->id,
                'slot_from_id' => $difference < 0 ? $slot->id : null,
                'slot_to_id' => $difference > 0 ? $slot->id : null,
                'type' => 'adjustment',
                'quantity' => abs($difference),
            ], $movementData));
        });
    }

    public function getSuggestedSlotsFifo(Product $product, int $quantity): array
    {
        return StockLocation::where('company_id', $product->company_id)
            ->where('product_id', $product->id)
            ->where('quantity', '>', 0)
            ->fifo()
            ->with('slot.shelf.zone.warehouse')
            ->get()
            ->map(fn ($sl) => [
                'stock_location' => $sl,
                'slot' => $sl->slot,
                'available' => $sl->quantity - $sl->reserved_quantity,
                'lot_number' => $sl->lot_number,
                'expiry_date' => $sl->expiry_date,
            ])
            ->filter(fn ($item) => $item['available'] > 0)
            ->values()
            ->all();
    }

    private function recordMovement(array $data): void
    {
        Movement::create(array_merge(['created_at' => now()], $data));
    }
}
