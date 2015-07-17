<?php
use Stripe\Subscription;
use Stripe\Plan;
use Stripe\Customer;
use Stripe\Error\Authentication;

/* All stripe related functions are bundled in this class.
*/
class StripeHelper
{
    // -- Attributes -- //
    private $user;

    // -- Constructor -- //
    function __construct($user) {
        $this->user = $user;
    }

    // -- Static section -- //
    /**
     * Returning the stripe connect url, based on config.
     *
     * @returns string A valid stripe conenct URI.
    */
    public static function getStripeConnectURI() {
        // Building Stripe connect URI.
        $connect_uri = Config::get('constants.STRIPE_CONNECT_URI');
        $connect_uri .= '?response_type=' . 'code';
        $connect_uri .= '&client_id=' . Config::get('constants.STRIPE_CLIENT_ID');
        $connect_uri .= '&scope=' . 'read_only';
        return $connect_uri;
    }

    // -- Public section -- //
    /**
     * Setting up a stripe connection with the API key.
     *
     * @param $user uint The editable user.
     * @returns The stripe plans.
     * @throws StripeNotConnected
    */
    public function connect($user) {
        if (!$user->isStripeConnected()) {
            throw new StripeNotConnected();
        }

        // Getting access token from DB.
        $token = Connection::where('user_id', $user->id)
                           ->where('service', 'stripe')
                           ->first()
                           ->access_token;

        // Setting API key.
        \Stripe\Stripe::setApiKey($token);
    }

    /**
     * Retrieving the access, and refresh tokens for the first time.
     *
     * @param string $code The returned code by stripe.
     * @returns None
     * @throws StripeConnectFailed
    */
    public function getTokens($code) {
        // Building POST request.
        $url= Config::get('constants.STRIPE_ACCESS_TOKEN_URI');
        $post_kwargs = 'client_secret=' . Config::get('constants.STRIPE_SECRET_KEY');;
        $post_kwargs .= '&code=' . $code;
        $post_kwargs .= '&grant_type=' . 'authorization_code';

        // POST settings.
        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_kwargs);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_HEADER, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

        // Retrieving response.
        $response = json_decode(curl_exec( $ch ), TRUE);

        // Invalid/No answer from Stripe.
        if ($response === null) {
            throw new StripeConnectFailed('Stripe server error, please try again.', 1);
        }

        // Error handling.
        if (isset($response['error'])) {
            throw new StripeConnectFailed('Your connection expired, please try again.', 1);
        }

        // Creating a Connection instance, and saving to DB.
        $connection = new Connection(array(
            'access_token'  => $response['access_token'],
            'refresh_token' => $response['refresh_token'],
            'service'          => 'stripe',
        ));
        $connection['user_id'] = $this->user->id;
        $connection->save();
    }

    /**
     * Updating the current stripe Plans.
     *
     * @returns The stripe plans.
     * @throws StripeNotConnected
    */
    public function updatePlans() {
        // Connecting to stripe, and making query.
        $this->connect($this->user);
        try {
            $decoded_data = json_decode(
                $this->loadJSON(\Stripe\Plan::all()), TRUE);
        } catch (\Stripe\Error\Authentication $e) {
            // Access token expired. Calling handler.
            $this->getNewToken();
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
    public function updateSubscriptions() {
        // Connecting to stripe.
        $this->connect($this->user);

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

    // -- Private section -- //
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
        $this->connect($this->user);
        try {
            $decoded_data = json_decode(
                $this->loadJSON(\Stripe\Customer::all()), TRUE);
        } catch (\Stripe\Error\Authentication $e) {
            // Access token expired. Calling handler.
            $this->getNewToken();
        }

        // Getting the plans.
        $customers = [];
        foreach($decoded_data['data'] as $customer) {
            array_push($customers, $customer);
        }

        // Return.
        return $customers;
    }
}