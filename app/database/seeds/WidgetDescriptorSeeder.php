<?php

class WidgetDescriptorSeeder extends Seeder
{
    public function run()
    {

        /* WidgetDescriptor: Update or create all */
        /* Personal widgets */
        WidgetDescriptor::updateOrCreate(
            ['type' => 'webhook_histogram'],
            array(
                'name'        => 'Webhook histogram',
                'description' => 'Building a simple line histogram from your data.',
                'type'        => 'webhook_histogram',
                'category'    => 'personal',
                'is_premium'  => TRUE,
                'min_cols'     => 3,
                'min_rows'     => 2,
                'default_cols' => 5,
                'default_rows' => 3
            )
        );
        WidgetDescriptor::updateOrCreate(
            ['type' => 'clock'],
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
            ['type' => 'quotes'],
            array(
                'name'        => 'Quotes',
                'description' => 'Get inspired every day, by this awesome widget.',
                'type'        => 'quote',
                'category'    => 'personal',
                'is_premium'  => FALSE,
                'min_cols'     => 5,
                'min_rows'     => 1,
                'default_cols' => 10,
                'default_rows' => 2
            )
        );

        WidgetDescriptor::updateOrCreate(
            ['type' => 'greetings'],
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
            ['type' => 'text'],
            array(
                'name'        => 'Text',
                'description' => 'Insert any text you want in this widget',
                'type'        => 'text',
                'category'    => 'personal',
                'is_premium'  => FALSE,
                'min_cols'     => 1,
                'min_rows'     => 1,
                'default_cols' => 2,
                'default_rows' => 1
            )
        );

        if (!App::environment('production', 'staging')) {

            WidgetDescriptor::updateOrCreate(
                ['type' => 'iframe'],
                array(
                    'name'        => 'Iframe',
                    'description' => 'Include your favourite sites into this dashboard.',
                    'type'        => 'iframe',
                    'category'    => 'personal',
                    'is_premium'  => FALSE,
                )
            );

            WidgetDescriptor::updateOrCreate(
                ['type' => 'note'],
                array(
                    'name'        => 'Note',
                    'description' => '',
                    'type'        => 'note',
                    'category'    => 'personal',
                    'is_premium'  => FALSE,
                )
            );

            WidgetDescriptor::updateOrCreate(
                ['type' => 'timer'],
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
            ['type' => 'stripe_mrr'],
            array(
                'name'        => 'Monthly recurring revenue',
                'description' => 'Monthly recurring revenue',
                'type'        => 'stripe_mrr',
                'category'    => 'stripe',
                'is_premium'  => TRUE,
                'min_cols'     => 3,
                'min_rows'     => 2,
                'default_cols' => 5,
                'default_rows' => 3
            )
        );

        WidgetDescriptor::updateOrCreate(
            ['type' => 'stripe_arr'],
            array(
                'name'        => 'Annual recurring revenue',
                'description' => 'Annual recurring revenue',
                'type'        => 'stripe_arr',
                'category'    => 'stripe',
                'is_premium'  => TRUE,
                'min_cols'     => 3,
                'min_rows'     => 2,
                'default_cols' => 5,
                'default_rows' => 3
            )
        );

        WidgetDescriptor::updateOrCreate(
            ['type' => 'stripe_arpu'],
            array(
                'name'        => 'Average revenue per user',
                'description' => 'Average revenue per user',
                'type'        => 'stripe_arpu',
                'category'    => 'stripe',
                'is_premium'  => TRUE,
                'min_cols'     => 3,
                'min_rows'     => 2,
                'default_cols' => 5,
                'default_rows' => 3
            )
        );

        if (!App::environment('production', 'staging')) {
            WidgetDescriptor::updateOrCreate(
                ['type' => 'stripe_events'],
                array(
                    'name'        => 'Events',
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
            ['type' => 'braintree_mrr'],
            array(
                'name'        => 'Monthly recurring revenue',
                'description' => 'Monthly recurring revenue',
                'type'        => 'braintree_mrr',
                'category'    => 'braintree',
                'is_premium'  => TRUE,
                'min_cols'     => 3,
                'min_rows'     => 2,
                'default_cols' => 5,
                'default_rows' => 3
            )
        );

        WidgetDescriptor::updateOrCreate(
            ['type' => 'braintree_arr'],
            array(
                'name'        => 'Annual recurring revenue',
                'description' => 'Annual recurring revenue',
                'type'        => 'braintree_arr',
                'category'    => 'braintree',
                'is_premium'  => TRUE,
                'min_cols'     => 3,
                'min_rows'     => 2,
                'default_cols' => 5,
                'default_rows' => 3
            )
        );

        WidgetDescriptor::updateOrCreate(
            ['type' => 'braintree_arpu'],
            array(
                'name'        => 'Average revenue per user',
                'description' => 'Average revenue per user',
                'type'        => 'braintree_arpu',
                'category'    => 'braintree',
                'is_premium'  => TRUE,
                'min_cols'     => 3,
                'min_rows'     => 2,
                'default_cols' => 5,
                'default_rows' => 3
            )
        );

        if (!App::environment('production')) {
            /* Social widgets | TWITTER */
            WidgetDescriptor::updateOrCreate(
                ['type' => 'twitter_followers'],
                array(
                    'name'        => 'Followers',
                    'description' => 'Follower count',
                    'type'        => 'twitter_followers',
                    'category'    => 'twitter',
                    'is_premium'  => TRUE,
                    'min_cols'     => 3,
                    'min_rows'     => 2,
                    'default_cols' => 5,
                    'default_rows' => 3
                )
            );

            WidgetDescriptor::updateOrCreate(
                ['type' => 'twitter_new_followers'],
                array(
                    'name'        => 'New followers',
                    'description' => 'New follower count',
                    'type'        => 'twitter_new_followers',
                    'category'    => 'twitter',
                    'is_premium'  => TRUE,
                    'min_cols'     => 3,
                    'min_rows'     => 2,
                    'default_cols' => 5,
                    'default_rows' => 3
                )
            );

            /* Social widgets | FACEBOOK */
            WidgetDescriptor::updateOrCreate(
                ['type' => 'facebook_likes'],
                array(
                    'name'        => 'Likes',
                    'description' => 'The total number of people who have liked your Page.',
                    'type'        => 'facebook_likes',
                    'category'    => 'facebook',
                    'is_premium'  => TRUE,
                    'min_cols'     => 3,
                    'min_rows'     => 2,
                    'default_cols' => 5,
                    'default_rows' => 3
                )
            );

            WidgetDescriptor::updateOrCreate(
                ['type' => 'facebook_likes_count'],
                array(
                    'name'        => 'Likes count',
                    'description' => 'The total number of people who have liked your Page.',
                    'type'        => 'facebook_likes_count',
                    'category'    => 'facebook',
                    'is_premium'  => TRUE,
                    'min_cols'     => 3,
                    'min_rows'     => 2,
                    'default_cols' => 5,
                    'default_rows' => 3
                )
            );

            WidgetDescriptor::updateOrCreate(
                ['type' => 'facebook_new_likes'],
                array(
                    'name'        => 'New likes',
                    'description' => 'The total number of people who have liked your Page daily.',
                    'type'        => 'facebook_new_likes',
                    'category'    => 'facebook',
                    'is_premium'  => TRUE,
                    'min_cols'     => 3,
                    'min_rows'     => 2,
                    'default_cols' => 5,
                    'default_rows' => 3
                )
            );

            WidgetDescriptor::updateOrCreate(
                ['type' => 'facebook_page_impressions'],
                array(
                    'name'        => 'Page impressions',
                    'description' => 'The number of people who have seen any content associated with your Page',
                    'type'        => 'facebook_page_impressions',
                    'category'    => 'facebook',
                    'is_premium'  => TRUE,
                    'min_cols'     => 3,
                    'min_rows'     => 2,
                    'default_cols' => 5,
                    'default_rows' => 3
                )
            );

            /* Social widgets | GOOGLE ANALYTICS */
            WidgetDescriptor::updateOrCreate(
                ['type' => 'google_analytics_bounce_rate'],
                array(
                    'name'        => 'Bounce rate',
                    'description' => 'The percentage of single-page session (i.e., session in which the person left your property from the first page).',
                    'type'        => 'google_analytics_bounce_rate',
                    'category'    => 'google_analytics',
                    'is_premium'  => TRUE,
                    'min_cols'     => 3,
                    'min_rows'     => 2,
                    'default_cols' => 5,
                    'default_rows' => 3
                )
            );

            WidgetDescriptor::updateOrCreate(
                ['type' => 'google_analytics_sessions'],
                array(
                    'name'        => 'Sessions',
                    'description' => 'The total number of sessions',
                    'type'        => 'google_analytics_sessions',
                    'category'    => 'google_analytics',
                    'is_premium'  => TRUE,
                    'min_cols'     => 3,
                    'min_rows'     => 2,
                    'default_cols' => 5,
                    'default_rows' => 3
                )
            );

            WidgetDescriptor::updateOrCreate(
                ['type' => 'google_analytics_avg_session_duration'],
                array(
                    'name'        => 'Average session duration',
                    'description' => 'The average duration of user sessions represented in total seconds.',
                    'type'        => 'google_analytics_avg_session_duration',
                    'category'    => 'google_analytics',
                    'is_premium'  => TRUE,
                    'min_cols'     => 3,
                    'min_rows'     => 2,
                    'default_cols' => 5,
                    'default_rows' => 3
                )
            );
        } /* !App::environment('production', 'staging')*/

        /* Send message to console */
        Log::info('WidgetDescriptorSeeder | All WidgetDescriptors updated');
    }

} /* WidgetDescriptorSeeder */
