<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->foreign('group_id')
                ->references('id')
                ->on('permission_groups')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
            $table->foreign('group_id')
                ->references('id')
                ->on('permission_groups');
        });
    }
};
