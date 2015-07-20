<?php

class InitialSeeder extends Seeder
{

    public function run()
    {
        WidgetDescriptor::create(array(
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
    }

}
