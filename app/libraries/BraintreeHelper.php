<?php

use Abf\Event;      // needed because of conflicts with Laravel and Stripe

class BraintreeHelper {

	public static function saveSecondaryEvent($notification)
	{

	}

//===============================================================================

	/**
	* Format braintree event type to stripe event type
	* @param string - braintree event type
	*
	* @return string - corresponding stripe event type
	*/

	public static function setEventType($kind)
	{	
		switch ($kind) 
		{
	    	case 'subscription_expired':
	    		return 'customer.subscription.updated';
	    		break;

	    	case 'subscription_canceled':
	    		return 'customer.subscription.deleted';
	    		break;

	    	case 'subscription_went_active':
	    		return 'customer.subscription.created';
	    		break;

	    	case 'subscription_trial_ended':
	    		return 'customer.subscription.trial_ended';
	    		break;

	    	case 'subscription_charged_successfully':
	    		return 'charge.succeeded';
	    		break;

	    	case 'subscription_charged_unsuccessfully':
	    		return 'charge.failed';
	    		break;

	    	case 'subscription_went_past_due':
	    		return 'customer.subscription.updated';
	    		break;
	    	
	    	case 'dispute_opened':
	    		return 'charge.dispute.created';
	    		break;

	    	case 'dispute_lost':
	    		return 'charge.dispute.funds_withdrawn';
	    		break;

	    	case 'dispute_won':
	    		return 'charge.dispute.funds_reinstated';
	    		break;

	    	case 'disbursement':
	    		return 'charge.succeeded';
	    		break;

	    	default:
	    		throw new Exception("New event type", 1);
	       		break;
	    }
	}

//===============================================================================

	/**
	* create new formatted array, from the braintree object
	* @param object - braintree notification object
	* @param string - type of the event (in stripe format)
	* @param object - user
	* 
	* @return array - formatted for stripe format
	*/

	public static function convertObjectFormat($notification, $type, $user)
	{
		$object = array();

		switch ($type) {
			case 'customer.subscription.trial_ended':
			case 'customer.subscription.created':
			case 'customer.subscription.deleted':

				// get the plan the user is subscribed for
				$plan = self::getPlan($notification->subscription->planId, $user);

				$object['object']['plan'] = array(
					'currency' 	=> $plan->currencyIsoCode,
					'interval' 	=> 'month',
					'name'		=> $plan->name,
					'id'		=> $plan->id,
					'amount'	=> $notification->subscription->price * 100 //braintree prices are in float, e.g: 9.99
				);
				break;
			case 'charge.succeeded':
			case 'charge.failed':
				switch ($notification->kind)
				{
					case 'disbursement':
						$transaction = self::getTransaction($notification->disbursement->transactionIds[0] ,$user);
						$object['object'] = array(
							'id' 		=> $notification->disbursement->id,
							'amount' 	=> $notification->disbursement->amount * 100,
							'currency'	=> $transaction->currencyIsoCode,
							'kind'		=> 'disbursement',
							'card'		=> array(
											'name' => $transaction->billing['firstName'].' '.$transaction->billing['lastName'])
						);
						break;
					case 'subscription_charged_successfully':
					case 'subscription_charged_unsuccessfully':
						$sub = $notification->subscription;
						$transaction = $sub->transactions[0];

						$plan = self::getPlan($sub->planId, $user);
						$object['object'] = array(
							'id'		=> $sub->id,
							'amount'	=> $sub->price * 100,
							'currency'  => $plan->currencyIsoCode,
							'kind'		=> 'subscription',
							'card'		=> array(
											'name' => $transaction->billing['firstName'].' '.$transaction->billing['lastName'])
						);
						break;
					default:
						break;
				}
				break;
			default:
				break;
		}

		return $object;
	}

//===============================================================================

	/**
	* calculate and save metrics on connect
	* @param object - user
	*/

