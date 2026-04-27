<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('slot_from_id')->nullable()->constrained('slots')->nullOnDelete();
            $table->foreignId('slot_to_id')->nullable()->constrained('slots')->nullOnDelete();
            $table->enum('type', ['incoming', 'outgoing', 'transfer', 'adjustment', 'return', 'damaged']);
            $table->integer('quantity');
            $table->string('lot_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('document_number')->nullable();
            $table->enum('document_type', ['ddt', 'order', 'transfer', 'adjustment'])->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->timestamp('created_at')->useCurrent();

            $table->index('company_id');
            $table->index('product_id');
            $table->index('type');
            $table->index('created_at');
            $table->index(['company_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};
