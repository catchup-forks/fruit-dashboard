<?php

class DataDescriptorSeeder extends Seeder
{
    public function run()
    {

        /* DataDescriptors */

        /* Google analytics descriptors */
        DataDescriptor::create(array(
                'type'       => 'new_users',
                'category'   => 'google_analytics',
                'attributes' => '{"cumulative": "true"}'
        ));
        DataDescriptor::create(array(
                'type'       => 'users',
                'category'   => 'google_analytics',
                'attributes' => '{"cumulative": "true"}'
        ));
        DataDescriptor::create(array(
                'type'       => 'sessions',
                'category'   => 'google_analytics',
                'attributes' => '{"cumulative": "true"}'
        ));
        DataDescriptor::create(array(
                'type'       => 'goal_completion',
                'category'   => 'google_analytics',
                'attributes' => '{"cumulative": "true"}'
        ));
        DataDescriptor::create(array(
                'type'       => 'active_users',
                'category'   => 'google_analytics',
                'attributes' => '{"cumulative": "false"}'
        ));
        DataDescriptor::create(array(
                'type'       => 'bounce_rate',
                'category'   => 'google_analytics',
                'attributes' => '{"cumulative": "false"}'
        ));
        DataDescriptor::create(array(
                'type'       => 'avg_session_duration',
                'category'   => 'google_analytics',
                'attributes' => '{"cumulative": "false"}'
        ));

        /* Facebook descriptors */
        DataDescriptor::create(array(
                'type'       => 'likes',
                'category'   => 'facebook',
                'attributes' => '{"cumulative": "true"}'
        ));
        DataDescriptor::create(array(
                'type'       => 'page_impressions',
                'category'   => 'facebook',
                'attributes' => '{"cumulative": "false"}'
        ));
        DataDescriptor::create(array(
                'type'       => 'engaged_users',
                'category'   => 'facebook',
                'attributes' => '{"cumulative": "false"}'
        ));

        /* Twitter descriptors */
        DataDescriptor::create(array(
                'type'       => 'followers',
                'category'   => 'twitter',
                'attributes' => '{"cumulative": "true"}'
        ));
        DataDescriptor::create(array(
                'type'     => 'mentions',
                'category' => 'twitter',
        ));

        /* Other descriptors */
        DataDescriptor::create(array(
                'type'       => 'webhook',
                'category'   => 'webhook_api',
                'attributes' => '{"cumulative": "false"}'
        ));
        DataDescriptor::create(array(
                'type'       => 'api',
                'category'   => 'webhook_api',
                'attributes' => '{"cumulative": "false"}'
        ));

        /* Stripe descriptors */
        DataDescriptor::create(array(
                'type'       => 'mrr',
                'category'   => 'stripe',
        ));
        DataDescriptor::create(array(
                'type'       => 'arr',
                'category'   => 'stripe',
        ));
        DataDescriptor::create(array(
                'type'       => 'arpu',
                'category'   => 'stripe',
            ));

        /* Braintree descriptors */
        DataDescriptor::create(array(
                'type'       => 'mrr',
                'category'   => 'braintree',
        ));
        DataDescriptor::create(array(
                'type'       => 'arr',
                'category'   => 'braintree',
        ));
        DataDescriptor::create(array(
                'type'       => 'arpu',
                'category'   => 'braintree',
        ));

        /* Other descriptors */
        DataDescriptor::create(array(
                'type'       => 'webhook',
                'category'   => 'webhook_api',
                'attributes' => '{"cumulative": "false"}'
        ));
        DataDescriptor::create(array(
                'type'       => 'api',
                'category'   => 'webhook_api',
                'attributes' => '{"cumulative": "false"}'
        ));

        Log::info('DataDescriptorSeeder | All WidgetDescriptors updated.');
    }

} /* WidgetDescriptorSeeder */
