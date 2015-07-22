<?php

class PlanTableSeeder extends Seeder
{

    public function run()
    {
        Plan::create(array(
            'name' => 'Contribute',
            'interval' => 'permanent',
            'interval_count' => 0,
            'amount' => 0,
        ));
        Plan::create(array(
            'name' => 'Free',
            'interval' => 'permanent',
            'interval_count' => 0,
            'amount' => 0,
        ));
        Plan::create(array(
            'name' => 'Premium',
            'interval' => 'month',
            'interval_count' => 12,
            'amount' => 9,
        ));
    }

}


