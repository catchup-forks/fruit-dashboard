<?php

class PlanSeeder extends Seeder
{

    public function run()
    {
        Plan::create(array(
            'id'                => Config::get('constants.PLAN_ID_CONTRIBUTE'),
            'name'              => 'Contribute',
            'interval'          => 'permanent',
            'interval_count'    => 0,
            'amount'            => 0,
        ));
        Plan::create(array(
            'id'                => Config::get('constants.PLAN_ID_FREE'),
            'name'              => 'Free',
            'interval'          => 'permanent',
            'interval_count'    => 0,
            'amount'            => 0,
        ));
        Plan::create(array(
            'id'                => Config::get('constants.PLAN_ID_PREMIUM'),
            'name'              => 'Premium',
            'interval'          => 'month',
            'interval_count'    => 12,
            'amount'            => 9,
        ));
    }

} /* PlanSeeder */


