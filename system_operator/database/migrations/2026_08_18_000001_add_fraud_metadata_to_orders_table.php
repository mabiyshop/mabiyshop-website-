<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFraudMetadataToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('risk_level')->nullable()->after('status');
            $table->text('risk_reasons')->nullable()->after('risk_level');
            $table->boolean('otp_required')->default(false)->after('risk_reasons');
            $table->boolean('manual_review')->default(false)->after('otp_required');
            $table->string('fraud_status')->default('NORMAL')->after('manual_review');
            $table->text('fraud_reason')->nullable()->after('fraud_status');
            $table->unsignedBigInteger('fraud_action_by')->nullable()->after('fraud_reason');
            $table->timestamp('fraud_action_at')->nullable()->after('fraud_action_by');
            $table->float('courier_success_rate')->nullable()->after('fraud_action_at');
            $table->text('courier_history_snapshot')->nullable()->after('courier_success_rate');
            $table->text('risk_decision_snapshot')->nullable()->after('courier_history_snapshot');
            $table->text('manual_fraud_state')->nullable()->after('risk_decision_snapshot');

            $table->index('fraud_status');
            $table->index('manual_review');
            $table->index('risk_level');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['fraud_status']);
            $table->dropIndex(['manual_review']);
            $table->dropIndex(['risk_level']);
            $table->dropColumn([
                'risk_level',
                'risk_reasons',
                'otp_required',
                'manual_review',
                'fraud_status',
                'fraud_reason',
                'fraud_action_by',
                'fraud_action_at',
                'courier_success_rate',
                'courier_history_snapshot',
                'risk_decision_snapshot',
                'manual_fraud_state',
            ]);
        });
    }
}
