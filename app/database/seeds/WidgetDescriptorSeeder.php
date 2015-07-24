<?php

class WidgetDescriptorSeeder extends Seeder
{

    public function run()
    {
        WidgetDescriptor::create(array(
            'name'        => 'Clock widget',
            'description' => 'A simple clock',
            'type'        => 'clock',
            'is_premium'  => FALSE,
        ));

        WidgetDescriptor::create(array(
            'name'        => 'Quotes',
            'description' => 'Get inspired every day, by this awesome widget.',
            'type'        => 'quote',
            'is_premium'  => FALSE,
        ));

        WidgetDescriptor::create(array(
            'name'        => 'Greetings',
            'description' => 'Wouldn\'t it be great to receive a greeting message from your favourite browser every time you open a new tab?',
            'type'        => 'greetings',
            'is_premium'  => FALSE,
        ));

        WidgetDescriptor::create(array(
            'name'        => 'Stripe MRR',
            'description' => 'Stripe Monthly recurring revenue',
            'type'        => 'stripe_mrr',
            'is_premium'  => TRUE,
        ));


    }

} /* WidgetDescriptorSeeder */


