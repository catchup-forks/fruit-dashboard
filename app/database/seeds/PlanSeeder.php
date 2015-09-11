<?php

class PlanSeeder extends Seeder
{

    public function run()
    {
        Plan::updateOrCreate(
            ['name' => 'Free'], 
            array(
                'name'              => 'Free',
                'interval'          => 'permanent',
                'interval_count'    => 0,
                'amount'            => 0,
                'braintree_plan_id' => null,
                'description'       => 
                    '<ul class="list-group">
                      <li class="list-group-item">30 days of historical data</li>
                      <li class="list-group-item">Community support</li>
                    </ul>
                    <p><small>Just create an account, and use the free functionalities without any install.</small></p>',
            )
        );

        Plan::updateOrCreate(
            ['name' => 'Premium'], 
            array(
                'name'              => 'Premium',
                'interval'          => 'month',
                'interval_count'    => 12,
                'amount'            => 9,
                'braintree_plan_id' => $_ENV['BRAINTREE_PREMIUM_PLAN_ID'],
                'description'       => 
                    '<ul class="list-group">
                      <li class="list-group-item">Unlimited historical data</li>
                      <li class="list-group-item">Email support</li>
                    </ul>
                    <p><small>Use the dashboard without any restrictions.</small></p>',
            )
        );

        /* Send message to console */
        Log::info('PlanSeeder | All Plans updated');

    }

} /* PlanSeeder */