	public static function firstTime($user)
	{
		$metrics = Metric::where('date', Carbon::now()->format('Y-m-d'))
							->where('user',$user->id)
							->first();
		$customers = self::getCustomers($user);
		$subscriptions = self::getSubscriptions($user);
		
		if ($metrics)
		{
			// we already have data for the metric, update it
			$metrics->mrr 	= self::getMRR($user, $subscriptions,$metrics->mrr);
			$metrics->au 	= self::getAU($user, $customers,$metrics->au);

			$metrics->arr 	= $metrics->mrr * 12;
			$metrics->arpu 	= $metrics->au != 0 ? round($metrics->mrr / $metrics->au) : null;
		} else {
			// we dont have any data yet
			$metrics = new Metric;
			
			$metrics->user = $user->id;
			$metrics->date = Carbon::now()->format('Y-m-d');

			$metrics->mrr 	= self::getMRR($user, $subscriptions);
			$metrics->au 	= self::getAU($user, $customers);
			
			$metrics->arr 	= $metrics->mrr * 12;
			$metrics->arpu 	= $metrics->au != 0 ? round($metrics->mrr / $metrics->au) : null;
            
            $metrics->uc 	= null;
			$metrics->cancellations = 0;
			$metrics->monthlyCancellations = null;

		}
					
		$metrics->save();
	}

//===============================================================================

	// ------------------------------------------
	// Braintree wrappers
	// ------------------------------------------	

	public static function setBraintreeCredentials($user)
	{
		Braintree_Configuration::environment($user->btEnvironment);
    	Braintree_Configuration::merchantId($user->btMerchantId);
    	Braintree_Configuration::publicKey($user->btPublicKey);
    	Braintree_Configuration::privateKey($user->btPrivateKey);
	}


//===============================================================================

	/**
	* wrapper for getting plan from braintree
	* @param string - id of the plan
	* @param object - user
	*
	* @return object - the searched plan OR null if not found
	*/

	private static function getPlan($planId, $user)
	{
		// setup braintree for querry

		self::setBraintreeCredentials($user);

    	$plans = Braintree_Plan::all();

        // find the correct plan to show
        // no way currently to get only one plan
        foreach ($plans as $plan) 
        {
            if($plan->id == $planId)
            {
                return $plan;
            }
        }

        return null;
	}

//===============================================================================

	/**
	* wrapper for getting customers from braintree
	* @param object - user
	* @param string - id of the customer (default null)
	*
	* @return object - the searched customers OR null if not found OR collection of all customers
	*/

	private static function getCustomers($user, $customerId = null)
	{
		self::setBraintreeCredentials($user);

    	$returnVariable = null;

    	if($customerId)
    	{
    		try
    		{
	    		$returnVariable = Braintree_Customer::find($customerId);
       		} catch (Exception $e)
       		{
       			return null;
       		}
    	} else {
    		$returnVariable = Braintree_Customer::all();
    	}

    	return $returnVariable;
	}

//===============================================================================

	/**
	* wrapper for getting subscriptions from braintree
	* @param object - user
	* @param string - id of the subscription (default null)
	*
	* @return object - the searched subscriptions OR null if not found OR collection of subscriptions
	*/

	private static function getSubscriptions($user, $subscriptionId = null)
	{
		self::setBraintreeCredentials($user);

    	$returnVariable = null;

    	if($subscriptionId)
    	{
    		try 
    		{
    			$returnVariable = Braintree_Subscription::find($subscriptionId);
    		} catch (Exception $e)
    		{
    			return null;
    		} 
    	} else {
    		$returnVariable = Braintree_Subscription::search(array(
    			Braintree_SubscriptionSearch::status()->in(
    				array(
    					Braintree_Subscription::ACTIVE,
    					Braintree_Subscription::PAST_DUE,
    					Braintree_Subscription::PENDING,
    					)
    				)
    			)
    		);
    	}

    	return $returnVariable;
	}

//===============================================================================

