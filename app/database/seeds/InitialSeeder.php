<?php

class InitialSeeder extends Seeder
{

    public function run()
    {
        WidgetDescriptor::create(array(
            'id'          => 1,
            'name'        => 'Clock widget',
            'description' => 'A simple clock',
            'type'        => 'clock',
            'is_premium'  => FALSE
        ));
        WidgetDescriptor::create(array(
            'name'        => 'Inspirational quotes',
            'description' => 'Get inspired every day, by this awesome widget.',
            'type'        => 'inspirational_quotes',
            'is_premium'  => FALSE
        ));
        WidgetDescriptor::create(array(
            'name'        => 'Greetings',
            'description' => 'Wouldn\'t it be great to receive a greeting message from your favourite browser every time you open a new tab?.',
            'type'        => 'greetings',
            'is_premium'  => FALSE
        ));
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
