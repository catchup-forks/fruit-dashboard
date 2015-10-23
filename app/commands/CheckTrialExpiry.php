<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CheckTrialExpiry extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'trial:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the trial period end for all users, and fires events if the period has ended.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        /* Iterate through the users */
        foreach (User::all() as $user) {
            /* Get the subscription */
            $subscription = $user->subscription;
            
            /* Catch if the user has no subscription (db seed error) */
            if ($subscription == null) {
                continue;
            }

            /* Get just expired subscriptions */
            if (($subscription->trial_status == 'active') and
                ($subscription->getDaysRemainingFromTrial() <= 0)) {
            
                /* Update status in db */
                $subscription->changeTrialState('ended');

                /* Track event | TRIAL ENDED */
                $tracker = new GlobalTracker();
                $tracker->trackAll('lazy', array(
                    'en' => 'Trial ended',
                    'el' => $user()->email)
                );
            }
        }
    }
}