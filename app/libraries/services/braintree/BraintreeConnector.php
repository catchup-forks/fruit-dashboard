<?php

/**
* --------------------------------------------------------------------------
* BraintreeConnector:
*       Wrapper functions for Braintree connection
* Usage:
*       Connect the user by calling getTokens()
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
     * @param array $credentials
     * --------------------------------------------------
     */
    public function getTokens($input) {
        // Populating access_token array.
        $credentials = array();
        foreach ($input as $key=>$value) {
            if (in_array($key, $this->getAuthFields())) {
                Log::info($key . " " . $value);
                $credentials[$key] = $value;
            }
        }

        $this->createConenction(json_encode($credentials), '');

        /* Creating custom dashboard in the background. */
        Queue::push('BraintreeAutoDashboardCreator', array('user_id' => Auth::user()->id));
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
        if (!$this->user->isServiceConnected(static::$service)) {
            throw new BraintreeNotConnected();
        }

        $credentials = json_decode($this->user->connections()->where('service', static::$service)->first()->access_token, 1);

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
        if (!$this->user->isServiceConnected(static::$service)) {
            throw new BraintreeNotConnected();
        }

        $this->user->connections()->where('service', static::$service)->delete();

        /* Deleting all widgets, plans, subscribtions */
        foreach ($this->user->widgets() as $widget) {
            if ($widget->descriptor->category == static::$service) {

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
        foreach ($this->user->braintreePlans as $braintreePlan) {
            BraintreeSubscription::where('plan_id', $braintreePlan->id)->delete();
            $braintreePlan->delete();
        }
    }

} /* BraintreeConnector */