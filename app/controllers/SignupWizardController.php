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
            return Redirect::route('signup-wizard.authentication');
        }
        
        /* Create the personal dashboard based on the inputs */
        $this->makePersonalAutoDashboard(Auth::user(), Input::all());
        
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
                    Log::error($e->getMessage());
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
            error_log('STRIPE');
        }

        /* Braintree connection */
        if(Input::get('braintree-connect', FALSE)) {
            error_log('BRAINTREE');
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
        $subscription->current_period_end   = Carbon::now()->addDays(Config::get('constants.TRIAL_PERIOD_IN_DAYS'));
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
        $clockwidget = new ClockWidget;

        $clockwidget->dashboard_id  = $dashboard->id;
        $clockwidget->descriptor_id = Config::get('constants.WD_ID_CLOCK');
        $clockwidget->state         = 'active';
        $clockwidget->position      = '{"row":1,"col":3,"size_x":8,"size_y":3}';

        /* Save clock widget object */
        $clockwidget->save();

        /* Create quote widget */
        $quotewidget = new QuoteWidget;

        $quotewidget->dashboard_id  = $dashboard->id;
        $quotewidget->descriptor_id = Config::get('constants.WD_ID_QUOTE');
        $quotewidget->state         = 'active';
        $quotewidget->position      = '{"row":8,"col":3,"size_x":8,"size_y":1}';

        /* Save quote widget object */
        $quotewidget->save();

        /* Create greetings widget */
        /**
         * @todo: create if model exists
         */

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