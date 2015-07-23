<?php

/**
* --------------------------------------------------------------------------
* StripeConnector:
*       Wrapper functions for Stripe connection
* Usage:
*       // For the connection uri (use directly in the template)
*       StripeHelper::getStripeConnectURI($redirect_to)
*
*       // For connecting the user
*       $stripeconnector = new StripeConnector($user);
*       try {
*           $stripeconnector->getTokens($code);
*       } catch (StripeConnectFailed $e) {
*           // error handling
*       }
*       $stripeconnector->connect();
* --------------------------------------------------------------------------
*/

use Stripe\Subscription;
use Stripe\Plan;
use Stripe\Customer;
use Stripe\Error\Authentication;

class StripeConnector
{
    /* -- Class properties -- */
    private $user;

    /* -- Constructor -- */
    function __construct($user) {
        $this->user = $user;
    }

    /**
     * ================================================== *
     *                   STATIC SECTION                   *
     * ================================================== *
     */

    /**
     * getStripeConnectURI
     * --------------------------------------------------
     * Returns the stripe connect url, based on config.
     * @param (string) ($redirect_to) A url to redirect after the connection
     * @return (string) ($connect_uri) A valid stripe connect URI.
     * --------------------------------------------------
     */
    public static function getStripeConnectURI($redirect_to) {
        /* Build Stripe connect URI */
        $connect_uri = $_ENV['STRIPE_CONNECT_URI'];
        $connect_uri .= '?response_type=' . 'code';
        $connect_uri .= '&client_id=' . $_ENV['STRIPE_CLIENT_ID'];
        $connect_uri .= '&scope=' . 'read_only';
        $connect_uri .= '&redirect_uri=' . $redirect_to;

        /* Return URI */
        return $connect_uri;
    }

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * connect
     * --------------------------------------------------
     * Sets up a stripe connection with the API key.
     * @throws StripeNotConnected
     * --------------------------------------------------
     */
    public function connect() {
        /* Check valid connection */
        if (!$this->user->isStripeConnected()) {
            throw new StripeNotConnected();
        }

        /* Get access token from DB. */
        $token = $this->user->connections()
            ->where('service', 'stripe')
            ->first()->access_token;

        /* Set up API key */
        \Stripe\Stripe::setApiKey($token);
    }

    /**
     * disconnect
     * --------------------------------------------------
     * Disconnecting the user from braintree.
     * @throws StripeNotConnected
     * --------------------------------------------------
     */
    public function disconnect() {
        /* Check valid connection */
        if (!$this->user->isStripeConnected()) {
            throw new StripeNotConnected();
        }

        $this->user->connections()->where('service', 'stripe')->delete();
    }
    /**
     * getTokens
     * --------------------------------------------------
     * Retrieving the access, and refresh tokens from authentication code.
     * @param (string) ($code) The returned code by stripe.
     * @return None
     * @throws StripeConnectFailed
     * --------------------------------------------------
     */
    public function getTokens($code) {
        /* Build and send POST request */
        $url = $this->buildTokenPostUriFromAuthCode($code);
        $response = $this->postUrl($url);

        /* Invalid/No answer from Stripe. */
        if ($response === null) {
            throw new StripeConnectFailed('Stripe connection error, please try again.', 1);
        }

        /* Error handling. */
        if (isset($response['error'])) {
            throw new StripeConnectFailed('Your connection expired, please try again.', 1);
        }

        /* Deleting previous connnection. */
        if ($this->user->isStripeConnected()) {
            $this->user->connections()->where('service', 'stripe')->delete();
        }

        // Creating a Connection instance, and saving to DB.
        $connection = new Connection(array(
            'access_token'  => $response['access_token'],
            'refresh_token' => $response['refresh_token'],
            'service'       => 'stripe',
        ));
        $connection->user()->associate($this->user);
        $connection->save();
    }

