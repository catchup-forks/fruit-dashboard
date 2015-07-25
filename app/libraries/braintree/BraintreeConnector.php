<?php

/**
* --------------------------------------------------------------------------
* BraintreeConnector:
*       Wrapper functions for Braintree connection
* Usage:
*       Connect the user by calling generateAccessToken()
        with validated input.
        If the user has an access_token, use the connect() method.
* --------------------------------------------------------------------------
*/

class BraintreeConnector
{
    /* -- Class properties -- */
    private $user;
    public static $authFields = array('publicKey', 'privateKey', 'merchantID', 'environment');

    /* -- Constructor -- */
    function __construct($user) {
        $this->user = $user;
    }
    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * getAuthFields
     * --------------------------------------------------
     * Returning the static authFields variable.
     * @return authFields
     * --------------------------------------------------
     */
    public function getAuthFields() {
        return static::$authFields;
    }

    /**
     * Creating an access token.
     * --------------------------------------------------
     * Creating an 'access_token'
     * @param array $credentials
     * @return string - corresponding stripe event type
     * --------------------------------------------------
     */
    public function generateAccessToken($input) {
        // Populating access_token array.
        $credentials = array();
        foreach ($input as $key=>$value) {
            if (in_array($key, $this->getAuthFields())) {
                $credentials[$key] = $value;
            }
        }

        // Creating a Connection instance, and saving to DB.
        $connection = new Connection(array(
            'access_token'  => json_encode($credentials),
            'refresh_token' => '',
            'service'       => 'braintree',
        ));
        $connection->user()->associate($this->user);
        $connection->save();
    }

    /**
     * connect.
     * --------------------------------------------------
     * Connecting the user with our stored credentials.
     * @throws BraintreeNotConnected
     * --------------------------------------------------
     */
    public function connect() {
        /* Check valid connection */
        if (!$this->user->isBraintreeConnected()) {
            throw new BraintreeNotConnected();
        }

        $credentials = json_decode($this->user->connections()->where('service', 'braintree')->first()->access_token);

        Braintree_Configuration::environment($credentials['environment']);
        Braintree_Configuration::merchantId($credentials['merchantID']);
        Braintree_Configuration::publicKey($credentials['publicKey']);
        Braintree_Configuration::privateKey($credentials['privateKey']);
    }

    /**
     * disconnect
     * --------------------------------------------------
     * Disconnecting the user from braintree.
     * @throws BraintreeNotConnected
     * --------------------------------------------------
     */
    public function disconnect() {
        /* Check valid connection */
        if (!$this->user->isBraintreeConnected()) {
            throw new BraintreeNotConnected();
        }

        $this->user->connections()->where('service', 'braintree')->delete();
    }
}