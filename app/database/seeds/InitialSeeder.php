<?php

class InitialSeeder extends Seeder
{

    public function run()
    {

        /* Apply seeder only if no Dashboard exists (database reset) */
        if (!Dashboard::all()->count()) {
            Dashboard::create(array(
                'id'         => '1',
                'user_id'    => '1',
                'name'       => 'First personal dashboard',
                'background' => TRUE,
            ));
            
            ClockWidget::create(array(
                'id'            => '1',
                'dashboard_id'  => '1',
                'descriptor_id' => '1',
                'state'         => 'active',
                'position'      => '{"row":1,"col":3,"size_x":8,"size_y":3}',
            ));

            GreetingsWidget::create(array(
                'id'            => '2',
                'dashboard_id'  => '1',
                'descriptor_id' => '1',
                'state'         => 'active',
                'position'      => '{"row":4,"col":3,"size_x":8,"size_y":1}',
            ));

            /* Send message to console */
            error_log('InitialSeeder | Successfully seeded');

        } else {
            /* Send message to console */
            error_log('InitialSeeder | No modifications were made, because the DB is not empty');
        }
    }

} /* InitialSeeder */
