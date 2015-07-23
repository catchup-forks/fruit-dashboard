<?php

class WidgetDescriptorSeeder extends Seeder
{

    public function run()
    {
        WidgetDescriptor::create(array(
            'id'          => Config::get('constants.WD_ID_CLOCK'),
            'name'        => 'Clock widget',
            'description' => 'A simple clock',
            'type'        => 'clock',
            'is_premium'  => FALSE
        ));

        WidgetDescriptor::create(array(
            'id'          => Config::get('constants.WD_ID_QUOTES'),
            'name'        => 'Quotes',
            'description' => 'Get inspired every day, by this awesome widget.',
            'type'        => 'quote',
            'is_premium'  => FALSE
        ));
        
        WidgetDescriptor::create(array(
            'id'          => Config::get('constants.WD_ID_GREETINGS'),
            'name'        => 'Greetings',
            'description' => 'Wouldn\'t it be great to receive a greeting message from your favourite browser every time you open a new tab?',
            'type'        => 'greeting',
            'is_premium'  => FALSE
        ));
    }

} /* WidgetDescriptorSeeder */


