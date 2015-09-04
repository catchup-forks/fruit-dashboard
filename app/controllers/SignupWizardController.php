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
            return Redirect::route('signup-wizard.financial-connections');

        /* Validator failed */
        } else {
            /* Render the page */
            return Redirect::route('signup-wizard.authentication')
                ->with('error', $validator->errors()->get(key($validator->invalid()))[0]);
        }
    }

    /**
     * anyFinancialConnections
     * --------------------------------------------------
     * @return Renders the financial connections step
     * --------------------------------------------------
     */
    public function anyFinancialConnections() {
        /* Render the page */
        return View::make('signup-wizard.financial-connections');
    }

    /**
     * anySocialConnections
     * --------------------------------------------------
     * @return Renders the personal widget setup step
     * --------------------------------------------------
     */
    public function anySocialConnections() {
        /* Render the page */
        return View::make('signup-wizard.social-connections');
    }

    /**
     * getPersonalWidgets
     * --------------------------------------------------
     * @return Renders the personal widget setup step
     * --------------------------------------------------
     */
    public function getPersonalWidgets() {
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
        /* Create the personal dashboard based on the inputs */
        $this->makePersonalAutoDashboard(Auth::user(), Input::all());

        /* Render the page */
        return Redirect::route('dashboard.dashboard');
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

        /* Save settings */
        $settings->save();

        /* Create default background for the user */
        $background = new Background;
        $background->user()->associate($user);
        $background->changeUrl();

        /* Save background */
        $background->save();

        /* Create default subscription for the user */
        $plan = Plan::getFreePlan();
        $subscription = new Subscription;
        $subscription->user()->associate($user);
        $subscription->plan()->associate($plan);
        $subscription->status = 'active';
        $subscription->trial_status = 'possible';
        $subscription->trial_start  = null;

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
        $dashboard = new Dashboard(array(
            'name'       => 'Personal dashboard',
            'background' => 'On',
            'number'     => Dashboard::where('user_id', $user->id)->max('number') + 1,
            'is_default' => TRUE
        ));
        $dashboard->user()->associate($user);

        /* Save dashboard object */
        $dashboard->save();

        /* Create clock widget */
        if (array_key_exists('widget-clock', $widgetdata)) {
            $clockwidget = new ClockWidget(array(
                'state'    => 'active',
                'position' => '{"row":1,"col":3,"size_x":8,"size_y":3}',
            ));
            $clockwidget->dashboard()->associate($dashboard);

            /* Save clock widget object */
            $clockwidget->save();
        }

        /* Create greetings widget */
        if (array_key_exists('widget-greetings', $widgetdata)) {
            $greetingswidget = new GreetingsWidget(array(
                'state'    => 'active',
                'position' => '{"row":4,"col":3,"size_x":8,"size_y":1}',
            ));
            $greetingswidget->dashboard()->associate($dashboard);

            /* Save greetings widget object */
            $greetingswidget->save();
        }

        /* Create quote widget */
        if (array_key_exists('widget-quote', $widgetdata)) {
            $quotewidget = new QuoteWidget(array(
                'state'    => 'active',
                'position' => '{"row":11,"col":1,"size_x":12,"size_y":1}',
            ));
            $quotewidget->dashboard()->associate($dashboard);

            /* Save quote widget object */
            $quotewidget->setSetting('type', 'inspirational');
            $quotewidget->collectData();
        }

        /* Create second dashboard */
        $dashboard2 = new Dashboard(array(
            'name'       => 'Second dashboard',
            'background' => 'On',
            'number'     => Dashboard::where('user_id', $user->id)->max('number') + 1
        ));
        $dashboard2->user()->associate($user);

        /* Save dashboard object */
        $dashboard2->save();

        /* Create text widgets */
        $textWidget = new TextWidget(array(
            'state'    => 'active',
            'position' => '{"col":2,"row":6,"size_x":6,"size_y":1}',
            'settings' => '{"text":"You can add a new widget by pressing the + sign at the bottom left."}'
        ));
        $textWidget->dashboard()->associate($dashboard2);

        /* Save text widget object */
        $textWidget->save();

        $textWidget2 = new TextWidget(array(
            'state'    => 'active',
            'position' => '{"col":7,"row":3,"size_x":6,"size_y":1}',
            'settings' => '{"text":"You can move & resize & delete widgets by hovering them."}'
        ));
        $textWidget2->dashboard()->associate($dashboard2);

        /* Save text widget object */
        $textWidget2->save();

        /* Return */
        return $dashboard;
    }

} /* SignupWizardController */