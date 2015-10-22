<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ActivateTrialPeriod extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (User::all() as $user) {
            if ($user->subscription->trial_status == 'possible') {
                $user->subscription->trial_status ='active';
                $user->subscription->trial_start = Carbon::now();
                $user->subscription->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (User::all() as $user) {
            if ($user->subscription->trial_status == 'active') {
                $user->subscription->trial_status = 'possible';
                $user->subscription->trial_start = Carbon::now();
                $user->subscription->save();
            }
        }
    }

}
