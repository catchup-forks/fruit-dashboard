<?php
use PayPal\Api\OpenIdSession;
use PayPal\Rest\ApiContext;

use PayPal\Api\OpenIdTokeninfo;
use PayPal\Exception\ConnectionException;
use PayPal\Api\OpenIdUserinfo;
use PayPal\Api\Plan;

use PayPal\Api\PaymentDefinition;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\Currency;
use PayPal\Api\ChargeModel;


/*
A Controller for testing stuff
*/

class DevController extends Controller
{

    public function showTesting() {
        if (!Auth::check()) {
            Auth::login(User::find(1));
        }

        // Looking for errors.
        if (Input::get('error') !== null) {
            Log::info('Stripe authentication failed for user ' + Input::get('error_description'));
        }

        // Looking for code.
        if (Input::get('code') !== null) {
            // Retrieving tokens.
            try {
                $this->getStripeTokens(Input::get('code'));
            } catch (StripeConnectFailed $e) {
                Log::info($e->getMessage());
            }
        }

        // Building Stripe connect URI.
        $stripe_connect_url = Config::get('constants.STRIPE_CONNECT_URI');
        $stripe_connect_url .= '?response_type=' . 'code';
        $stripe_connect_url .= '&client_id=' . Config::get('constants.STRIPE_CLIENT_ID');
        $stripe_connect_url .= '&scope=' . 'read_only';

        // Return.
        return View::make('connectstripe')
            ->with('stripe_connect_url', $stripe_connect_url);
    }

    /**
     * Retrieving the access, and refresh tokens from stripe.
     *
     * @param string $code The returned code by stripe.
     * @return None
     * @throws StripeConnectFailed
    */
    private function getStripeTokens($code) {
        // Building POST request.
        $url= Config::get('constants.STRIPE_ACCESS_TOKEN_URI');
        $post_kwargs = 'client_secret=' . Config::get('constants.STRIPE_SECRET_KEY');;
        $post_kwargs .= '&code=' . $code;
        $post_kwargs .= '&grant_type=' . 'authorization_code';

        // POST settings.
        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_kwargs);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_HEADER, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

        // Retrieving response.
        $response = json_decode(curl_exec( $ch ), TRUE);

        // Invalid/No answer from Stripe.
        if ($response === null) {
            throw new StripeConnectFailed('Stripe server error, please try again.', 1);
        }

        // Error handling.
        if (isset($response['error'])) {
            throw new StripeConnectFailed('Your connection expired, please try again.', 1);
        }

        // Creating a Connection instance, and saving to DB.
        $connection = new Connection(array(
            'access_token'  => $response['access_token'],
            'refresh_token' => $response['refresh_token'],
            'type'          => 'stripe',
        ));
        $connection->user()->associate(Auth::user());
        $connection->save();

    }

}
