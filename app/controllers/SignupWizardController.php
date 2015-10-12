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
            return Redirect::route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('first', null));

        /* Validator failed */
        } else {
            /* Render the page */
            return Redirect::route('signup-wizard.authentication')
                ->with('error', $validator->errors()->get(key($validator->invalid()))[0]);
        }
    }

    /**
     * anyFacebookLogin
     * --------------------------------------------------
     * @return logs a user in with facebook.
     * --------------------------------------------------
     */
    public function anyFacebookLogin() {
        /* Oauth ready. */
        if (Input::get('code', FALSE)) {
            $userInfo = FacebookConnector::loginWithFacebook();
            if ($userInfo['isNew']) {
                return Redirect::route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('first', null))
                    ->with('success', 'Welcome on board, '. $userInfo['user']->name. '!');
            } else {
                return Redirect::route('dashboard.dashboard')
                    ->with('success', 'Welcome back, '. $userInfo['user']->name. '!');
            }
        /* User declined */
        } else if (Input::get('error', FALSE)) {
            return Redirect::route('auth.signin')
                ->with('error', 'Sorry, we couldn\'t log you in. Please try again.');
        }

        /* Basic page load */
        return Redirect::to(FacebookConnector::getFacebookLoginUrl());
    }

    /**
     * ================================================== *
     *                 FUNCTIONS FOR STEPS                *
     * ================================================== *
     */

    /**
     * getStep
     * --------------------------------------------------
     * @param (string) {$step} The actual step
     * @return Renders the requested step
     * --------------------------------------------------
     */
    public function getStep($step) {
        /* Get user settings */
        $settings = Auth::user()->settings;
        
        /* Requesting the last step */
        if ($step == SiteConstants::getSignupWizardStep('last', null)) {
            /* Set onboarding state */
            $settings->onboarding_state = 'finished';
            $settings->save();

            /* Redirect to the dashboard*/
            return Redirect::route('dashboard.dashboard', array('tour' => TRUE));
        } else {
            /* Set onboarding state */
            $settings->onboarding_state = $nextStep;
            $settings->save();
           
            /* Get responsible function */
            $stepFunction = 'get'. Utilities::dashToCamelCase($step);

            /* Call responsible function */
            $params = $this->$stepFunction();
            $params = array_merge($params, ['currentStep' => $step]);

            /* Return */
            return View::make('signup-wizard.'.$step, $params);
        }
    }

    /**
     * postStep
     * --------------------------------------------------
     * @param (string) {$step} The actual step
     * @return Handles the POST data for the step, and renders next
     * --------------------------------------------------
     */
    public function postStep($step) {
        /* Get responsible function */
        $stepFunction = 'post'. Utilities::dashToCamelCase($step);

        /* Call responsible function */
        $this->$stepFunction();

        /* Return next step or dashboard and save the new state*/
        $nextStep = SiteConstants::getSignupWizardStep('next', $step);
        $settings = Auth::user()->settings;
        if (is_null($nextStep)) {
            /* Set onboarding state */
            $settings->onboarding_state = 'finished';
            $settings->save();

            /* Redirect to the dashboard*/
            return Redirect::route('dashboard.dashboard', array('tour' => TRUE));
        } else {
            /* Set onboarding state */
            $settings->onboarding_state = $nextStep;
            $settings->save();

            /* Redirect to the next step*/
            return Redirect::route('signup-wizard.getStep', $nextStep);
        }
    }

    /**
     * STEP | getCompanyInfo
     * --------------------------------------------------
     * @return Handles the extra process for getCompanyInfo
     * --------------------------------------------------
     */
    public function getCompanyInfo() {
        return array();
    }

    /**
     * STEP | postCompanyInfo
     * --------------------------------------------------
     * @return Handles the extra process for postCompanyInfo
     * --------------------------------------------------
     */
    public function postCompanyInfo() {
        /* Success */
        $settings = Auth::user()->settings;
        $settings->project_name     = Input::get('project_name');
        $settings->project_url      = Input::get('project_url');
        $settings->startup_type     = Input::get('startup_type');
        $settings->company_size     = Input::get('company_size');
        $settings->company_funding  = Input::get('company_funding');
        $settings->save();   
    }
    
    /**
     * STEP | getFinancialConnections
     * --------------------------------------------------
     * @return Handles the extra process for getFinancialConnections
     * --------------------------------------------------
     */
    public function getFinancialConnections() {
        return array();
    }

    /**
     * STEP | postFinancialConnections
     * --------------------------------------------------
     * @return Handles the extra process for postFinancialConnections
     * --------------------------------------------------
     */
    public function postFinancialConnections() {
        return array();
    }

    /**
     * STEP | getSocialConnections
     * --------------------------------------------------
     * @return Handles the extra process for getSocialConnections
     * --------------------------------------------------
     */
    public function getSocialConnections() {
        return array();
    }

    /**
     * STEP | postSocialConnections
     * --------------------------------------------------
     * @return Handles the extra process for postSocialConnections
     * --------------------------------------------------
     */
    public function postSocialConnections() {
        return array();
    }

    /**
     * STEP | getGoogleAnalyticsConnection
     * --------------------------------------------------
     * @return Handles the extra process for getGoogleAnalyticsConnection
     * --------------------------------------------------
     */
    public function getGoogleAnalyticsConnection() {
        return array('service' => SiteConstants::getServiceMeta('google_analytics'));
    }

    /**
     * STEP | postGoogleAnalyticsConnection
     * --------------------------------------------------
     * @return Handles the extra process for postGoogleAnalyticsConnection
     * --------------------------------------------------
     */
    public function postGoogleAnalyticsConnection() {
        return array();
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

} /* SignupWizardController */