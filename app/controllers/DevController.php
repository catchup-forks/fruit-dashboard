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
                Log::error($e->getMessage());
            }
        }
        /*
        $dashboard = new Dashboard(array(
            'name'       => 'High dashboard',
            'background' => TRUE
        ));
        $dashboard->user()->associate($user);
        $dashboard->save();
        $clockWidget = new ClockWidget(array(
            'settings' => '',
            'state'    => 'active',
            'position' => '{"size_x": 2, "size_y": 2, "row": 0, "col": 0}'
        ));
        $clockWidget->dashboard()
                ->associate($user->dashboards()->first());
        $clockWidget->save(); */
        Log::info(ClockWidget::all()->count());
        Log::info(QuoteWidget::all()->count());

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
        Log::info($stripe->calculateMRR(TRUE));

        return Redirect::route('dev.testing_page');
    }

    /* PROTOTYPE CONTROLLERS */
    public function showSignup() {

    }

    /* Login required at this point
       Sending the widgetdescriptors to the view.
    */
    public function showSelectPersonalWidgets() {
        return View::make('select_personal_widgets')
            ->with('personalWidgets', WidgetDescriptor::where('type', '!=', 'financial')
            ->get());
    }

}
