<?php
use Stripe\Subscription;
use Stripe\Plan;
use Stripe\Error\Authentication;

class DevController extends Controller
{
    public function showTesting() {

        $user = User::find(1);

        // Looking for errors.
        if (Input::get('error') !== null) {
            Log::info('Stripe authentication failed for user ' + Input::get('error_description'));
        }
        $messages = array();
        // Looking for code.
        if (Input::get('code') !== null) {

            // Retrieving tokens.
            $stripe = new StripeHelper($user);
            try {
                $stripe->getTokens(Input::get('code'));
            } catch (StripeConnectFailed $e) {
                array_push($messages, $e->getMessage());
                Log::info($e->getMessage());
            }
        }

        // Return.
        return View::make('connectstripe')
            ->with('stripeConnectURI', StripeHelper::getStripeConnectURI())
            ->with('stripeData', array(
                'subscriptions' => StripeSubscription::all(),
                'plans'         => StripePlan::all()))
            ->withErrors($messages);
    }

    public function showGetStripeData() {
        $user = User::find(1);

        $stripe = new StripeHelper($user);
        $stripe->updateSubscriptions();

        return Redirect::route('dev.testing_page');
    }
}
