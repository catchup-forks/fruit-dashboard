<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddedSubscriptionIdToStripeSubscriptions extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('stripe_subscriptions',function($table) {
            $table->string('subscription_id', 32);
        });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('stripe_subscriptions',function($table) {
            $table->dropColumn('subscription_id');
        });
    }

}
