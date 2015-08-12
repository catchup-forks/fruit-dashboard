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
                'min_cols'     => 2,
                'min_rows'     => 2,
                'default_cols' => 3,
                'default_rows' => 2
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
                'min_cols'     => 5,
                'min_rows'     => 1,
                'default_cols' => 7,
                'default_rows' => 1
            )
        );

        WidgetDescriptor::updateOrCreate(
            ['name' => 'Text'],
            array(
                'name'        => 'Text',
                'description' => 'Insert any text you want in this widget',
                'type'        => 'text',
                'category'    => 'personal',
                'is_premium'  => FALSE,
            )
        );

        if (!App::environment('production', 'staging')) {

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

            WidgetDescriptor::updateOrCreate(
                ['name' => 'Timer'],
                array(
                    'name'         => 'Timer',
                    'description'  => 'A simple timer',
                    'type'         => 'timer',
                    'category'     => 'personal',
                    'is_premium'   => FALSE,
                    'min_cols'     => 2,
                    'min_rows'     => 2,
                    'default_cols' => 2,
                    'default_rows' => 2
                )
            );

        } /* !App::environment('production', 'staging')*/


        /* Financial widgets | STRIPE */
        WidgetDescriptor::updateOrCreate(
            ['name' => 'Stripe MRR'],
            array(
                'name'        => 'Stripe MRR',
                'description' => 'Stripe Monthly recurring revenue',
                'type'        => 'stripe_mrr',
                'category'    => 'stripe',
                'is_premium'  => TRUE,
            )
        );

        WidgetDescriptor::updateOrCreate(
            ['name' => 'Stripe ARR'],
            array(
                'name'        => 'Stripe ARR',
                'description' => 'Stripe Annual recurring revenue',
                'type'        => 'stripe_arr',
                'category'    => 'stripe',
                'is_premium'  => TRUE,
            )
        );

        WidgetDescriptor::updateOrCreate(
            ['name' => 'Stripe ARPU'],
            array(
                'name'        => 'Stripe ARPU',
                'description' => 'Stripe Average revenue per user',
                'type'        => 'stripe_arpu',
                'category'    => 'stripe',
                'is_premium'  => TRUE,
            )
        );

        if (!App::environment('production', 'staging')) {
            WidgetDescriptor::updateOrCreate(
                ['name' => 'Stripe events'],
                array(
                    'name'        => 'Stripe events',
                    'description' => 'Your stripe events',
                    'type'        => 'stripe_events',
                    'category'    => 'stripe',
                    'is_premium'  => TRUE,
                    'min_cols'     => 2,
                    'min_rows'     => 4,
                    'default_cols' => 2,
                    'default_rows' => 5
                )
            );
        } /* !App::environment('production', 'staging')*/

        /* Financial widgets | BRAINTREE */
        WidgetDescriptor::updateOrCreate(
            ['name' => 'Braintree MRR'],
            array(
                'name'        => 'Braintree MRR',
                'description' => 'Braintree Monthly recurring revenue',
                'type'        => 'braintree_mrr',
                'category'    => 'braintree',
                'is_premium'  => TRUE,
            )
        );

        WidgetDescriptor::updateOrCreate(
            ['name' => 'Braintree ARR'],
            array(
                'name'        => 'Braintree ARR',
                'description' => 'Braintree Annual recurring revenue',
                'type'        => 'braintree_arr',
                'category'    => 'braintree',
                'is_premium'  => TRUE,
            )
        );

        WidgetDescriptor::updateOrCreate(
            ['name' => 'Braintree ARPU'],
            array(
                'name'        => 'Braintree ARPU',
                'description' => 'Braintree Average revenue per user',
                'type'        => 'braintree_arpu',
                'category'    => 'braintree',
                'is_premium'  => TRUE,
            )
        );
        WidgetDescriptor::updateOrCreate(
            ['name' => 'Twitter followers'],
            array(
                'name'        => 'Twitter followers',
                'description' => 'Twitter follower count',
                'type'        => 'twitter_followers',
                'category'    => 'twitter',
                'is_premium'  => TRUE,
            )
        );
        /* Send message to console */
        Log::info('WidgetDescriptorSeeder | All WidgetDescriptors updated');
    }

} /* WidgetDescriptorSeeder */


