<?php


/**
 * --------------------------------------------------------------------------
 * PaymentController: Handles the pricing, subscriptions and payment related sites
 * --------------------------------------------------------------------------
 */
class PaymentController extends BaseController
{
    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * getPlansAndPricing
     * --------------------------------------------------
     * @return Renders the Plans and Pricing page
     * --------------------------------------------------
     */
    public function getPlansAndPricing() {
        /* Get all plans */
        $plans = Plan::all();

        /* Render the page */
        return View::make('payment.plans', array(
            'plans' => $plans,
        ));
    }


    /**
     * postSubscribe
     * --------------------------------------------------
     * Subscribes the user to the selected plan.
     * @return Redirects to the dashboard
     * --------------------------------------------------
     */
    public function postSubscribe($planId) {
        /* Get the Plan */
        $plan = Plan::find($planId);

        /* Check for payment_method_nonce in input */
        if (!Input::has('payment_method_nonce')) {
            return Redirect::route('payment.plan')
                ->with('error', "Something went wrong with your request, please try again.");
        }

        $subscription = new Subscription(array('status' => 'active'));
        $dt = Carbon::now();
        $subscription->current_period_start = $dt;
        switch ($plan->interval) {
            /* Braintree only supports month */
            case 'permanent': $subscription->current_period_end = NULL; break;
            case 'month': $subscription->current_period_end = $dt->diffinMonths($dt->copy()->addMonth()); break;
            default: ;
        }
        $subscription->user()->associate(Auth::user());
        $subscription->plan()->associate($plan);
        $subscription->save();

        /* Creating braintree subscription, if there is an associated plan */
        if ($plan->plan_id) {
            try {
                $subscription->commit(Input::get('payment_method_nonce'));
            } catch (AlreadyConnected $e)  {
                return Redirect::route('dashboard.dashboard')
                    ->with('error',"You are already subscribed to that plan.");
            } catch (Exception $e) {
                return Redirect::route('payment.plan')
                    ->with('error',"Couldn't process your subscription, try again later.");

            }
        }

        /* Render the page */
        return Redirecet::route('dashboard.dashboard', array(
            ));
    }

    /**
     * --------------------------------------------------
     * @todo Clean the code below
     * --------------------------------------------------
     */
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
                    // $email = Mailman::make('emails.payment.upgrade')
                    //  ->to($user->email)
                    //  ->subject('Upgrade')
                    //  ->send();
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
        else {
            return Redirect::route('payment.plan')
                ->with('error',"Notoken.");
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
                // $email = Mailman::make('emails.payment.downgrade')
                //  ->to($user->email)
                //  ->subject('Downgrade')
                //  ->send();
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

    // change user plan to trial
    public function doTrial ()
    {
        $user = Auth::user();
        $user->plan = 'trial';
        $user->trial_started = Carbon::now();
        $user->save();

        return Redirect::route('payment.plan')
            ->with('success','Trial has started!');
    }


}