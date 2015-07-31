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
        /* Deleting connection */
        $this->user->connections()->where('service', 'stripe')->delete();

        /* Deleting all widgets, plans, subscribtions */
        foreach ($this->user->widgets() as $widget) {
            if ($widget->descriptor->category == 'stripe') {

                /* Saving data while it is accessible. */
                $dataID = 0;
                if (!is_null($widget->data)) {
                    $dataID = $widget->data->id;
                }

                $widget->delete();

                /* Deleting data if it was present. */
                if ($dataID > 0) {
                    Data::find($dataID)->delete();
                }
            }
        }

        /* Deleting all plans. */
        foreach ($this->user->stripePlans as $stripePlan) {
            StripeSubscription::where('plan_id', $stripePlan->id)->delete();
            $stripePlan->delete();
        }
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
        $response = SiteFunctions::postUrl($url);

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

        /* Deleting all previos connections, and stripe widgets. */
        $this->user->connections()->where('service', 'stripe')->delete();

        /* Creating a Connection instance, and saving to DB. */
        $connection = new Connection(array(
            'access_token'  => $response['access_token'],
            'refresh_token' => $response['refresh_token'],
            'service'       => 'stripe',
        ));
        $connection->user()->associate($this->user);
        $connection->save();

        /* Creating custom dashboard. */
        $this->createDashboard();

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
        $response = SiteFunctions::postUrl($url);

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
     * createDashboard
     * --------------------------------------------------
     * Creating a dashboard dedicated to stripe widgets.
     * --------------------------------------------------
     */
    private function createDashboard() {
        /* Creating dashboard. */
        $dashboard = new Dashboard(array(
            'name'       => 'Stripe dashboard',
            'background' => TRUE,
            'number'     => $this->user->dashboards->max('number') + 1
        ));
        $dashboard->user()->associate($this->user);
        $dashboard->save();

        /* Adding widgets */
        $mrrWidget = new StripeMrrWidget(array(
            'position'  => '{"col":1,"row":1,"size_x":1,"size_y":1}',
            'state'     => 'active',
        ));

        $arrWidget = new StripeArrWidget(array(
            'position'  => '{"col":2,"row":1,"size_x":1,"size_y":1}',
            'state'     => 'active',
        ));

        $arpuWidget = new StripeArpuWidget(array(
            'position'  => '{"col":3,"row":1,"size_x":1,"size_y":1}',
            'state'     => 'active',
        ));

        /* Associating dashboard */
        $mrrWidget->dashboard()->associate($dashboard);
        $arrWidget->dashboard()->associate($dashboard);
        $arpuWidget->dashboard()->associate($dashboard);

        /* Saving widgets */
        $mrrWidget->save();
        $arrWidget->save();
        $arpuWidget->save();

        /* Creating data for the last 30 days. */
        $calculator = new StripeLastMonthCalculator($this->user);
        $lastMonthData = $calculator->getLastMonthData();

        $mrrWidget->data->raw_value = json_encode($lastMonthData['mrr']);
        $arrWidget->data->raw_value = json_encode($lastMonthData['arr']);
        $arpuWidget->data->raw_value = json_encode($lastMonthData['arpu']);

        $mrrWidget->data->save();
        $arrWidget->data->save();
        $arpuWidget->data->save();

        $mrrWidget->state = 'active';
        $arrWidget->state = 'active';
        $arpuWidget->state = 'active';

        /* Saving widgets */
        $mrrWidget->save();
        $arrWidget->save();
        $arpuWidget->save();

    }

} /* StripeConnector */
