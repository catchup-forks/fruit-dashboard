<?php

class InitialSeeder extends Seeder
{

    public function run()
    {
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
            'position'      => '{"col":3,"row":1,"size_x":3,"size_y":1}',
        ));
    }

}
