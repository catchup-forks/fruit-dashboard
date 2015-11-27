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
            /* Track event | TRIAL STARTS */
            $tracker = new GlobalTracker();
            $tracker->trackAll('lazy', array(
                'en' => 'Trial starts',
                'el' => Auth::user()->email)
            );

            /* Redirect to next step */
            return Redirect::route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('first'));

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
        if (Input::get('code', false)) {
            $userInfo = FacebookConnector::loginWithFacebook();
            if ($userInfo['isNew']) {
                return Redirect::route('signup-wizard.getStep', SiteConstants::getSignupWizardStep('first'))
                    ->with('success', 'Welcome on board, '. $userInfo['user']->name. '!');
            } else {
                return Redirect::route('dashboard.dashboard')
                    ->with('success', 'Welcome back, '. $userInfo['user']->name. '!');
            }
        /* User declined */
        } else if (Input::get('error', false)) {
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
        if ($step == SiteConstants::getSignupWizardStep('last')) {
            /* Set onboarding state */
            $settings->onboarding_state = 'finished';
            $settings->save();

            /* Track event | SIGNUPWIZARD LOADED STEP */
            $tracker = new GlobalTracker();
            $tracker->trackAll('lazy', array(
                'en' => 'SignupWizard step loaded',
                'el' => $settings->onboarding_state)
            );

            /* Redirect to the dashboard*/
            return Redirect::route('dashboard.dashboard', array('tour' => true));
        } else {
            /* Set onboarding state */
            $settings->onboarding_state = $step;
            $settings->save();

            /* Track event | SIGNUPWIZARD LOADED STEP */
            $tracker = new GlobalTracker();
            $tracker->trackAll('lazy', array(
                'en' => 'SignupWizard step loaded',
                'el' => $settings->onboarding_state)
            );

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
        $result = $this->$stepFunction();

        /* Check result errors */
        if (array_key_exists('error', $result)) {
            return Redirect::route('signup-wizard.getStep', $step)->with('error', $result['error']);
        }

        /* Return next step or dashboard and save the new state*/
        $nextStep = SiteConstants::getSignupWizardStep('next', $step);
        $settings = Auth::user()->settings;
        if (is_null($nextStep)) {
            /* Set onboarding state */
            $settings->onboarding_state = 'finished';
            $settings->save();

            /* Redirect to the dashboard*/
            if (array_key_exists('success', $result)) {
                return Redirect::route('dashboard.dashboard', array('tour' => true))->with('success', $result['success']);
            } else {
                return Redirect::route('dashboard.dashboard', array('tour' => true));
            }
        } else {
            /* Set onboarding state */
            $settings->onboarding_state = $nextStep;
            $settings->save();

            /* Redirect to the next step*/
            if (array_key_exists('success', $result)) {
                return Redirect::route('signup-wizard.getStep', $nextStep)->with('success', $result['success']);
            } else {
                return Redirect::route('signup-wizard.getStep', $nextStep);
            }
        }
    }

    /**
     * STEP | getCompanyInfo
     * --------------------------------------------------
     * @return Handles the extra process for getCompanyInfo
     * --------------------------------------------------
     */
    public function getCompanyInfo() {
        return array('info' => Auth::user()->settings);
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

        /* Return */
        return array();
    }

    /**
     * STEP | getGoogleAnalyticsConnection
     * --------------------------------------------------
     * @return Handles the extra process for getGoogleAnalyticsConnection
     * --------------------------------------------------
     */
    public function getGoogleAnalyticsConnection() {
        /* Return */
        return array('service' => SiteConstants::getServiceMeta('google_analytics'));
    }

    /**
     * STEP | postGoogleAnalyticsConnection
     * --------------------------------------------------
     * @return Handles the extra process for postGoogleAnalyticsConnection
     * --------------------------------------------------
     */
    public function postGoogleAnalyticsConnection() {
        /* Return */
        return array();
    }

    /**
     * STEP | getGoogleAnalyticsProfile
     * --------------------------------------------------
     * @return Handles the extra process for getGoogleAnalyticsProfile
     * --------------------------------------------------
     */
    public function getGoogleAnalyticsProfile() {
        /* Get the profiles of the user */
        $profiles = array();
        foreach (Auth::user()->googleAnalyticsProperties()
                   ->orderBy('name')
                   ->get() as $property) {
            $profiles[$property->name] = array();
            foreach ($property->profiles as $profile) {
                $profiles[$property->name][$profile->profile_id] = $profile->name;
            }
        }

        /* Return */
        return array('profiles' => $profiles);
    }

    /**
     * STEP | postGoogleAnalyticsProfile
     * --------------------------------------------------
     * @return Handles the extra process for postGoogleAnalyticsProfile
     * --------------------------------------------------
     */
    public function postGoogleAnalyticsProfile() {
        /* Check errors */
        if (count(Input::get('profiles')) == 0) {
            return array('error' => 'Please select at least one of the profiles.');
        }

        /* Save profile (create datamanagers) */
        $connector = new GoogleAnalyticsConnector(Auth::user());
        $connector->createDataObjects(array('profile' => Input::get('profiles')));

        /* Save the selected profile in the session */
        Session::put('selectedProfile', Input::get('profiles'));

        /* Return */
        return array();
    }

    /**
     * STEP | getGoogleAnalyticsGoal
     * --------------------------------------------------
     * @return Handles the extra process for getGoogleAnalyticsGoal
     * --------------------------------------------------
     */
    public function getGoogleAnalyticsGoal() {
        /* Get the goals of the user */
        $goals = array();

        $profileId = Session::get('selectedProfile');
        if (!is_null($profileId)) {
            foreach (Auth::user()->googleAnalyticsProfiles()->where('profile_id', $profileId)->first()->goals as $goal) {
                $goals[$goal->goal_id] = $goal->name;
            }
        }
        return array('goals' => $goals);
    }

    /**
     * STEP | postGoogleAnalyticsGoal
     * --------------------------------------------------
     * @return Handles the extra process for postGoogleAnalyticsGoal
     * --------------------------------------------------
     */
    public function postGoogleAnalyticsGoal() {
        /* Save goal (create datamanagers) */
        $connector = new GoogleAnalyticsConnector(Auth::user());
        $connector->createDataObjects(array(
            'profile' => Session::pull('selectedProfile'),
            'goal'    => Input::get('goals')
        ));

        /* Return */
        return array();
    }

    /**
     * STEP | getInstallExtension
     * --------------------------------------------------
     * @return Handles the extra process for getInstallExtension
     * --------------------------------------------------
     */
    public function getInstallExtension() {
        return array();
    }

    /**
     * STEP | postInstallExtension
     * --------------------------------------------------
     * @return Handles the extra process for postInstallExtension
     * --------------------------------------------------
     */
    public function postInstallExtension() {
        return array();
    }

    /**
     * STEP | getSlackIntegration
     * --------------------------------------------------
     * @return Handles the extra process for getSlackIntegration
     * --------------------------------------------------
     */
    public function getSlackIntegration() {
        /* Get existing notification */
        $notification = Auth::user()->notifications()->where('type','slack')->first();
        if ($notification) {
            return array('address' => $notification->address);
        } else {
            return array('address' => '');
        }
    }

    /**
     * STEP | postSlackIntegration
     * --------------------------------------------------
     * @return Handles the extra process for postSlackIntegration
     * --------------------------------------------------
     */
    public function postSlackIntegration() {
        /* Get the address from POST */
        $address = Input::get('address');
        /* Proceed only with provided address */
        if ($address) {
            /* Update Slack notification */
            $slackNotification = Auth::user()->notifications()->where('type','slack')->first();
            $slackNotification->address = $address;
            $slackNotification->save();

            /* Try to send welcome message */
            if ($slackNotification->sendWelcome()) {
                /* Enable notification */
                $slackNotification->is_enabled = true;
                $slackNotification->save();

                /* Return */                
                return array('success' => 'Slack successfully connected');
            } else {
                /* Disable notification */
                $slackNotification->is_enabled = false;
                $slackNotification->save();

                /* Return */
                return array('error' => 'We couldn\'t send any message to the provided url. Please check it, and try again');
              }
        } else {
            return array('error' => 'Please enter your url to the field to connect with Slack.');
        }
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