    /**
     * getNewAccessToken
     * --------------------------------------------------
     * Retrieving the access token from a refresh token.
     * @param None
     * @return None
     * @throws StripeConnectFailed
     * --------------------------------------------------
     */
    public function getNewAccessToken() {
        /* Check connection errors. */
        if (!$this->user->isStripeConnected()) {
            throw new StripeNotConnected();
        }

        /* Build and send POST request */
        $stripe_connection = $this->user->connections()->where('service', 'stripe')->first();
        $url = $this->buildTokenPostUriFromRefreshToken($stripe_connection->refresh_token);

        /* Get response */
        $response = $this->postUrl($url);

        /* Invalid/No answer from Stripe. */
        if ($response === null) {
            throw new StripeConnectFailed('Stripe server error, please try again.', 1);
        }

        /* Error handling. */
        if (isset($response['error'])) {
            throw new StripeConnectFailed('Your connection expired, please try again.', 1);
        }

        /* Saving new token. */
        $stripe_connection->access_token = $response['access_token'];
        $stripe_connection->save();
    }

    /**
     * Calculating the MRR for the user.
     * @todo MOVE THIS TO A SEPARATED FILE
     * @param $update, boolean Whether or not sync the db.
     * @return float The value of the mrr.
     * @throws StripeNotConnected
    */
    public function calculateMRR($update=False) {
        $mrr = 0;

        // Updating database, with the latest data.
        if ($update) {
            $this->updateSubscriptions();
        }

        // Iterating through the plans and subscriptions.
        foreach ($this->user->stripePlans()->get() as $plan) {
            foreach ($plan->subscriptions()->get() as $subscription) {
                // Dealing only with active subscriptions.
                if ($subscription->status == 'active') {
                    //
                    $value = $plan->amount * $subscription->quantity;
                    switch ($plan->interval) {
                        case 'day'  : $value *= 30; break;
                        case 'week' : $value *= 4.33; break;
                        case 'month': $value *= 1; break;
                        case 'year' : $value *= 1/12; break;
                        default: break;
                    }
                    $mrr += $value;
                }
            }
        }
        return $mrr;
    }

    /**
     * ================================================== *
     *                   PRIVATE SECTION                  *
     * ================================================== *
     */

    /**
     * buildTokenPostUriFromAuthCode
     * --------------------------------------------------
     * Creates a POST URI for the authorization and retrieving token.
     * @param (string) ($code) The returned code by stripe.
     * @return (array) (post_uri) The POST URI parameters
     * --------------------------------------------------
     */
    private function buildTokenPostUriFromAuthCode($code) {
        /* Build URI */
        $post_uri = array(
            'endpoint'  => $_ENV['STRIPE_ACCESS_TOKEN_URI'],
            'params'    => array(
                'client_secret' => $_ENV['STRIPE_SECRET_KEY'],
                'client_id'     => $_ENV['STRIPE_CLIENT_ID'],
                'code'          => $code,
                'grant_type'    => 'authorization_code'),
        );

        /* Return URI */
        return $post_uri;
    }

    /**
     * buildTokenPostUriFromRefreshToken
     * --------------------------------------------------
     * Creates a POST URI for the authorization and retrieving token.
     * @param (string) ($code) The returned code by stripe.
     * @return (string) (post_uri) The POST URI
     * --------------------------------------------------
     */
    private function buildTokenPostUriFromRefreshToken($refresh_token) {
        /* Build URI */
        $post_uri = array(
            'endpoint'  => $_ENV['STRIPE_ACCESS_TOKEN_URI'],
            'params'    => array(
                'client_secret' => $_ENV['STRIPE_SECRET_KEY'],
                'client_id'     => $_ENV['STRIPE_CLIENT_ID'],
                'refresh_token' => $refresh_token,
                'grant_type'    => 'refresh_token'
            )
        );

        /* Return URI */
        return $post_uri;
    }

    /**
     * postUrl
     * --------------------------------------------------
     * Creates a POST request, and returns the response
     * @param (array) ($url) The url endpoint and params
     * @return (array) ($response) JSON decoded response
     * --------------------------------------------------
     */
    private function postUrl($url) {
        /* Build request */
        $request = curl_init($url['endpoint']);
        curl_setopt($request, CURLOPT_POST, 1);
        curl_setopt($request, CURLOPT_POSTFIELDS, http_build_query($url['params']));
        curl_setopt($request, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($request, CURLOPT_HEADER, 0);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);

        // TODO: Additional error handling
        /* Get response */
        $respCode = curl_getinfo($request, CURLINFO_HTTP_CODE);
        $response = json_decode(curl_exec($request), TRUE);
        curl_close($request);

        /* Return response */
        return $response;
    }

