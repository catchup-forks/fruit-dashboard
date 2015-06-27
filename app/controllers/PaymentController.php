<?php


/*
|--------------------------------------------------------------------------
| PaymentController: Handles the payment related sites and stuff
|--------------------------------------------------------------------------
*/
class PaymentController extends BaseController
{

	// Renders Plans & Pricing page 
	public function showPlans()
	{
		$plans = Braintree_Plan::all();

		try {
			$customer = Braintree_Customer::find('fruit_analytics_user_'.Auth::user()->id);
		}
		catch(Braintree_Exception_NotFound $e) {
			// no such customer yet, lets make it

			$result = Braintree_Customer::create(array(
				'id'        => 'fruit_analytics_user_'.Auth::user()->id,
				'email'     => Auth::user()->email,
				'firstName' => Auth::user()->email,
			));
			if($result->success)
			{
				$customer = $result->customer;
			} else {
				// needs error handling
			}
		}

		// generate clientToken for the user to make payment
		$clientToken = Braintree_ClientToken::generate(array(
			"customerId" => $customer->id
		));
		
		
		return View::make('payment.plan',array(
			'plans' => $plans,
			'clientToken'   =>$clientToken,
		));
	}


	// Renders specific plan payment page
	public function showPayPlan($planName)
	{
		try {
			$customer = Braintree_Customer::find('fruit_analytics_user_'.Auth::user()->id);
		}
		catch(Braintree_Exception_NotFound $e) {
			// no such customer yet, lets make it

			$result = Braintree_Customer::create(array(
				'id'        => 'fruit_analytics_user_'.Auth::user()->id,
				'email'     => Auth::user()->email,
				'firstName' => Auth::user()->email,
			));
			if($result->success)
			{
				$customer = $result->customer;
			} else {
				// needs error handling
			}
		}

		// generate clientToken for the user to make payment
		$clientToken = Braintree_ClientToken::generate(array(
			"customerId" => $customer->id
		));
		
		$plans = BraintreeHelper::getPlanDictionary();

		return View::make('payment.payplan', array(
			'planName'      =>$plans[$planName]->name,
			'clientToken'   =>$clientToken,
		)); 
	}

	// Execute the payment process
	public function doPayPlan($planName)
	{
		if(Input::has('payment_method_nonce'))
		{

			$user = Auth::user();
			
			// lets see, if the user already has a subscripton
			if ($user->subscriptionId)
			{
				try
				{
					$result = Braintree_Subscription::cancel($user->subscriptionId);
				}
				catch (Exception $e)
				{
					return Redirect::route('payment.plan')
					->with('error',"Couldn't process subscription, try again later.");
				}
			}   
			
			$plans = BraintreeHelper::getPlanDictionary();

			// create the new subscription
			$result = Braintree_Subscription::create(array(
				'planId'                => $plans[$planName]->id,
				'paymentMethodNonce'    => Input::get('payment_method_nonce'),
			));
			
			if($result->success)
			{
				// update user plan to subscrition
				$user->plan = $plans[$planName]->id;
				$user->subscriptionId = $result->subscription->id;
				$user->paymentStatus = 'ok';
				$user->save();

				// send event to intercom about subscription
				IntercomHelper::subscribed($user,$plans[$planName]->name);

				// send email to the user
				try {
					$email = Mailman::make('emails.payment.upgrade')
						->to($user->email)
						->subject('Upgrade')
						->send();
				} catch (Exception $e)
				{
					Log::error('Upgrade email sending error');
					Log::info($e->getMessage());
					Log::info($user->email);
				}

				return Redirect::route('connect.connect')
					->with('success','Subscribed to '.$plans[$planName]->name);
			} else {
				return Redirect::route('payment.plan')
					->with('error',"Couldn't process subscription, try again later.");
			}
		}
	}

	// Execute the cancellation
	public function doCancelSubscription()
	{
		$user = Auth::user();

		if ($user->subscriptionId)
		{
			try
			{
				$result = Braintree_Subscription::cancel($user->subscriptionId);
			}
			catch (Exception $e)
			{
				Log::error("Couldn't process cancellation with subscription ID: ".$user->subscriptionId."(user email: ".$user->email);
				return Redirect::back()
					->with('error',"Couldn't process cancellation, try again later.");
			}

			$plan = BraintreeHelper::getPlanById($user->plan);

			$user->subscriptionId = '';
			$user->plan = 'free';
			$user->save();

			IntercomHelper::cancelled($user);

			try {
				$email = Mailman::make('emails.payment.downgrade')
					->to($user->email)
					->subject('Downgrade')
					->send();
			} catch (Exception $e)
			{
				Log::error('Downgrade email sending error');
				Log::info($e->getMessage());
				Log::info($user->email);
			}

			return Redirect::route('payment.plan')
				->with('success','Unsubscribed successfully');
		} else {
			Redirect::back()
				->with('error','No valid subscription');
		}
	}
}