<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('fruit_id')->constrained('fruits')->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->foreignId('box_configuration_id')
                ->nullable()
                ->constrained('fruit_box_configurations')
                ->nullOnDelete();

            // Snapshot values: future master-data/price changes must not alter old orders.
            $table->decimal('quantity', 12, 3);
            $table->decimal('unit_weight_kg', 10, 3)->nullable();
            $table->decimal('converted_kg', 12, 3)->nullable();
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->decimal('line_total', 14, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'fruit_id']);
            $table->index(['fruit_id', 'unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
