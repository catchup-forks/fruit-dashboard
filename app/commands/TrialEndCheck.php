<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class TrialEndCheck extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'check:trial';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Checks trial period end for each user who is not already subscribed';

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

		foreach ($users as $user) 
		{
			if($user->isTrialEnded())
			{
				if ($user->plan == 'trial')
				{ 
					// this is the first time we are checking it
					$user->plan = 'trial_ended';
	            	$user->save();
					
					// create intercom event
					IntercomHelper::trialEnded($user,'now');

					// send email

				} 
			}

			if($user->trialWillEndExactlyInDays(3)) 
			{
				// create intercom event
				IntercomHelper::trialWillEnd($user,3);
				
				// send email
				$data = array('user' => $user);
				// Mailman::make('emails.trial.trialWillEnd')
				// 	->with($data)
				// 	->to($user->email)
				// 	->subject('[Fruit Analytics] Your free trial is ending.')
				// 	->send();
			}

			if($user->trialWillEndExactlyInDays(-1))
			{
				// create intercom event
				IntercomHelper::trialEnded($user,'1-day-ago');

				// send email
				$data = array('user' => $user);
				// Mailman::make('emails.trial.trialEndedFirst')
				// 	->with($data)
				// 	->to($user->email)
				// 	->subject('[Fruit Analytics] Your free trial is ended')
				// 	->send();
			}
			if($user->trialWillEndExactlyInDays(-7))
			{
				// create intercom event
				IntercomHelper::trialEnded($user,'7-days-ago');

				// send email
				$data = array('user' => $user);
				// Mailman::make('emails.trial.trialEndedSecond')
				// 	->with($data)
				// 	->to($user->email)
				// 	->subject('[Fruit Analytics] ')
				// 	->send();
			}
			if($user->trialWillEndExactlyInDays(-14))
			{
				/// create intercom event
				IntercomHelper::trialEnded($user,'14-days-ago');

				// send email
				$data = array('user' => $user);
				// Mailman::make('emails.trial.trialEndedThird')
				// 	->with($data)
				// 	->to($user->email)
				// 	->subject('[Fruit Analytics]')
				// 	->send();
			}
		}
	}
}
