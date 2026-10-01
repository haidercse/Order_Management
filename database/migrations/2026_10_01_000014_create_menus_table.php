<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('menu_groups')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('menus')->nullOnDelete();
            $table->string('title');
            $table->string('route')->nullable();
            $table->string('icon')->nullable();
            $table->string('permission')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index(['group_id', 'order']);
            $table->index(['parent_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
