<?php

/**
 * --------------------------------------------------------------------------
 * SignupWizardController: Handles the signup process
 * --------------------------------------------------------------------------
 */
class SignupWizardController extends BaseController
{
    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * anySignup
     * --------------------------------------------------
     * @return Wrapper for the signup wizard entry point
     * --------------------------------------------------
     */
    public function anySignup() {
        /* Redirect to the first signup page */
        return Redirect::route('signup-wizard.authentication');
    }

    /**
     * getAuthentication
     * --------------------------------------------------
     * @return Renders the authentication step
     * --------------------------------------------------
     */
    public function getAuthentication() {
        /* Render the page */
        return View::make('signup-wizard.authentication');
    }

    /**
     * postAuthentication
     * --------------------------------------------------
     * @return Saves the user authentication data
     * --------------------------------------------------
     */
    public function postAuthentication() {
        /* Validation rules */
        $rules = array(
            'email' => 'required|email|unique:users',
            'password' => 'required|min:4',
        );

        /* Run validation rules on the inputs */
        $validator = Validator::make(Input::all(), $rules);

        /* Everything is ok */
        if (!$validator->fails()) {

            /* Create the user */
            $user = $this->createUser(Input::all());

            /* Log in the user*/
            Auth::login($user);

            /* Track event | SIGN UP */
            $tracker = new GlobalTracker();
            $tracker->trackAll('lazy', array(
                'en' => 'Sign up',
                'el' => Auth::user()->email)
            );

            /* Redirect to next step */
            return Redirect::route('signup-wizard.personal-widgets');

        /* Validator failed */
        } else {
            /* Render the page */
            return Redirect::route('signup-wizard.authentication')
                ->with('error', $validator->errors()->get(key($validator->invalid()))[0]);
        }
    }

    /**
     * getPersonalWidgets
     * --------------------------------------------------
     * @return Renders the personal widget setup step
     * --------------------------------------------------
     */
    public function getPersonalWidgets() {
        /* Redirect if the user already has a dashboard (not a new user) */
        if (Auth::user()->dashboards()->count()) {
            return Redirect::route('dashboard.dashboard');
        }

        /* Render the page */
        return View::make('signup-wizard.personal-widgets');
    }

    /**
     * postPersonalWidgets
     * --------------------------------------------------
     * @return Saves the user personal widget settings
     * --------------------------------------------------
     */
    public function postPersonalWidgets() {
        /* Check for authenticated user, redirect if nobody found */
        if (!Auth::check()) {
            return Redirect::route('signup');
        }

        /* Create the personal dashboard based on the inputs */
        $this->makePersonalAutoDashboard(Auth::user(), Input::all());

        /* Render the page */
        return Redirect::route('signup-wizard.financial-connections');
    }

    /**
     * getFinancialConnections
     * --------------------------------------------------
     * @return Renders the financial connections step
     * --------------------------------------------------
     */
    public function getFinancialConnections() {
        /* Render the page */
        return View::make('signup-wizard.financial-connections');
    }

    /**
     * postFinancialConnections
     * --------------------------------------------------
     * @return Saves the financial connection setting
     * --------------------------------------------------
     */
    public function postFinancialConnections() {
        /* Stripe connection */
        if(Input::get('stripe-connect', FALSE)) {
        }

        /* Braintree connection */
        if(Input::get('braintree-connect', FALSE)) {
        }

        /* Redirect to the same page */
        return Redirect::route('signup-wizard.financial-connections');
    }

    /**
     * getSocialConnections
     * --------------------------------------------------
     * @return Renders the personal widget setup step
     * --------------------------------------------------
     */
    public function getSocialConnections() {
        /* Render the page */
        return View::make('signup-wizard.social-connections');
    }

    /**
     * ================================================== *
     *                   PRIVATE SECTION                  *
     * ================================================== *
    */

