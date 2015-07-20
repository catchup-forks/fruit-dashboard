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
    public function connect() {
        if (!$this->user->isStripeConnected()) {
            throw new StripeNotConnected();
        }

        // Getting access token from DB.
        $token = $this->user->connections()
            ->where('service', 'stripe')
            ->first()->access_token;

        // Setting API key.
        \Stripe\Stripe::setApiKey($token);
    }

    /**
     * Retrieving the access, and refresh tokens from authentication code.
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
            throw new StripeConnectFailed('Stripe connection error, please try again.', 1);
        }

        // Error handling.
        if (isset($response['error'])) {
            throw new StripeConnectFailed('Your connection expired, please try again.', 1);
        }

        // Deleting previous connnection.
        if ($this->user->isStripeConnected()) {
            $this->user->connections()->where('service', 'stripe')->delete();
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
     * Retrieving the access token from a refresh token.
     *
     * @param string $code The returned code by stripe.
     * @returns None
     * @throws StripeConnectFailed
    */
    public function getNewAccessToken() {
        // Building POST request.
        if (!$this->user->isStripeConnected()) {
            throw new StripeNotConnected();
        }
        $stripe_connection = $this->user->connections()->where('service', 'stripe')->first();
        $url= Config::get('constants.STRIPE_ACCESS_TOKEN_URI');
        $post_kwargs = 'client_secret=' . Config::get('constants.STRIPE_SECRET_KEY');
        $post_kwargs .= '&refresh_token=' . $stripe_connection->refresh_token;
        $post_kwargs .= '&grant_type=' . 'refresh_token';

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

        // Saving new token.
        $stripe_connection->access_token = $response['access_token'];
        $stripe_connection->save();

    }
    /**
     * Calculating the MRR for the user.
     *
     * @param $update, boolean Whether or not sync the db.
     * @returns float The value of the mrr.
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

    // -- Private section -- //

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
}