	/**
	* wrapper for getting subscriptions from braintree
	* @param string - id of the transaction
	* @param object - user
	*
	* @return object - the searched transaction
	*/

	private static function getTransaction($transactionId,$user)
	{
		self::setBraintreeCredentials($user);

		$transaction = Braintree_Transaction::find($transactionId);

		return $transaction;
	}

//===============================================================================

	// ------------------------------------------
	// Calculators
	// ------------------------------------------

	private static function getMRR($user, $subscriptions, $previousValue = 0)
	{
		$mrr = $previousValue;

		foreach ($subscriptions as $subscription) 
		{
			$mrr += round($subscription->price * 100);
		}

		// needs a new custom event so the calculator runs normal
		$newEvent = new Event;

		$newEvent->date                 = Carbon::now()->format('Y-m-d');
        $newEvent->eventID              = str_random(32);
        $newEvent->user                 = $user->id;
        $newEvent->created              = Carbon::now();
        $newEvent->provider             = 'connect';
        $newEvent->type                 = 'customer.subscription.created';
        $newEvent->object               = json_encode(array('plan' => array(
        									'amount' 	=> $mrr - $previousValue,
        									'interval'	=> 'month')));
        $newEvent->previousAttributes   = null;

        $newEvent->save();
                     
		return $mrr;
	}

//===============================================================================

	private static function getAU($user, $customers,$previousValue = 0)
	{
		$au = $previousValue;

		foreach ($customers as $customer) 
		{
      foreach ($customer->paymentMethods() as $paymentMethod) 
      {
        if (isset($paymentMethod->_attributes['subscriptions']))
        {        
          foreach ($paymentMethod->_attributes['subscriptions'] as $subscription) 
          {        
            if ($subscription->status == Braintree_Subscription::ACTIVE)
            {
            	$au++;

							// needs a new custom events so the calculator runs normal
            	$newEvent = new Event;

							$newEvent->date                 = Carbon::now()->format('Y-m-d');
			        $newEvent->eventID              = str_random(32);
			        $newEvent->user                 = $user->id;
			        $newEvent->created              = Carbon::now();
			        $newEvent->provider             = 'connect';
			        $newEvent->type                 = 'customer.created';
			        $newEvent->object               = '{}';
			        $newEvent->previousAttributes   = null;

			        $newEvent->save();
            } // /if active
          } // /foreach subscription            
        } // /if isset
      } // /foreach paymentMethod     
    } // /foreach customer

		return $au;
	}

//===============================================================================

	// Get the plan_name => plan dictionary for all plans
	public static function getPlanDictionary($userId = null)
	{
		// we are okay, if we search through our plans,
		// but we have to set users credentials, if
		// we go through our user's plans
		if ($userId)
		{
			self::setBraintreeCredentials(User::find($userId));
		}

		$planDict = array();

		$plans = Braintree_Plan::all();

		// find the correct plan to show
		// no way currently to get only one plan
		foreach ($plans as $plan) 
		{
			$planDict[snake_case(camel_case($plan->name))] = $plan;
		}
		return $planDict;
	}

//===============================================================================

	// Get the plan for a given id
	public static function getPlanById($id, $userId = null)
	{
		// we are okay, if we search through our plans,
		// but we have to set users credentials, if
		// we go through our user's plans
		if ($userId)
		{
			self::setBraintreeCredentials(User::find($userId));
		}

		$plans = Braintree_Plan::all();

		foreach ($plans as $plan) {
			if($plan->id == $id)
			{
				return $plan;
			}
		}
		return null;
	}


	public static function disconnect($user){
		$user->btPrivateKey = null;
		$user->btPublicKey = null;
		$user->btEnvironment = null;
		$user->btMerchantId = null;

		$user->btWebhookId = null;
		$user->btWebhookConnected = false;
		$user->save();

		return true;

	} # /function disconnect


}