<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fruit_box_configurations', function (Blueprint $table) {
            $table->decimal('tare_weight_kg', 10, 3)->default(0)->after('weight_kg');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('unit_tare_kg', 10, 3)->nullable()->after('unit_weight_kg');
        });

        $tareByBox = DB::table('fruit_box_configurations')->pluck('tare_weight_kg', 'id');
        DB::table('order_items')
            ->whereNotNull('box_configuration_id')
            ->orderBy('id')
            ->chunkById(500, function ($items) use ($tareByBox): void {
                foreach ($items as $item) {
                    DB::table('order_items')
                        ->where('id', $item->id)
                        ->update(['unit_tare_kg' => $tareByBox[$item->box_configuration_id] ?? 0]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('unit_tare_kg');
        });

        Schema::table('fruit_box_configurations', function (Blueprint $table) {
            $table->dropColumn('tare_weight_kg');
        });
    }
};
