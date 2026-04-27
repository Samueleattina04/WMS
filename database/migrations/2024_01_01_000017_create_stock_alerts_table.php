<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->enum('alert_type', ['low_stock', 'expiry', 'out_of_stock']);
            $table->integer('current_quantity')->nullable();
            $table->integer('threshold_quantity')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('company_id');
            $table->index('product_id');
            $table->index('is_resolved');
            $table->index('alert_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_alerts');
    }
};
