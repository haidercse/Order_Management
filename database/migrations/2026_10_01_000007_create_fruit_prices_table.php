<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fruit_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fruit_id')->constrained('fruits')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->foreignId('box_configuration_id')
                ->nullable()
                ->constrained('fruit_box_configurations')
                ->nullOnDelete();

            $table->decimal('price', 12, 2);
            $table->string('currency', 3)->default('EUR');
            $table->date('effective_from')->index();
            $table->date('effective_to')->nullable()->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['fruit_id', 'unit_id', 'is_active']);
            $table->index(['fruit_id', 'effective_from']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fruit_prices');
    }
};
