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
        $braintreeConnector = new BraintreeConnector(Auth::user());

        /* Render the page */
        return View::make('service.braintree.connect')
            ->with('authFields', $braintreeConnector->getAuthFields());
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
        $braintreeConnector->getTokens(Input::all());

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
        return Redirect::to($this->getReferer());
    }

    /**
     * anyDisconnectBraintree
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

        } catch (SericeNotConnected $e) {}

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
                $connector->getTokens(
                    Session::get('oauth_token'),
                    Input::get('oauth_token'),
                    Session::get('oauth_token_secret'),
                    Input::get('oauth_verifier')
                );
            } catch (TwitterConnectFailed $e) {
                return Redirect::route('signup-wizard.social-connections')
                    ->with('error', 'Something went wrong, please try again.');
            }

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
     * anyDisconnectTwitter * --------------------------------------------------
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
                $connector->getTokens();
            } catch (TwitterNotConnected $e) {
                Log::info($e->getMessage());
                return Redirect::route('signup-wizard.social-connections')
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

            /* Successful connect. */
            return Redirect::route('service.facebook.select-pages')
                ->with('success', 'Facebook connection successful');

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
        } else if (count($pages) == 1) {
            Queue::push('FacebookAutoDashboardCreator', array(
                'user_id' => Auth::user()->id,
                'page_id' => array_keys($pages)[0]
            ));
            return Redirect::to($this->getReferer());
        }

        return View::make('service.facebook.select-pages')
            ->with('pages', $pages);
    }

    /**
     * postSelectFacebookPages
     * --------------------------------------------------
     * @return Creates auto dashboard for the selected pages.
     * --------------------------------------------------
     */
    public function postSelectFacebookPages() {
        /* Getting a user's facebook pages for multiple select. */
        if (count(Input::get('pages'))) {
            return Redirect::back()
                ->with('error', 'Please select at least one of the pages.');
        }
        foreach (Input::get('pages') as $page) {
            Queue::push('FacebookAutoDashboardCreator', array(
                'user_id' => Auth::user()->id,
                'page_id' => $page->id
            ));
        }
        return Redirect::to($this->getReferer())
            ->with('success', 'Your dashboards are being created at the moment.');
    }

    /**
     * ================================================== *
     *                        GOOGLE                      *
     * ================================================== *
     */

    /**
     * anyGoogleAnalyticsConnectanyGoogleConnect
     * --------------------------------------------------
     * @return connects a user to GA.
     * --------------------------------------------------
     */
    public function anyGoogleAnalyticsConnect() {
        return $this->connectGoogle("GoogleAnalyticsConnector");
     }

    /**
     * anyDisconnectGoogle
     * --------------------------------------------------
     * @return Deletes the logged in user's GA connection.
     * --------------------------------------------------
     */
    public function anyGoogleAnalyticsDisconnect() {
        return $this->disconnectGoogle("GoogleAnalyticsConnector");
    }

    /**
     * anyGoogleCalendarConnectanyGoogleConnect
     * --------------------------------------------------
     * @return connects a user to GA.
     * --------------------------------------------------
     */
    public function anyGoogleCalendarConnect() {
        return $this->connectGoogle("GoogleCalendarConnector");
     }

    /**
     * anyDisconnectGoogle
     * --------------------------------------------------
     * @return Deletes the logged in user's GA connection.
     * --------------------------------------------------
     */
    public function anyGoogleCalendarDisconnect() {
        return $this->disconnectGoogle("GoogleCalendarConnector");
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
                ->with('warning', 'You are already connected the service');
        }

        if (Input::get('code', FALSE)) {
            /* Oauth ready. */
            $connector = new StripeConnector(Auth::user());
            try {
                $connector->getTokens(Input::get('code'));
            } catch (StripeConnectFailed $e) {
                return Redirect::to($this->getReferer())
                    ->with('error', 'Something went wrong, please try again.');
            }

            /* Track event | SERVICE CONNECTED */
            $tracker = new GlobalTracker();
            $tracker->trackAll('detailed', array(
                'ec' => 'Service connected',
                'ea' => 'Stripe',
                'el' => Auth::user()->email,
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
     * anyDisconnectStripe
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
     * @return connects a user to a google service.
     * --------------------------------------------------
     */
    private function connectGoogle($connectorClass) {
        /* Creating connection credentials. */
        $connector = new $connectorClass(Auth::user());
        if (Input::get('code', FALSE)) {
            try {
                $connector->getTokens(Input::get('code'));
            } catch (GoogleConnectFailed $e) {
                /* User declined */
                return Redirect::route('signup-wizard.social-connections')
                    ->with('error', 'something went wrong.');
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
            return Redirect::to($this->getReferer())
                ->with('success', 'Google connection successful');

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
        Log::info(URL::previous());
        Session::put('referer', URL::previous());
    }

    /**
     * getReferer
     * --------------------------------------------------
     * @return Returns the saved route from session.
     * --------------------------------------------------
     */
    private function getReferer() {
        if (Session::has('referer')) {
            return Session::pull('referer');
        } else {
            return route('settings.settings');
        }
    }

} /* ServiceConnectionController */