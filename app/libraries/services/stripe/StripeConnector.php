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

class StripeConnector extends GeneralServiceConnector
{
    protected static $service = 'stripe';
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
        /* Set up API key */
        \Stripe\Stripe::setApiKey($this->getConnection()->access_token);
    }

    /**
     * disconnect
     * --------------------------------------------------
     * disconnecting the user from stripe.
     * @throws stripenotconnected
     * --------------------------------------------------
     */
    public function disconnect() {
        parent::disconnect();
        /* deleting all plans. */
        foreach ($this->user->stripePlans as $stripeplan) {
            StripeSubscription::where('plan_id', $stripeplan->id)->delete();
            $stripeplan->delete();
        }
    }

    /**
     * getTokens
     * --------------------------------------------------
     * Retrieving the access, and refresh tokens from authentication code.
     * @param array $parameters
     * @return None
     * @throws StripeConnectFailed
     * --------------------------------------------------
     */
    public function getTokens(array $parameters=array()) {
        $code = $parameters['auth_code'];
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

        $this->createConnection($response['access_token'], $response['refresh_token']);
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
        if (!$this->user->isServiceConnected(static::$service)) {
            throw new StripeNotConnected();
        }

        /* Build and send POST request */
        $stripe_connection = $this->getConnection();
        $url = $this->buildTokenPostUriFromRefreshToken($stripe_connection->refresh_token);

        /* Get response */
        $response = SiteFunctions::postUrl($url);

        /* Invalid/No answer from Stripe. */
        if ($response === null) {
            throw new StripeConnectFailed('Stripe server error, please try again.', 1);
        }

        /* Error handling. */ if (isset($response['error'])) {
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


} /* StripeConnector */
