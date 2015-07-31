<?php
use Stripe\Subscription;
use Stripe\Plan;
use Stripe\Error\Authentication;

/**
 * --------------------------------------------------------------------------
 * DevelopmentController: Handles the authentication related sites
 * --------------------------------------------------------------------------
 */
class DevelopmentController extends Controller
{
    public function showGetStripeData() {
        $user = User::find(1);

        $stripe = new StripeHelper($user);
        Log::info($stripe->calculateMRR(TRUE));

        return Redirect::route('development.testing_page');
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

} /* DevelopmentController */
