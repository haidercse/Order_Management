<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fruit_box_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fruit_id')->constrained('fruits')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 50);
            $table->decimal('weight_kg', 10, 3);
            $table->boolean('is_default')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->unique(['fruit_id', 'code']);
            $table->index(['fruit_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fruit_box_configurations');
    }
};
