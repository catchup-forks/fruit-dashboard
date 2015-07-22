<?php


/**
 * --------------------------------------------------------------------------
 * SignupWizardController: Handles the signup process
 * --------------------------------------------------------------------------
 */
class SignupWizardController extends BaseController
{

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
        if (Auth::check()) {
            error_log('YES');
        } else {
            error_log('NO');
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
        /* Render the page */
        return View::make('signup-wizard.personal-widgets');
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
        /* Render the page */
        return View::make('signup-wizard.financial-connections');
    }

    /**
     * ================================================== *
     *                   HELPER FUNCTIONS                 *
     * ================================================== *
     */

    /**
     * createUser
     * creates a new User object from the POST data
     * --------------------------------------------------
     * @return ($user) (User) The new User object
     * --------------------------------------------------
     */
    private function createUser($input) {
        /* Create new user */
        $user = new User;

        /* Set authentication info */
        $user->email = $input['email'];
        $user->password = Hash::make($input['password']);
        $user->name = $input['name'];
        
        /* Save the user */
        $user->save();

        /* Create default settings for the user */
        $settings = new Settings;
        $settings->user_id = $user->id;
        $settings->newsletter_frequency = 0;
        $settings->background_enabled = true;

        /* Save settings */
        $settings->save();

        /* Create default subscription for the user */
        $plan = Plan::where('name', 'Free')->first();
        $subscription = new Subscription;
        $subscription->user_id = $user->id;
        $subscription->plan_id = $plan->id;
        $subscription->current_period_start = Carbon::now();
        $subscription->current_period_end = Carbon::now()->addDays(Config::get('constants.TRIAL_PERIOD_IN_DAYS'));
        $subscription->status = 'active';

        /* Save subscription */
        $subscription->save();

        /* Return */
        return $user;
    }

} /* SignupWizardController */