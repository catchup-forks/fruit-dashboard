<?php

class DataDescriptorSeeder extends Seeder
{
    public function run()
    {
        /* DataDescriptors */

        /* Google analytics descriptors */
        DataDescriptor::updateOrCreate(
            array(
                'type'     => 'new_users',
                'category' => 'google_analytics'
            ),
            array(
                'type'       => 'new_users',
                'category'   => 'google_analytics',
                'attributes' => '{"cumulative": true}'
        ));
        DataDescriptor::updateOrCreate(
            array(
                'type'     => 'users',
                'category' => 'google_analytics'
            ),
            array(
                'type'       => 'users',
                'category'   => 'google_analytics',
                'attributes' => '{"cumulative": true}'
        ));
        DataDescriptor::updateOrCreate(
            array(
                'type'     => 'sessions',
                'category' => 'google_analytics'
            ),
            array(
                'type'       => 'sessions',
                'category'   => 'google_analytics',
                'attributes' => '{"cumulative": true}'
        ));
        DataDescriptor::updateOrCreate(
            array(
                'type'     => 'goal_completion',
                'category' => 'google_analytics'
            ),
            array(
                'type'       => 'goal_completion',
                'category'   => 'google_analytics',
                'attributes' => '{"cumulative": true}'
        ));
        DataDescriptor::updateOrCreate(
            array(
                'type'     => 'active_users',
                'category' => 'google_analytics'
            ),
            array(
                'type'       => 'active_users',
                'category'   => 'google_analytics',
                'attributes' => '{"cumulative": false}'
        ));
        DataDescriptor::updateOrCreate(
            array(
                'type'     => 'bounce_rate',
                'category' => 'google_analytics'
            ),
            array(
                'type'       => 'bounce_rate',
                'category'   => 'google_analytics',
                'attributes' => '{"cumulative": false}'
        ));
        DataDescriptor::updateOrCreate(
            array(
                'type'     => 'avg_session_duration',
                'category' => 'google_analytics'
            ),
            array(
                'type'       => 'avg_session_duration',
                'category'   => 'google_analytics',
                'attributes' => '{"cumulative": false}'
        ));

        /* Facebook descriptors */
        DataDescriptor::updateOrCreate(
            array(
                'type'     => 'likes',
                'category' => 'facebook'
            ),
            array(
                'type'       => 'likes',
                'category'   => 'facebook',
                'attributes' => '{"cumulative": false}' // Facebook likes is naturally cumulative on the source.
        ));
        DataDescriptor::updateOrCreate(
            array(
                'type'     => 'page_impressions',
                'category' => 'facebook',
            ),
            array(
                'type'       => 'page_impressions',
                'category'   => 'facebook',
                'attributes' => '{"cumulative": true}'
        ));
        DataDescriptor::updateOrCreate(
            array(
                'type'     => 'engaged_users',
                'category' => 'facebook'
            ),
            array(
                'type'       => 'engaged_users',
                'category'   => 'facebook',
                'attributes' => '{"cumulative": true}'
        ));

        /* Twitter descriptors */
        DataDescriptor::updateOrCreate(
            array(
                'type'     => 'followers',
                'category' => 'twitter',
                'attributes' => '{"cumulative": false}' // Facebook likes is naturally cumulative on the source.
            ),
            array(
                'type'       => 'followers',
                'category'   => 'twitter',
        ));
        DataDescriptor::updateOrCreate(
            array(
                'type'     => 'mentions',
                'category' => 'twitter'
            ),
            array(
                'type'     => 'mentions',
                'category' => 'twitter',
        ));

        /* Other descriptors */
        DataDescriptor::updateOrCreate(
            array(
                'type'     => 'webhook',
                'category' => 'webhook_api'
            ),
            array(
                'type'       => 'webhook',
                'category'   => 'webhook_api',
                'attributes' => '{"cumulative": false}'
        ));
        DataDescriptor::updateOrCreate(
            array(
                'type'     => 'api',
                'category' => 'webhook_api'
            ),
            array(
                'type'       => 'api',
                'category'   => 'webhook_api',
                'attributes' => '{"cumulative": false}'
        ));

        /* Stripe descriptors */
        DataDescriptor::updateOrCreate(
            array(
                'type'     => 'mrr',
                'category' => 'stripe'
            ),
            array(
                'type'       => 'mrr',
                'category'   => 'stripe',
        ));
        DataDescriptor::updateOrCreate(
            array(
                'type'     => 'arr',
                'category' => 'stripe'
            ),
            array(
                'type'       => 'arr',
                'category'   => 'stripe',
        ));
        DataDescriptor::updateOrCreate(
            array(
                'type'     => 'arpu',
                'category' => 'stripe'
            ),
            array(
                'type'       => 'arpu',
                'category'   => 'stripe',
            ));

        /* Braintree descriptors */
        DataDescriptor::updateOrCreate(
            array(
                'type'     => 'mrr',
                'category' => 'braintree'
            ),
            array(
                'type'       => 'mrr',
                'category'   => 'braintree',
        ));
        DataDescriptor::updateOrCreate(
            array(
                'type'     => 'arr',
                'category' => 'braintree'
            ),
            array(
                'type'       => 'arr',
                'category'   => 'braintree',
        ));
        DataDescriptor::updateOrCreate(
            array(
                'type'     => 'arpu',
                'category' => 'braintree'
            ),
            array(
                'type'       => 'arpu',
                'category'   => 'braintree',
        ));
        /* Personal descriptors */
        DataDescriptor::updateOrCreate(
            array(
                'type'     => 'quote',
                'category' => 'personal'
            ),
            array(
                'type'       => 'quote',
                'category'   => 'personal',
        ));
        Log::info('DataDescriptorSeeder | All DataDescriptors updated.');
    }

} /* WidgetDescriptorSeeder */
