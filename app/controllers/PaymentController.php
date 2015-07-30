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
     * getPlans
     * --------------------------------------------------
     * @return Renders the Plans and Pricing page
     * --------------------------------------------------
     */
    public function getPlans() {
        /* Get all plans */
        $plans = Plan::all();

        /* Render the page */
        return View::make('payment.plans', ['plans' => $plans]);
    }

   /**
     * getSubscribe
     * --------------------------------------------------
     * @param (integer) ($planID) The requested Plan ID
     * @return Renders the subscription page and redirects on error
     * --------------------------------------------------
     */
    public function getSubscribe($planID) {
        /* Get the Plan */
        $plan = Plan::find($planID);

        /* Check if the user has the same plan */
        // if (Auth::user()->subscription->plan->id == $plan->id) {
        //     return Redirect::route('payment.plans')
        //         ->with(['success' => 'You have already been subscribed to the requested plan.']);
        // }

        /* Render the view */
        return View::make('payment.subscribe', ['plan' => $plan]);
    }



    /**
     * postSubscribe
     * --------------------------------------------------
     * @param (integer) ($planID) The requested Plan ID
     * @return Subscribes the user to the selected plan.
     * --------------------------------------------------
     */
    public function postSubscribe($planID) {
        /* Get the Plan */
        $plan = Plan::find($planID);

        /* Get the current subscription of the user */
        $subscription = Auth::user()->subscription;

        /* Check if the plan has been modified. Redirect if not */
        // if ($subscription->plan->id == $plan->id) {
        //     return Redirect::route('payment.plans')
        //         ->with(['success' => 'You have already been subscribed to the requested plan.']);
        // }

        /* Check if the new plan has Braintree plan_id. Redirect if not */
        if ($plan->braintree_plan_id == null) {
            return Redirect::route('payment.unsubscribe');
        }

        /* Check if the old plan has Braintree plan_id. Cancel the subscription if it has */
        if ($subscription->plan->braintree_plan_id != null) {
            /**
             * @todo Cancel current subscription on Braintree
             */
            //result = Braintree::Subscription.cancel("the_subscription_id")
        }

        /* Check for payment_method_nonce in input */
        if (!Input::has('payment_method_nonce')) {
            return Redirect::route('payment.subscribe', $plan->id)
                ->with('error', "Something went wrong with your request, please try again.");
        }

        /* Commit subscription */
        $result = $subscription->commitSubscribe(Input::get('payment_method_nonce'), $plan);

        /* check errors */
        if ($result['errors'] == FALSE) {
            /* Return with success */
            return Redirect::route('payment.subscribe', $planID)
                ->with('success', 'Your subscription was successfull.');
        } else {
            /* Return with errors */
            return Redirect::route('payment.subscribe', $planID)
                ->with('error', $result['messages']);
        }
    }

    /**
     * getUnsubscribe
     * --------------------------------------------------
     * @return Renders the Unsubscribe page and redirects on error
     * --------------------------------------------------
     */
    public function getUnsubscribe() {
        /* Render the view */
        return View::make('payment.unsubscribe');
    }

    /**
     * postUnsubscribe
     * --------------------------------------------------
     * @return Unsubscribes the user from the paid plans.
     * --------------------------------------------------
     */
    public function postUnsubscribe() {
        /* Render the view */
        return View::make('payment.unsubscribe');
    }

} /* PaymentController */
