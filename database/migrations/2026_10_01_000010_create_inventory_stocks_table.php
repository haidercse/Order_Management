<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fruit_id')->constrained('fruits')->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->foreignId('box_configuration_id')
                ->nullable()
                ->constrained('fruit_box_configurations')
                ->nullOnDelete();

            // Native stock quantity, e.g. 30 CAJA.
            $table->decimal('quantity', 14, 3)->default(0);
            $table->decimal('reserved_quantity', 14, 3)->default(0);

            // KG equivalent is kept for cross-unit shortage calculations.
            $table->decimal('quantity_kg', 14, 3)->default(0);
            $table->decimal('reserved_kg', 14, 3)->default(0);

            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->unique(['fruit_id', 'unit_id', 'box_configuration_id'], 'inventory_stock_unique');
            $table->index(['fruit_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_stocks');
    }
};
