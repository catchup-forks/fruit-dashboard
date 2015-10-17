<?php

/**
 * --------------------------------------------------------------------------
 * SignupWizardController: Handles the services connections.
 * --------------------------------------------------------------------------
 */
class ServiceConnectionController extends BaseController
{
    /**
     * ================================================== *
     *                      BRAINTREE                     *
     * ================================================== *
     */

    /**
     * getBraintreeConnect
     * --------------------------------------------------
     * @return Renders the braintree connect setup step
     * --------------------------------------------------
     */
    public function getBraintreeConnect() {
        $this->saveReferer();

        /* Render the page */
        return View::make('service.braintree.connect')
            ->with('authFields', BraintreeConnector::getAuthFields());
    }

    /**
     * postBraintreeConnect
     * --------------------------------------------------
     * @return Saves the user braintree connect settings
     * --------------------------------------------------
     */
    public function postBraintreeConnect() {
        // Validation.
        $rules = array('publicKey'   => 'required',
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

        /* Saving connection. */
        $braintreeConnector = new BraintreeConnector(Auth::user());
        $braintreeConnector->saveTokens(Input::all());
        $braintreeConnector->createDataObjects();

        /* Track event | SERVICE CONNECTED */
        $tracker = new GlobalTracker();
        $tracker->trackAll('detailed', array(
            'ec' => 'Service connected',
            'ea' => 'Braintree',
            'el' => Auth::user()->email,
            'ev' => 0,
            'en' => 'Service connected',
            'md' => array(
                'service' => 'Braintree',
                'email'   => Auth::user()->email
                )
            )
        );

        /* Render the page */
        return Redirect::to($this->getReferer())
            ->with('success', 'Braintree connection successful.');
    }

    /**
     * anyBraintreeDisconnect
     * --------------------------------------------------
     * @return Deletes the logged in user's braintree connection.
     * --------------------------------------------------
     */
    public function anyBraintreeDisconnect() {
        /* Try to disconnect */
        try {
            $connector = new BraintreeConnector(Auth::user());
            $connector->disconnect();

            /* Track event | SERVICE DISCONNECTED */
            $tracker = new GlobalTracker();
            $tracker->trackAll('detailed', array(
                'ec' => 'Service disconnected',
                'ea' => 'Braintree',
                'el' => Auth::user()->email,
                'ev' => 0,
                'en' => 'Service disconnected',
                'md' => array(
                    'service' => 'Braintree',
                    'email'   => Auth::user()->email
                    )
                )
            );

        } catch (ServiceNotConnected $e) {}

        /* Redirect */
        return Redirect::back();
    }

    /**
     * ================================================== *
     *                       TWITTER                      *
     * ================================================== *
     */

    /**
     * anyTwitterConnect
     * --------------------------------------------------
     * @return connects a user to twitter.
     * --------------------------------------------------
     */
    public function anyTwitterConnect() {

        /* Setting up connection. */
        if (Input::get('denied')) {
            return Redirect::to($this->getReferer())
                ->with('error', 'You\'ve declined the request.');
        }
        else if (Input::get('oauth_verifier', FALSE) && Input::get('oauth_token', FALSE)) {
            $connector = new TwitterConnector(Auth::user());
            try {
                $connector->saveTokens(array(
                    'token_ours'    => Session::get('oauth_token'),
                    'token_request' => Input::get('oauth_token'),
                    'token_secret'  => Session::get('oauth_token_secret'),
                    'verifier'      => Input::get('oauth_verifier')
                ));
            } catch (TwitterConnectFailed $e) {
                return Redirect::route('signup-wizard.getStep', 'social-connections')
                    ->with('error', 'Something went wrong, please try again.');
            }

            $connector->createDataObjects();

            /* Successful connect. */
            return Redirect::to($this->getReferer())
                ->with('success', 'Twitter connection successful');

        } else {
            /* Creating connection, storing credentials. */
            $connectData = TwitterConnector::getTwitterConnectURL();
            Session::put('oauth_token', $connectData['oauth_token']);
            Session::put('oauth_token_secret', $connectData['oauth_token_secret']);

            /* Track event | SERVICE CONNECTED */
            $tracker = new GlobalTracker();
            $tracker->trackAll('detailed', array(
                'ec' => 'Service connected',
                'ea' => 'Twitter',
                'el' => Auth::user()->email,
                'ev' => 0,

                'en' => 'Service connected',
                'md' => array(
                    'service' => 'Twitter',
                    'email'   => Auth::user()->email
                    )
                )
            );

            $this->saveReferer();
            return Redirect::to($connectData['connection_url']);
        }

        return Redirect::to($this->getReferer())
            ->with('error', 'Something went wrong.');
     }

    /**
     * anyTwitterDisconnect
     * --------------------------------------------------
     * @return Deletes the logged in user's twitter connection.
     * --------------------------------------------------
     */
    public function anyTwitterDisconnect() {
        /* Try to disconnect */
        try {
            $connector = new TwitterConnector(Auth::user());
            $connector->disconnect();

            /* Track event | SERVICE DISCONNECTED */
            $tracker = new GlobalTracker();
            $tracker->trackAll('detailed', array(
                'ec' => 'Service disconnected',
                'ea' => 'Twitter',
                'el' => Auth::user()->email,
                'ev' => 0,
                'en' => 'Service disconnected',
                'md' => array(
                    'service' => 'Twitter',
                    'email'   => Auth::user()->email
                    )
                )
            );

        } catch (ServiceNotConnected $e) {}

        /* Redirect */
        return Redirect::back();
    }

    /**
     * ================================================== *
     *                      FACEBOOK                      *
     * ================================================== *
     */

    /**
     * anyFacebookConnect
     * --------------------------------------------------
     * @return connects a user to facebook.
     * --------------------------------------------------
     */
    public function anyFacebookConnect() {
        if (Auth::user()->isServiceConnected('facebook')) {
            return Redirect::to($this->getReferer())
                ->with('warning', 'You are already connected the service');
        }
        $connector = new FacebookConnector(Auth::user());
        if (Input::get('code', FALSE)) {
            /* Oauth ready. */
            try {
                $connector->saveTokens();
            } catch (ServiceException $e) {
                Log::info($e->getMessage());
                return Redirect::route($this->getReferer(), 'social-connections')
                    ->with('error', 'Something went wrong with the connection.');
            }
            /* Track event | SERVICE CONNECTED */
            $tracker = new GlobalTracker();
            $tracker->trackAll('detailed', array(
                'ec' => 'Service connected',
                'ea' => 'Facebook',
                'el' => Auth::user()->email,
                'ev' => 0,
                'en' => 'Service connected',
                'md' => array(
                    'service' => 'Facebook',
                    'email'   => Auth::user()->email
                    )
                )
            );

            $message = 'Facebook connection successful';
            if (Session::pull('createDashboard')) {
                return Redirect::route('service.facebook.select-pages')
                    ->with('success', $message);
            } else {
                return Redirect::to($this->getReferer())
                    ->with('success', $message);
            }

        } else if (Input::get('error', FALSE)) {
            /* User declined */
            return Redirect::to($this->getReferer())
                ->with('error', 'You\'ve declined the request.');
        }

        $this->saveReferer();
        return Redirect::to($connector->getFacebookConnectUrl());
    }

    /**
     * anyFacebookDisconnect
     * --------------------------------------------------
     * @return disconnects a user from facebook.
     * --------------------------------------------------
     */
    public function anyFacebookDisconnect() {
        /* Try to disconnect */
        $connector = new FacebookConnector(Auth::user());
        try {
            $connector->disconnect();

            /* Track event | SERVICE DISCONNECTED */
            $tracker = new GlobalTracker();
            $tracker->trackAll('detailed', array(
                'ec' => 'Service disconnected',
                'ea' => 'Facebook',
                'el' => Auth::user()->email,
                'ev' => 0,
                'en' => 'Service disconnected',
                'md' => array(
                    'service' => 'Facebook',
                    'email'   => Auth::user()->email
                    )
                )
            );

        } catch (ServiceNotConnected $e) {}

        /* Redirect */
        return Redirect::back();
    }

    /**
     * getSelectFacebookPages
     * --------------------------------------------------
     * @return Renders the select facebook page view.
     * --------------------------------------------------
     */
    public function getSelectFacebookPages() {
        /* Getting a user's facebook pages for multiple select. */
        $pages = array();
        foreach (Auth::user()->facebookPages as $page) {
            $pages[$page->id] = $page->name;
        }

        /* If only one auto create and redirect. */
        if (count($pages) == 0) {
            return Redirect::to($this->getReferer())
                ->with('error', 'You don\'t have any facebook pages associated with this account');
        }

        return View::make('service.facebook.select-pages', array(
                'pages' => $pages,
                'cancelRoute' => $this->getReferer(FALSE),
            ));
    }

    /**
     * postSelectFacebookPages
     * --------------------------------------------------
     * @return Creates auto dashboard for the selected pages.
     * --------------------------------------------------
     */
    public function postSelectFacebookPages() {
        /* Getting a user's facebook pages for multiple select. */
        if (count(Input::get('pages')) == 0) {
            return Redirect::back()
                ->with('error', 'Please select at least one of the pages.');
        }

        $pages = array();
        foreach (Auth::user()->facebookPages as $page) {
            $pages[$page->id] = $page->name;
        }

        foreach (Input::get('pages') as $id) {
            /* Creating data objects. */
            $connector = new FacebookConnector(Auth::user());
            $connector->createDataObjects(array('page' => $id));
        }

        return Redirect::to($this->getReferer())
            ->with('success', 'Connection successful.');
    }
    /**
     * anyFacebookRefreshPages
     * --------------------------------------------------
     * @return Refreshes a user's facebook pages.
     * --------------------------------------------------
     */
    public function anyFacebookRefreshPages() {
        /* Try to disconnect */
        try {
            $collector = new FacebookDataCollector(Auth::user());
            $collector->savePages();

        } catch (ServiceNotConnected $e) {}

        /* Redirect */
        return Redirect::back();
    }

    /**
     * ================================================== *
     *                        GOOGLE                      *
     * ================================================== *
     */

    /**
     * anyGoogleAnalyticsConnect
     * --------------------------------------------------
     * @return connects a user to GA.
     * --------------------------------------------------
     */
    public function anyGoogleAnalyticsConnect() {
        $route = null;
        if (Session::pull('createDashboard')) {
            $route = route('service.google_analytics.select-properties');
        } elseif (Session::pull('signupWizard')) {
            $route = route(
                'signup-wizard.getStep',
                SiteConstants::getSignupWizardStep('next', 'google-analytics-connection')
            );
        }
        return $this->connectGoogle("GoogleAnalyticsConnector", $route);
     }

    /**
     * anyGoogleAnalyticsDisconnect
     * --------------------------------------------------
     * @return Deletes the logged in user's GA connection.
     * --------------------------------------------------
     */
    public function anyGoogleAnalyticsDisconnect() {
        return $this->disconnectGoogle("GoogleAnalyticsConnector");
    }

    /**
     * anyGoogleCalendarConnect
     * --------------------------------------------------
     * @return connects a user to GA.
     * --------------------------------------------------
     */
    public function anyGoogleCalendarConnect() {
        return $this->connectGoogle("GoogleCalendarConnector");
     }

    /**
     * anyGoogleCalendarDisconnect
     * --------------------------------------------------
     * @return Deletes the logged in user's GA connection.
     * --------------------------------------------------
     */
    public function anyGoogleCalendarDisconnect() {
        return $this->disconnectGoogle("GoogleCalendarConnector");
    }

    /**
     * getSelectGoogleAnalyticsProperties
     * --------------------------------------------------
     * @return Renders the select google analytics properties view.
     * --------------------------------------------------
     */
    public function getSelectGoogleAnalyticsProperties() {
        /* Getting a user's google analytics properties for multiple select. */
        $profiles = array();
        foreach (Auth::user()->googleAnalyticsProperties as $property) {
            $profiles[$property->name] = array();
            foreach ($property->profiles as $profile) {
                $profiles[$property->name][$profile->profile_id] = $profile->name;
            }
        }

        /* If only one auto create and redirect. */
        if (count($profiles) == 0) {
            return Redirect::to($this->getReferer())
                ->with('error', 'You don\'t have any google analytics properties associated with this account');
        }

        return View::make('service.google_analytics.select-properties', array(
                   'profiles' => $profiles,
                   'cancelRoute' => $this->getReferer(FALSE),
                ));
    }

    /**
     * getGoogleAnalyticsGoals
     * Returning the goals for the selected profile
     * --------------------------------------------------
     * @param int $profileId
     * --------------------------------------------------
     */
    public function getGoogleAnalyticsGoals($profileId) {
        $goals = array();
        foreach (Auth::user()->googleAnalyticsProfiles()->where('profile_id', $profileId)->first()->goals as $goal) {
            $goals[$goal->goal_id] = $goal->name;
        }
        return Response::json($goals);
    }

    /**
     * postSelectGoogleAnalyticsProperties
     * --------------------------------------------------
     * @return Creates auto dashboard for the selected properties.
     * --------------------------------------------------
     */
    public function postSelectGoogleAnalyticsProperties() {
        if (count(Input::get('profiles')) == 0) {
            return Redirect::back()
                ->with('error', 'Please select at least one of the properties.');
        }

        foreach (Input::get('profiles') as $id) {
            /* Selecting profile */
            $profile = Auth::user()->googleAnalyticsProfiles()->where('profile_id', $id)->first();
            if (is_null($profile)) {
                continue;
            }

            /* Creating connector instance. */
            $connector = new GoogleAnalyticsConnector(Auth::user());

            /* Iterating through the goals. */
            $goals = $profile->goals;
            $selectedGoals = Input::get('goals');
            if ($selectedGoals && ! empty($selectedGoals)) {
                foreach ($selectedGoals as $goalId) {
                    /* Creating data objects. */
                    $settings = array(
                        'profile'  => $id,
                        'goal'     => $goalId
                    );
                    $connector->createDataObjects($settings);
                }
            }

            /* Calling collector for simple profile objects. */
            $settings = array('profile'  => $id);
            $connector->createDataObjects($settings);

        }

        $message = 'Connection successful.';

        return Redirect::to($this->getReferer())
            ->with('success', $message);
    }

    /**
     * anyGoogleAnalyticsRefreshProperties
     * --------------------------------------------------
     * @return Refreshes a user's google analytics properties.
     * --------------------------------------------------
     */
    public function anyGoogleAnalyticsRefreshProperties() {
        /* Try to disconnect */
        try {
            $collector = new GoogleAnalyticsDataCollector(Auth::user());
            $collector->saveProperties();

        } catch (ServiceNotConnected $e) {}

        /* Redirect */
        return Redirect::back();
    }

    /**
     * ================================================== *
     *                       STRIPE                       *
     * ================================================== *
     */

    /**
     * anyStripeConnect
     * --------------------------------------------------
     * @return connects a user to twitter.
     * --------------------------------------------------
     */
    public function anyStripeConnect() {
        if (Auth::user()->isServiceConnected('stripe')) {
            return Redirect::back()
                ->with('warning', 'You have already connected the service');
        }

        if (Input::get('code', FALSE)) {
            /* Oauth ready. */
            $connector = new StripeConnector(Auth::user());
            try {
                $connector->saveTokens(array('auth_code' => Input::get('code')));
            } catch (StripeConnectFailed $e) {
                return Redirect::to($this->getReferer())
                    ->with('error', 'Something went wrong, please try again.');
            }

            $connector->createDataObjects();

            /* Track event | SERVICE CONNECTED */
            $tracker = new GlobalTracker();
            $tracker->trackAll('detailed', array(
                'ec' => 'Service connected',
                'ea' => 'Stripe', 'el' => Auth::user()->email,
                'ev' => 0,
                'en' => 'Service connected',
                'md' => array(
                    'service' => 'Stripe',
                    'email'   => Auth::user()->email
                    )
                )
            );

            /* Successful connect. */
            return Redirect::to($this->getReferer())
                ->with('success', 'Stripe connection successful');

        } else if (Input::get('error', FALSE)) {
            /* User declined */
            return Redirect::to($this->getReferer())
                ->with('error', 'You\'ve declined the request.');
        }

        $this->saveReferer();
        return Redirect::to(StripeConnector::getStripeConnectURI(route('service.stripe.connect')));
     }
    /**
     * anyStripeDisconnect
     * --------------------------------------------------
     * @return Deletes the logged in user's stripe connection.
     * --------------------------------------------------
     */
    public function anyStripeDisconnect() {
        /* Try to disconnect */
        try {
            $connector = new StripeConnector(Auth::user());
            $connector->disconnect();

            /* Track event | SERVICE DISCONNECTED */
            $tracker = new GlobalTracker();
            $tracker->trackAll('detailed', array(
                'ec' => 'Service disconnected',
                'ea' => 'Stripe',
                'el' => Auth::user()->email,
                'ev' => 0,
                'en' => 'Service disconnected',
                'md' => array(
                    'service' => 'Stripe',
                    'email'   => Auth::user()->email
                    )
                )
            );

        } catch (ServiceNotConnected $e) {}

        /* Redirect */
        return Redirect::back();
    }

    /**
     * ================================================== *
     *                   GOOGLE SHORTHAND                 *
     * ================================================== *
     */

    /**
     * connectGoogle
     * --------------------------------------------------
     * @param string $connectorClass
     * @param string return route.
     * @return connects a user to a google service.
     * --------------------------------------------------
     */
    private function connectGoogle($connectorClass, $returnUrl=null) {
        if (Auth::user()->isServiceConnected($connectorClass::getServiceName())) {
            return Redirect::to($this->getReferer())
                ->with('warning', 'You have already connected the service');
        }
        /* Creating connection credentials. */
        $connector = new $connectorClass(Auth::user());
        if (Input::get('code', FALSE)) {
            try {
                $connector->saveTokens(array('auth_code' => Input::get('code')));
            } catch (GoogleConnectFailed $e) {
                /* User declined */
                return Redirect::route('signup-wizard.getStep', 'google-analytics-connection')
                    ->with('error', 'something went wrong.');
            } catch (Google_Auth_Exception $e) {
                return Redirect::route('signup-wizard.getStep', 'google-analytics-connection')
                    ->with('error', 'Invalid token please try again.');
            }

            /* Track event | SERVICE CONNECTED */
            $tracker = new GlobalTracker();
            $tracker->trackAll('detailed', array(
                'ec' => 'Service connected',
                'ea' => str_replace('Connector', '', $connectorClass),
                'el' => Auth::user()->email,
                'ev' => 0,
                'en' => 'Service connected',
                'md' => array(
                    'service' => str_replace('Connector', '', $connectorClass),
                    'email'   => Auth::user()->email
                    )
                )
            );

            /* Successful connect. */
            if (is_null($returnUrl)) {
                return Redirect::to($this->getReferer())
                    ->with('success', 'Google connection successful');
            } else {
                return Redirect::to($returnUrl)
                    ->with('success', 'Google connection successful');
            }

        } else if (Input::get('error', FALSE)) {
            /* User declined */
            return Redirect::to($this->getReferer())
                ->with('error', 'You\'ve declined the request.');

        } else {
            /* Redirectong to Oauth. */
            $this->saveReferer();
            return Redirect::to($connectorClass::getConnectUrl());
        }
     }

    /**
     * disconnectGoogle
     * --------------------------------------------------
     * @return Deletes the logged in user's specific google connection.
     * --------------------------------------------------
     */
    private function disconnectGoogle($connectorClass) {
        /* Try to disconnect */
        try {
            $connector = new $connectorClass(Auth::user());
            $connector->disconnect();

            /* Track event | SERVICE DISCONNECTED */
            $tracker = new GlobalTracker();
            $tracker->trackAll('detailed', array(
                'ec' => 'Service disconnected',
                'ea' => str_replace('Connector', '', $connectorClass),
                'el' => Auth::user()->email,
                'ev' => 0,
                'en' => 'Service disconnected',
                'md' => array(
                    'service' => str_replace('Connector', '', $connectorClass),
                    'email'   => Auth::user()->email
                    )
                )
            );

        } catch (ServiceNotConnected $e) {}

        /* Redirect */
        return Redirect::back();
    }

    /**
     * saveReferer
     * Saving the Referer to session.
     */
    private function saveReferer() {
        $previous = URL::previous();
        if (strpos($previous, route('widget.add')) === 0) {
            Session::put('addWidgetMeta', array(
                'dashboard'  => Input::get('dashboard'),
                'descriptor' => Input::get('descriptor')
            ));
        }

        if (Input::get('createDashboard')) {
            Session::put('createDashboard', true);
        }
        if (Input::get('signupWizard')) {
            Session::put('signupWizard', true);
        }
        if ( ! is_null($previous)) {
            Session::put('referer', $previous);
        } else {
            Session::put('referer', 'settings');
        }
    }

    /**
     * getReferer
     * --------------------------------------------------
     * @return Returns the saved route from session.
     * --------------------------------------------------
     */
    private function getReferer($forget=TRUE) {
        if (Session::has('addWidgetMeta')) {
            /* We came from add widget. */
            $meta = Session::pull('addWidgetMeta');
            return route('widget.add-with-data', array(
                'descriptorId' => $meta['descriptor'],
                'dashboardId'  => $meta['dashboard']
            ));
        } else if (Session::has('referer')) {
            if ( ! $forget) {
                return Session::get('referer');
            }
            return Session::pull('referer');
        } else {
            return route('settings.settings');
        }
    }

} /* ServiceConnectionController */