<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fruits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('code', 80)->unique();
            $table->string('name');
            $table->string('display_name')->nullable();
            $table->foreignId('default_unit_id')->constrained('units')->restrictOnDelete();
            $table->boolean('allow_kg')->default(true);
            $table->boolean('allow_box')->default(true);
            $table->boolean('status')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['category_id', 'status']);
            $table->index(['name', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fruits');
    }
};