    /**
     * createUser
     * creates a new User object (and related models)
     * from the POST data
     * --------------------------------------------------
     * @param (array) ($userdata) Array with the user data
     * @return (User) ($user) The new User object
     * --------------------------------------------------
     */
    private function createUser($userdata) {
        /* Create new user */
        $user = new User;

        /* Set authentication info */
        $user->email    = $userdata['email'];
        $user->password = Hash::make($userdata['password']);
        $user->name     = $userdata['name'];

        /* Save the user */
        $user->save();

        /* Create default settings for the user */
        $settings = new Settings;
        $settings->user()->associate($user);
        $settings->newsletter_frequency = 0;
        $settings->background_enabled   = true;

        /* Save settings */
        $settings->save();

        /* Create default subscription for the user */
        $plan = Plan::getFreePlan();
        $subscription = new Subscription;
        $subscription->user()->associate($user);
        $subscription->plan()->associate($plan);
        $subscription->status = 'active';
        $subscription->trial_status = 'possible';
        $subscription->trial_start = null;

        /* Save subscription */
        $subscription->save();

        /* Return */
        return $user;
    }

    /**
     * makePersonalAutoDashboard
     * creates a new Dashboard object and personal widgets
     * from the POST data
     * --------------------------------------------------
     * @param (User) ($user) The current user
     * @param (array) ($widgetdata) Personal widgets data
     * @return (Dashboard) ($dashboard) The new Dashboard object
     * --------------------------------------------------
     */
    private function makePersonalAutoDashboard($user, $widgetdata) {
        /* Create new dashboard */
        $dashboard = new Dashboard;

        $dashboard->user_id     = $user->id;
        $dashboard->name        = 'My personal dashboard';
        $dashboard->background  = 'On';
        $dashboard->number      = 0;

        /* Save dashboard object */
        $dashboard->save();

        /* Create clock widget */
        if (array_key_exists('widget-clock', $widgetdata)) {
            $clockwidget = new ClockWidget;

            $clockwidget->dashboard()->associate($dashboard);
            $clockwidget->state         = 'active';
            $clockwidget->position      = '{"row":1,"col":3,"size_x":8,"size_y":3}';

            /* Save clock widget object */
            $clockwidget->save();
        }

        /* Create greetings widget */
        if (array_key_exists('widget-greetings', $widgetdata)) {
            $greetingswidget = new GreetingsWidget;

            $greetingswidget->dashboard()->associate($dashboard);
            $greetingswidget->state         = 'active';
            $greetingswidget->position      = '{"row":4,"col":3,"size_x":8,"size_y":1}';

            /* Save greetings widget object */
            $greetingswidget->save();

        }

        /* Create quote widget */
        if (array_key_exists('widget-quote', $widgetdata)) {
            $quotewidget = new QuoteWidget;

            $quotewidget->dashboard()->associate($dashboard);
            $quotewidget->state         = 'active';
            $quotewidget->position      = '{"row":11,"col":1,"size_x":12,"size_y":1}';

            /* Save quote widget object */
            $quotewidget->save();
            $quotewidget->collectData();
        }

        $dashboard2 = new Dashboard(array(
            'name'       => 'Second dashboard',
            'background' => TRUE,
            'number'     => 1
        ));
        $dashboard2->user()->associate($user);
        $dashboard2->save();

        /* Create text widgets */
        $textWidget = new TextWidget(array(
            'state'    => 'active',
            'position' => '{"col":2,"row":6,"size_x":6,"size_y":1}',
            'settings' => '{"text":"You can add a new widget by pressing the + sign at the bottom left."}'
        ));
        $textWidget->dashboard()->associate($dashboard2);
        $textWidget->save();

        $textWidget2 = new TextWidget(array(
            'state'    => 'active',
            'position' => '{"col":7,"row":3,"size_x":6,"size_y":1}',
            'settings' => '{"text":"You can move & resize & delete widgets by hovering them."}'
        ));
        $textWidget2->dashboard()->associate($dashboard2);
        $textWidget2->save();

        /* Return */
        return $dashboard;
    }

} /* SignupWizardController */