    /**
     * Updating the current stripe Plans.
     *
     * @returns The stripe plans.
     * @throws StripeNotConnected
    */
    private function updatePlans() {
        // Connecting to stripe, and making query.
        $this->connect();
        try {
            $decoded_data = json_decode(
                $this->loadJSON(\Stripe\Plan::all()), TRUE);
        } catch (\Stripe\Error\Authentication $e) {
            // Access token expired. Calling handler.
            $this->getNewAccessToken();
        }

        // Getting the plans.
        $plans = [];
        foreach($decoded_data['data'] as $plan) {
            $new_plan = new StripePlan(array(
                'plan_id'        => $plan['id'],
                'name'           => $plan['name'],
                'currency'       => $plan['currency'],
                'amount'         => $plan['amount'],
                'interval'       => $plan['interval'],
                'interval_count' => $plan['interval_count']
            ));
            $new_plan->user()->associate($this->user);
            array_push($plans, $new_plan);
        }

        // Delete old, save new.
        foreach (StripePlan::where('user_id', $this->user->id)->get() as $stripePlan) {
            StripeSubscription::where('plan_id', $stripePlan->id)->delete();
        }

        stripeplan::where('user_id', $this->user->id)->delete();
        foreach ($plans as $plan) {
            $plan->save();
        }

        return $plans;
    }

    /**
     * Updating the StripeSubscriptions.
     *
     * @returns The stripe plans.
     * @throws StripeNotConnected
    */
    private function updateSubscriptions() {
        // Connecting to stripe.
        $this->connect();

        // Deleting all subscription to avoid constraints.
        $this->updatePlans();
        $subscriptions = array();

        foreach ($this->getCustomers() as $customer) {
            $decoded_data = json_decode(
                $this->loadJSON(\Stripe\Customer::retrieve($customer['id'])->subscriptions->all()),
                TRUE);
            foreach($decoded_data['data'] as $subscription) {
                $new_subscription = new StripeSubscription(array(
                    'start'       => $subscription['start'],
                    'status'      => $subscription['status'],
                    'customer'    => $subscription['customer'],
                    'ended_at'    => $subscription['ended_at'],
                    'canceled_at' => $subscription['canceled_at'],
                    'quantity'    => $subscription['quantity'],
                    'discount'    => $subscription['discount'],
                    'trial_start' => $subscription['trial_start'],
                    'trial_end'   => $subscription['trial_start'],
                    'discount'    => $subscription['discount']
                ));
                $plan = StripePlan::where('plan_id', $subscription['plan']['id'])->first();
                if ($plan === null) {
                    // Stripe integrity error, link to a non-existing plan.
                    return array();
                }
                $new_subscription->plan()->associate($plan);
                array_push($subscriptions, $new_subscription);
            }
        }

        // Save new.
        foreach ($subscriptions as $subscription) {
            $subscription->save();
        }

        return $subscriptions;
    }

    /**
     * Getting the stripe plans from an already setup stripe connection.
     *
     * @param stripe_json string of the received object.
     * @return the decoded object.
    */
    private function loadJSON($stripe_json) {
        return strstr($stripe_json, '{');
    }

    /**
     * Getting a list of customers.
     *
     * @returns The stripe customers.
     * @throws StripeNotConnected
    */
    private function getCustomers() {
        // Connecting to stripe, and making query.
        $this->connect();
        try {
            $decoded_data = json_decode(
                $this->loadJSON(\Stripe\Customer::all()), TRUE);
        } catch (\Stripe\Error\Authentication $e) {
            // Access token expired. Calling handler.
            $this->getNewAccessToken();
        }

        // Getting the plans.
        $customers = [];
        foreach($decoded_data['data'] as $customer) {
            array_push($customers, $customer);
        }

        // Return.
        return $customers;
    }
} /* StripeConnector */
