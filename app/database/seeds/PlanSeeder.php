<?php

class PlanSeeder extends Seeder
{

    public function run()
    {
        /* Plans: Update or create all */
        Plan::updateOrCreate(
            ['name' => 'Contribute'], 
            array(
                'name'              => 'Contribute',
                'interval'          => 'permanent',
                'interval_count'    => 0,
                'amount'            => 0,
                'plan_id'           => null,
                'description'       => 
                    '<ul class="list-group">
                      <li class="list-group-item">You host your software</li>
                      <li class="list-group-item">Access and customize each functionality</li>
                      <li class="list-group-item">Community support</li>
                    </ul>
                    <p><small>Fork us on GitHub, and create your own instance.</small></p>',
            )
        );

        Plan::updateOrCreate(
            ['name' => 'Free'], 
            array(
                'name'              => 'Free',
                'interval'          => 'permanent',
                'interval_count'    => 0,
                'amount'            => 0,
                'plan_id'           => null,
                'description'       => 
                    '<ul class="list-group">
                      <li class="list-group-item">We host the software</li>
                      <li class="list-group-item">Access all free widgets</li>
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
                'plan_id'           => $_ENV['BRAINTREE_PREMIUM_PLAN_ID'],
                'description'       => 
                    '<ul class="list-group">
                      <li class="list-group-item">We host the software</li>
                      <li class="list-group-item">Access all premium widgets</li>
                      <li class="list-group-item">Email support</li>
                    </ul>
                    <p><small>Use the dashboard without any restrictions.</small></p>',
            )
        );

        /* Send message to console */
        error_log('PlanSeeder | All Plans updated');

    }

} /* PlanSeeder */