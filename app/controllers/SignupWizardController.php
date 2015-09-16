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
     * @return Renders the social connections setup step
     * --------------------------------------------------
     */
    public function anySocialConnections() {
        /* Render the page */
        return View::make('signup-wizard.social-connections');
    }

    /**
     * anyWebAnalyticsConnections
     * --------------------------------------------------
     * @return Renders the web analytics connections setup step
     * --------------------------------------------------
     */
    public function anyWebAnalyticsConnections() {
        /* Render the page */
        return View::make('signup-wizard.web-analytics-connections');
    }

    /**
     * getPersonalWidgets
     * --------------------------------------------------
     * @return Renders the personal widget setup step
     * --------------------------------------------------
     */
    public function anyPersonalWidgets() {
        /* Make personal dashboard automatically */
        $this->makePersonalAutoDashboard(Auth::user(), 'auto', null);
        /* Redirect to the dashboard */
        return Redirect::route('dashboard.dashboard', array('tour' => TRUE));
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
        $user->createDefaultProfile();

        /* Return */
        return $user;
    }

    /**
     * makePersonalAutoDashboard
     * creates a new Dashboard object and personal widgets
     * from the POST data
     * --------------------------------------------------
     * @param (User)    ($user) The current user
     * @param (string)  ($mode) 'auto' or 'manual'
     * @param (array)   ($widgetdata) Personal widgets data
     * @return (Dashboard) ($dashboard) The new Dashboard object
     * --------------------------------------------------
     */
    private function makePersonalAutoDashboard($user, $mode, $widgetdata) {
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
        if (($mode == 'auto') or
            array_key_exists('widget-clock', $widgetdata)) {
            $clockwidget = new ClockWidget(array(
                'state'    => 'active',
                'position' => '{"row":1,"col":3,"size_x":8,"size_y":3}',
            ));
            $clockwidget->dashboard()->associate($dashboard);

            /* Save clock widget object */
            $clockwidget->save();
        }

        /* Create greetings widget */
        if (($mode == 'auto') or
            array_key_exists('widget-greetings', $widgetdata)) {
            $greetingswidget = new GreetingsWidget(array(
                'state'    => 'active',
                'position' => '{"row":4,"col":3,"size_x":8,"size_y":1}',
            ));
            $greetingswidget->dashboard()->associate($dashboard);

            /* Save greetings widget object */
            $greetingswidget->save();
        }

        /* Create quote widget */
        if (($mode == 'auto') or
            array_key_exists('widget-quote', $widgetdata)) {
            $quotewidget = new QuoteWidget(array(
                'state'    => 'active',
                'position' => '{"row":11,"col":1,"size_x":12,"size_y":1}',
            ));
            $quotewidget->dashboard()->associate($dashboard);

            /* Save quote widget object */
            $quotewidget->saveSettings(array('type' => 'inspirational'));
        }

        /* Return */
        return $dashboard;
    }

} /* SignupWizardController */