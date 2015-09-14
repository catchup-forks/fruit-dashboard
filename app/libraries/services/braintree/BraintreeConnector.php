<?php

/**
* --------------------------------------------------------------------------
* BraintreeConnector:
*       Wrapper functions for Braintree connection
* Usage:
*       Connect the user by calling saveTokens()
*       with validated input.
*       If the user has an access_token, use the connect() method.
* --------------------------------------------------------------------------
*/

class BraintreeConnector extends GeneralServiceConnector
{
    /* -- Class properties -- */
    private static $authFields = array('publicKey', 'privateKey', 'merchantID', 'environment');

    protected static $service = 'braintree';

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
     * @param array $parameters
     * --------------------------------------------------
     */
    public function saveTokens(array $parameters=array()) {
        // Populating access_token array.
        $credentials = array();
        foreach ($parameters as $key=>$value) {
            if (in_array($key, $this->getAuthFields())) {
                $credentials[$key] = $value;
            }
        }

        $this->createConnection(json_encode($credentials), '');
    }

    /**
     * connect
     * --------------------------------------------------
     * Connecting the user with our stored credentials.
     * @throws BraintreeNotConnected
     * --------------------------------------------------
     */
    public function connect() {
        /* Check valid connection */
        if (!$this->user->isServiceConnected(static::$service)) {
            throw new BraintreeNotConnected();
        }

        $credentials = json_decode($this->getConnection()->access_token, 1);

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
        parent::disconnect();
        /* Deleting all plans. */
        foreach ($this->user->braintreePlans as $braintreePlan) {
            BraintreeSubscription::where('plan_id', $braintreePlan->id)->delete();
            $braintreePlan->delete();
        }
    }

    /**
     * populateData
     * --------------------------------------------------
     * Collecting the initial data from the service.
     * --------------------------------------------------
     */
    public function populateData() {
        Queue::push('BraintreePopulateData', array(
            'user_id' => $this->user->id
        ));
    }

} /* BraintreeConnector */