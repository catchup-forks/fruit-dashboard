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

            /* Track events */
            $tracker = new GlobalTracker();
            /* Track event | SIGN UP */
            $tracker->trackAll('lazy', array(
                'en' => 'Sign up',
                'el' => Auth::user()->email)
            );
            /* Track event | TRIAL STARTS */
            $tracker->trackAll('lazy', array(
                'en' => 'Trial starts',
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

        /* Render the page */
        return View::make('signup-wizard.authentication');
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
        return View::make('signup-wizard.financial-connections');
    }

    /**
     * getBraintreeConnect
     * --------------------------------------------------
     * @return Renders the braintree connect setup step
     * --------------------------------------------------
     */
    public function getBraintreeConnect() {
        /* Render the page */
        return View::make('signup-wizard.braintree-connect');
    }

    /**
     * postBraintreeConnect
     * --------------------------------------------------
     * @return Saves the user braintree connect settings
     * --------------------------------------------------
     */
    public function postBraintreeConnect() {
        // Validation.
        $rules = array(
            'publicKey'   => 'required',
            'privateKey'  => 'required',
            'merchantID'  => 'required',
            'environment' => 'required'
        );

        // Run the validation rules on the inputs.
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            // validation error -> sending back
            $failedAttribute = $validator->invalid();
            return Redirect::back()
                ->with('error', 'Please correct the form errors')
                ->withErrors($validator->errors())
                ->withInput(); // sending back data
        }

        $braintreeConnector = new BraintreeConnector(Auth::user());
        $braintreeConnector->generateAccessToken(Input::except('_token'));

        /* Render the page */
        return View::make('signup-wizard.financial-connections');
    }

    /**
     * getFinancialConnections
     * --------------------------------------------------
     * @return Renders the financial connections step
     * --------------------------------------------------
     */
    public function getFinancialConnections() {
        /* Connect stripe after OAuth if not connected */
        if (!Auth::user()->isStripeConnected()) {

            /* Access code if available */
            if (Input::get('code', FALSE)) {
                /* Create instance */
                $stripeconnector = new StripeConnector(Auth::user());

                /* Get tokens */
                try {
                    $stripeconnector->getTokens(Input::get('code'));

                /* Error handling */
                } catch (StripeConnectFailed $e) {
                    $messages = array();
                    array_push($messages, $e->getMessage());
                }

                /* Connect to stripe */
                $stripeconnector->connect();

                /* Create auto Stripe dashboard for the user */
                $this->makeStripeAutoDashboard(Auth::user());
            }
        }

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
        $settings->user_id              = $user->id;
        $settings->newsletter_frequency = 0;
        $settings->background_enabled   = true;

        /* Save settings */
        $settings->save();

        /* Create default subscription for the user */
        $plan = Plan::where('name', 'Free')->first();
        $subscription = new Subscription;
        $subscription->user_id              = $user->id;
        $subscription->plan_id              = $plan->id;
        $subscription->current_period_start = Carbon::now();
        $subscription->current_period_end   = Carbon::now()->addDays(SiteConstants::getTrialPeriodInDays());
        $subscription->status               = 'active';

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

        /* Save dashboard object */
        $dashboard->save();

        /* Create clock widget */
        if (array_key_exists('widget-clock', $widgetdata)) {
            $clockwidget = new ClockWidget;

            $clockwidget->dashboard_id  = $dashboard->id;
            $clockwidget->state         = 'active';
            $clockwidget->position      = '{"row":1,"col":3,"size_x":8,"size_y":3}';

            /* Save clock widget object */
            $clockwidget->save();
        }

        /* Create greetings widget */
        if (array_key_exists('widget-greetings', $widgetdata)) {
            $greetingswidget = new GreetingsWidget;

            $greetingswidget->dashboard_id  = $dashboard->id;
            $greetingswidget->state         = 'active';
            $greetingswidget->position      = '{"row":4,"col":3,"size_x":8,"size_y":1}';

            /* Save greetings widget object */
            $greetingswidget->save();

        }

        /* Create quote widget */
        if (array_key_exists('widget-quote', $widgetdata)) {
            $quotewidget = new QuoteWidget;

            $quotewidget->dashboard_id  = $dashboard->id;
            $quotewidget->state         = 'active';
            $quotewidget->position      = '{"row":8,"col":3,"size_x":8,"size_y":1}';

            /* Save quote widget object */
            $quotewidget->save();
        }

        /* Return */
        return $dashboard;
    }

    /**
     * makeStripeAutoDashboard
     * creates a new Dashboard object and the default Stripe widgets
     * --------------------------------------------------
     * @param (User) ($user) The current user
     * @return (Dashboard) ($dashboard) The new Dashboard object
     * --------------------------------------------------
     */
    private function makeStripeAutoDashboard($user) {
        /* Create new dashboard */
        $dashboard = new Dashboard;

        $dashboard->user_id     = $user->id;
        $dashboard->name        = 'My Stripe financial dashboard';
        $dashboard->background  = 'On';

        /* Save dashboard object */
        $dashboard->save();

        /* Create MRR widget */

        /* Create ARR widget */

        /* Create ARPU widget */

        /* Return */
        return $dashboard;
    }

} /* SignupWizardController */