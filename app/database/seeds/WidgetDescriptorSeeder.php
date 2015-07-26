<?php

class WidgetDescriptorSeeder extends Seeder
{

    public function run()
    {
        WidgetDescriptor::create(array(
            'name'        => 'Clock',
            'description' => 'A simple clock',
            'type'        => 'clock',
            'category'    => 'personal',
            'is_premium'  => FALSE,
        ));

        WidgetDescriptor::create(array(
            'name'        => 'Quotes',
            'description' => 'Get inspired every day, by this awesome widget.',
            'type'        => 'quote',
            'category'    => 'personal',
            'is_premium'  => FALSE,
        ));

        WidgetDescriptor::create(array(
            'name'        => 'Greetings',
            'description' => 'Wouldn\'t it be great to receive a greeting message from your favourite browser every time you open a new tab?',
            'type'        => 'greetings',
            'category'    => 'personal',
            'is_premium'  => FALSE,
        ));

        WidgetDescriptor::create(array(
            'name'        => 'Stripe MRR',
            'description' => 'Stripe Monthly recurring revenue',
            'type'        => 'stripe_mrr',
            'category'    => 'financial',
            'is_premium'  => TRUE,
        ));

        WidgetDescriptor::create(array(
            'name'        => 'Iframe',
            'description' => 'Include your favourite sites into this dashboard.',
            'type'        => 'iframe',
            'category'    => 'personal',
            'is_premium'  => FALSE,
        ));

    }

} /* WidgetDescriptorSeeder */


