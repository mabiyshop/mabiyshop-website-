<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDeliveryAreaAndThreeTierShippingSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('delivery_area', 32)->nullable()->after('shipping_union');
        });

        DB::table('settings')->insertOrIgnore([
            'key' => 'default_shipping_subarea_origin',
            'value' => 80,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('settings')
            ->where('key', 'default_shipping_inside_origin')
            ->where('value', '50')
            ->update([
                'value' => 60,
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('settings')
            ->where('key', 'default_shipping_inside_origin')
            ->where('value', '60')
            ->update([
                'value' => 50,
                'updated_at' => now(),
            ]);

        DB::table('settings')
            ->where('key', 'default_shipping_subarea_origin')
            ->where('value', '80')
            ->where('is_active', 1)
            ->delete();

        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('delivery_area');
        });
    }
}
