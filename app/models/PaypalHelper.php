<?php

class PaypalHelper
{
    /**
     * Testing if the user has connected a paypal account
     * @param paypal key
     *
     * @return boolean
    */
    public static function isConnected($key)
    {
        // at this point validation like this is all right
        if (strlen($key) > 16) {
            // refreshtoken is longer longer than 16
            return True;
        }
        // no valid refreshtoken is stored
        return False;
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

    public static function getEvents($key)
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
	 * @param paypal key
	 *
	 * @return an array with the plans
	*/

    public static function getPlans($key)
    {
    	$out_plans = array();

		// tell paypal who we are
		// get the plans from paypal
		// build return array

		// returning object
        return $out_plans;
    }

     /**
     * Getting all the customers for the user
     * @param stripe key
     *
     * @return an array with the subscriptions
    */
    
    public static function getCustomers($key)
    {
    	$out_customers = array();

		// tell paypal who we are
		// get the customers from paypal
		// build return array

		// returning object
        return $out_customers;
    }
}