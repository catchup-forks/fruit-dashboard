<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CheckPaymentPastDue extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'payment:check';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Checks for every user if payment is overdue, and makes necessary actions';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$users = User::all();
		
		// helpers for logging
		$downgrade = 0;
		$upgrade = 0;
		$paying = 0;
		$notPaying = 0;
		
		foreach ($users as $user) 
		{
			if ($user->id == 1)
			{
				continue;
			}
			// if the user has any subscriptions
			if($user->plan != 'free')
			{
				if ($user->paymentStatus == 'overdue')
				{
					$notPaying++;
					// get the users subscription status
					$collection = Braintree_Subscription::search(array(
						Braintree_SubscriptionSearch::id()->is($user->subscriptionId),
						Braintree_SubscriptionSearch::status()->is(Braintree_Subscription::ACTIVE)
					));
					if($collection->maximumCount() > 0)
					{
						$subscription = $collection->firstItem();
						// upgrade and alert the user

						$user->paymentStatus = 'ok';
						$user->save();

						$upgrade++;

						// send email to the user about the upgrade
						$data = array();
						// $email = Mailman::make('emails.payment.upgrade')
						// 	->with($data)
						// 	->to($user->email)
						// 	->subject('Upgrade')
						// 	->send();
					}

				} else {
					$paying++;
					// get user's subscription if its overdue
					$collection = Braintree_Subscription::search(array(
						Braintree_SubscriptionSearch::id()->is($user->subscriptionId),
						Braintree_SubscriptionSearch::status()->is(Braintree_Subscription::PAST_DUE)
						// more versatile search would be with Braintree_SubscriptionSearch::daysPastDue(int) 
					));

					// search returns a collection, but 
					// we now there is maximum one such subscription
					if($collection->maximumCount() > 0)
					{
						$subscription = $collection->firstItem();
						// downgrade and alert user
						$user->paymentStatus = 'overdue';
						$user->save();
						$downgrade++;

						// send email to the user about the downgrade
						// FIXME - other email?
						$plans = Braintree_Plan::all();
						$foundPlan = null;
						foreach ($plans as $plan) {
							if($plan->id == $user->plan)
							{
								$foundPlan = $plan;
							}
						}
						
						$data = array(
							'plan' => $foundPlan,
							);

						// $email = Mailman::make('emails.payment.downgrade')
						// 	->with($data)
						// 	->to($user->email)
						// 	->subject('Downgrade')
						// 	->send();
						// FIXME - should the subscription be cancelled?
					
					} // /if - overdue subscription found
				} // /if - user was downgraded before
			} // /if - user is paying
		} // /foreach - all users

		Log::info('New overdue subscriptions found: '.$downgrade.' out of '.$paying);
		Log::info('New comeback subscriptions found: '.$upgrade.' out of '.$notPaying);
	}
}
