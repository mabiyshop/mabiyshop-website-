<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMetaCustomerMatchingToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('client_user_agent')->nullable()->after('ip_address');
            $table->string('fbp', 255)->nullable()->after('client_user_agent');
            $table->string('fbc', 255)->nullable()->after('fbp');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['client_user_agent', 'fbp', 'fbc']);
        });
    }
}
