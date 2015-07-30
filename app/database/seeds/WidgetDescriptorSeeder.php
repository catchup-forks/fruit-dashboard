<?php

class WidgetDescriptorSeeder extends Seeder
{

    public function run()
    {

        /* WidgetDescriptor: Update or create all */
        /* Personal widgets */
        WidgetDescriptor::updateOrCreate(
            ['name' => 'Clock'],
            array(
                'name'        => 'Clock',
                'description' => 'A simple clock',
                'type'        => 'clock',
                'category'    => 'personal',
                'is_premium'  => FALSE,
            )
        );

        WidgetDescriptor::updateOrCreate(
            ['name' => 'Quotes'],
            array(
                'name'        => 'Quotes',
                'description' => 'Get inspired every day, by this awesome widget.',
                'type'        => 'quote',
                'category'    => 'personal',
                'is_premium'  => FALSE,
            )
        );

        WidgetDescriptor::updateOrCreate(
            ['name' => 'Greetings'],
            array(
                'name'        => 'Greetings',
                'description' => 'Wouldn\'t it be great to receive a greeting message from your favourite browser every time you open a new tab?',
                'type'        => 'greetings',
                'category'    => 'personal',
                'is_premium'  => FALSE,
            )
        );

        WidgetDescriptor::updateOrCreate(
            ['name' => 'Reminder'],
            array(
                'name'        => 'Reminder',
                'description' => '',
                'type'        => 'reminder',
                'category'    => 'personal',
                'is_premium'  => FALSE,
            )
        );

        WidgetDescriptor::updateOrCreate(
            ['name' => 'Iframe'],
            array(
                'name'        => 'Iframe',
                'description' => 'Include your favourite sites into this dashboard.',
                'type'        => 'iframe',
                'category'    => 'personal',
                'is_premium'  => FALSE,
            )
        );

        WidgetDescriptor::updateOrCreate(
            ['name' => 'Note'],
            array(
                'name'        => 'Note',
                'description' => '',
                'type'        => 'note',
                'category'    => 'personal',
                'is_premium'  => FALSE,
            )
        );

        /* Financial widgets | STRIPE */
        WidgetDescriptor::updateOrCreate(
            ['name' => 'Stripe MRR'],
            array(
                'name'        => 'Stripe MRR',
                'description' => 'Stripe Monthly recurring revenue',
                'type'        => 'stripe_mrr',
                'category'    => 'financial',
                'is_premium'  => TRUE,
            )
        );

        WidgetDescriptor::updateOrCreate(
            ['name' => 'Stripe ARR'],
            array(
                'name'        => 'Stripe ARR',
                'description' => 'Stripe Annual recurring revenue',
                'type'        => 'stripe_arr',
                'category'    => 'financial',
                'is_premium'  => TRUE,
            )
        );

        WidgetDescriptor::updateOrCreate(
            ['name' => 'Stripe ARPU'],
            array(
                'name'        => 'Stripe ARPU',
                'description' => 'Stripe Average revenue per user',
                'type'        => 'stripe_arpu',
                'category'    => 'financial',
                'is_premium'  => TRUE,
            )
        );

        /* Financial widgets | BRAINTREE */
        WidgetDescriptor::updateOrCreate(
            ['name' => 'Braintree MRR'],
            array(
                'name'        => 'Braintree MRR',
                'description' => 'Braintree Monthly recurring revenue',
                'type'        => 'braintree_mrr',
                'category'    => 'financial',
                'is_premium'  => TRUE,
            )
        );

        WidgetDescriptor::updateOrCreate(
            ['name' => 'Braintree ARR'],
            array(
                'name'        => 'Braintree ARR',
                'description' => 'Braintree Annual recurring revenue',
                'type'        => 'braintree_arr',
                'category'    => 'financial',
                'is_premium'  => TRUE,
            )
        );

        WidgetDescriptor::updateOrCreate(
            ['name' => 'Braintree ARPU'],
            array(
                'name'        => 'Braintree ARPU',
                'description' => 'Braintree Average revenue per user',
                'type'        => 'braintree_arpu',
                'category'    => 'financial',
                'is_premium'  => TRUE,
            )
        );

        /* Send message to console */
        error_log('WidgetDescriptorSeeder | All WidgetDescriptors updated');
    }

} /* WidgetDescriptorSeeder */


