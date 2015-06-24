<?php
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\OpenIdTokeninfo;

use PayPal\Api\Plan;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\Currency;

use PayPal\Api\Agreement;


class PayPalHelper {

    /**
     * Getting the API context for the application
     * change the clientID and clientSecret accordingly
     *
     * @return boolean
    */
    public static function getApiContext() {
        // setting api codes
        $client_id = "AY1PlRC0yK6SExlx8aRDW-hF2REkl90Qmza0Ak5LUacd-LFAczGmXfanQYK-";
        $client_secret = "EBXUZxD6PobEUtc-WldtZgbG8eUzl4IkOFAeMxpAGhNDt-mESoj3a3QRRIGw";


        // getting the ApiContext from oauths
        $api_context = new ApiContext(
            new OAuthTokenCredential(
                $client_id,
                $client_secret
            )
        );

        // setting api contextd
        $api_context->setConfig(
            array(
                'mode'                   => 'sandbox',
                'http.ConnectionTimeOut' => 30,
                'log.LogEnabled'         => true,
                'log.FileName'           => '../PayPal.log',
                'log.LogLevel'           => 'FINE',
                'validation.level'       => 'log',
                'cache.enabled'          => 'true'
            )
        );

        // returning api context
        return $api_context;
    }

    /**
     * Generating an access token for the user
     * from the previously stored refresh token
     * false on no token
     *
     * @return String/boolean
    */
    public static function generateAccessTokenFromRefreshToken($refresh_token)
    {
        $api_context = PayPalHelper::getApiContext();

        try {
            // getting token info
            $token_info = new OpenIdTokeninfo();
            $token_info = $token_info->createFromRefreshToken(array('refresh_token' => $refresh_token), $api_context);

        } catch (Exception $ex) {
            // something went wrong
            echo '<pre>';print_r(json_decode($ex->getData()));
            exit(1);
        }

        // everything's fine, returning accessToken
        return $token_info->getAccessToken();
    }

    /**
     * Getting all the charges for the user
     * @param paypal key
     *
     * @return an array with the charges
    */

    public static function getCharges($key)
    {
        $out_charges = array();

        // tell paypal who we are
        // get the charges from paypal
        // build return array

        // return the object
        return $out_charges;
    }

    /**
     * Getting specific events for the user
     * @param paypal key
     *
     * @return an array with the charges
    */

    public static function getEvents($apiContext)
    {
        $out_events = array();

        // tell paypal who we are
        // get the events from paypal
        // build return array

        // return the object
        return $out_events;
    }

    /**
     * Getting all the plans for the user
     * @param paypal api_context
     *
     * @return an array with the plans
    */

    public static function getPlans($api_context)
    {
        // initializing output array
        $out_plans = array();

        try {
            // getting the list of plans
            $params = array('page_size' => '20', 'status' => 'ACTIVE'); // needs paging !!!!
            $planlist = Plan::all($params, $api_context);

        } catch (PayPal\Exception\PPConnectionException $ex) {

            // error handling
            echo '<pre>';
            print_r(json_decode($ex->getData()));
            exit(1);

        }

        // building up the output array
        foreach ($planlist->getPlans() as $raw_plan) {
            // getting the plan
            $plan = Plan::get($raw_plan->getId(), $api_context);

            // decoding data
            $json_plan = json_decode($plan->toJSON(), true);

            // initializing array to add
            $plan_instance = array();

            // extracting data
            $plan_instance['name'] = $json_plan['name'];
            $plan_instance['interval'] = $json_plan['payment_definitions'][0]['frequency'];
            $plan_instance['interval_count'] = $json_plan['payment_definitions'][0]['frequency_interval'];
            $plan_instance['currency'] = $json_plan['payment_definitions'][0]['amount']['currency'];
            $plan_instance['amount'] = $json_plan['payment_definitions'][0]['amount']['value']*100;
            $plan_instance['provider'] = 'paypal';

            // adding to array
            $out_plans[self::generatePlanId($plan)] = $plan_instance;
        }

        // returning object
        return $out_plans;
    }

     /**
     * Generating a goddamn id for the plan, because paypal's own function sucks
     * @param paypal plan
     *
     * @return an id
    */

    private static function generatePlanId($plan) {

        $jsonPlan = json_decode($plan->toJson(), true);


        $id = $jsonPlan['payment_definitions'][0]['frequency'].
            $jsonPlan['payment_definitions'][0]['amount']['value'][0].
            $jsonPlan['payment_definitions'][0]['cycles'].
            $jsonPlan['merchant_preferences']['max_fail_attempts'];

        return $id;
    }

     /**
     * Getting the paypal customers for the user
     * @param paypal api_context
     *
     * @return an array with the subscriptions
    */

    public static function getCustomers($apiContext)
    {
        // retrieving the subscriptions
        // !!! Currently unavailable !!!
        // subscription_ids = getSubscriptions()

        $subscriptionIds = array("I-F231FUFEPYG8", "I-WFTN8BULD984", "I-YSRV6BDEPBLG");

        // initializing customer array
        $customers = array();

        // counter (just for testing purposes)
        $i = 0;

        // going through the ids
        foreach ($subscriptionIds as $subId) {

            $i++; // just for testing

            // trying to get the agreements one by one
            try {
                // getting the agreement
                $agreement = Agreement::get($subId, $apiContext);


                // transforming agreement to our format
                $formatted_agreement = array
                (
                    'id'     => $subId,
                    'start'  => strtotime($agreement->getStartDate()),
                    'status' => strtolower($agreement->getState()),
                    'plan'   => array('id' => self::generatePlanId($agreement->getPlan()))
                );

                // getting the payer of the agreement
                $payer = $agreement->getPayer();

                // this is not working yet because the API sucks at the moment
                // ... but it'll get better
                // getting the payer_info
                //$payer_info = $payer->getPayerInfo();

                // getting the user id
                //$user_email = $payer_info->getEmail();

                // right now just adding something that makes sense
                $user_email = $i%2;

                $found = false;
                // finding out whether or not we know this customer
                foreach ($customers as $index=>$customer_i) {

                    // matching email
                    if ($customer_i['email'] == $user_email) {
                        // found
                        $found = $index;
                    }
                }

                if ($found === false) {
                    // customer not found

                    // pushing new customer to array
                    array_push($customers, array
                        (
                            'zombie'        => false,
                            'email'         => $user_email,
                            'subscriptions' => array(
                                'total_count' => 1,
                                'data' => array($formatted_agreement))
                        )
                    );
                    // user's first subscription

                } else {
                    // we already added this customer

                    // adding agreement to the existing ones
                    array_push(
                        $customers[$found]['subscriptions']['data'],
                        $formatted_agreement
                    );

                    // adding total count
                    $customers[$found]['subscriptions']['total_count']++;
                }

            } catch (PayPal\Exception\PayPalConnectionException $ex) {
                // an error occoured
                echo '<pre>';print_r(json_decode($ex->getData()));
                exit(1);
            }

        }

        // returning object
        return $customers;
    }
}
