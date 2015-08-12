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
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * getBraintreeConnect
     * --------------------------------------------------
     * @return Renders the braintree connect setup step
     * --------------------------------------------------
     */
    public function getBraintreeConnect() {
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
        $braintreeConnector->generateAccessToken(Input::all());

        /* Render the page */
        return Redirect::route('signup-wizard.financial-connections');
    }

    /**
     * anyTwitterConnect
     * --------------------------------------------------
     * @return connects a user to twitter.
     * --------------------------------------------------
     */
    public function anyTwitterConnect() {

        /* Setting up connection. */
        if (Input::get('oauth_verifier', FALSE) && Input::get('oauth_token', FALSE)) {
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
            return Redirect::route('signup-wizard.social-connections')
                ->with('success', 'Twitter connection successful');

        } else {
            /* Creating connection, storing credentials. */
            $connectData = TwitterConnector::getTwitterConnectURL();
            Session::put('oauth_token', $connectData['oauth_token']);
            Session::put('oauth_token_secret', $connectData['oauth_token_secret']);
            return Redirect::to($connectData['connection_url']);
        }

        return Redirect::route('signup-wizard.social-connections')
            ->with('error', 'Something went wrong.');
     }


} /* ServiceConnectionController */