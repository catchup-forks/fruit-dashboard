<?php

use Abraham\TwitterOAuth\TwitterOAuth;

/**
* --------------------------------------------------------------------------
* TwitterConnector:
*       Wrapper functions for Twitter connection
* Usage:
*       // For the connection url, and tokens.
*       TwitterHelper::getTwitterConnectURL()
*
*       // For connecting the user
*       $twitterConnector = new TwitterConnector($user);
*       $twitterConnector->saveTokens($token_ours, $token_request, $token_secret, $verifier);
*       $connector->connect();
* --------------------------------------------------------------------------
*/

class TwitterConnector extends GeneralServiceConnector
{
    protected static $service = 'twitter';

    /**
     * ================================================== *
     *                   STATIC SECTION                   *
     * ================================================== *
     */

    /**
     * getTwitterConnectURL
     * --------------------------------------------------
     * Returns the twitter connect url, based on config.
     * @return array
     * --------------------------------------------------
     */
    public static function getTwitterConnectURL() {
        /* Setting up connection. */
        $connection = new TwitterOAuth(
            $_ENV['TWITTER_CONSUMER_KEY'],
            $_ENV['TWITTER_CONSUMER_SECRET']
        );
        /* Getting a request token. */
        $requestToken = $connection->oauth('oauth/request_token', array('oauth_callback' => route('service.twitter.connect')));

        /* Return URI */
        return array(
            'oauth_token'        => $requestToken['oauth_token'],
            'oauth_token_secret' => $requestToken['oauth_token_secret'],
            'connection_url'     => $connection->url(
                'oauth/authorize',
                array('oauth_token' => $requestToken['oauth_token'])
            )

        );
    }

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * connect
     * --------------------------------------------------
     * Sets up a twitter connection API key.
     * @throws TwiiterNotConnected
     * --------------------------------------------------
     */
    public function connect() {
        $conn = $this->getConnection();
        /* Get access tokens from DB. */
        $accessToken = json_decode($conn->access_token, 1);

        /* Creating connection */
        $connection = new TwitterOAuth($_ENV['TWITTER_CONSUMER_KEY'], $_ENV['TWITTER_CONSUMER_SECRET'], $accessToken['oauth_token'], $accessToken['oauth_token_secret']);

        return $connection;
    }

    /**
     * saveTokens
     * --------------------------------------------------
     * Retrieving the access tokens, OAUTH.
     * @param array $parameters
     * @return None
     * @throws TwitterConnectFailed
     * --------------------------------------------------
     */
    public function saveTokens(array $parameters=array()) {
        $tokenOurs = $parameters['token_ours'];

        /* Oauth ready. */
        $requestToken = [];
        $requestToken['oauth_token'] = $tokenOurs;
        $requestToken['oauth_token_secret'] = $parameters['token_secret'];

        /* Checking validation. */
        if ($tokenOurs !== $parameters['token_request']) {
            throw new TwitterConnectFailed("Error Processing Request", 1);
        }

        /* Setting up connection. */
        $connection = new TwitterOAuth(
            $_ENV['TWITTER_CONSUMER_KEY'],
            $_ENV['TWITTER_CONSUMER_SECRET'],
            $requestToken['oauth_token'],
            $requestToken['oauth_token_secret']
        );

        /* Retreiving access token. */
        try {
            $accessToken = $connection->oauth(
               "oauth/access_token", array("oauth_verifier" => $parameters['verifier']));
        } catch (Abraham\TwitterOAuth\TwitterOAuthException $e) {
            throw new TwitterConnectFailed($e->getMessage(), 1);
        }

        $this->createConnection(
            json_encode(array(
                'oauth_token'       => $accessToken['oauth_token'],
                'oauth_token_secret' => $accessToken['oauth_token_secret'],
            )),
            ''
        );

        /* Creating twitter user */
        $collector = new TwitterDataCollector(Auth::user());
        $twitterUser = new TwitterUser(array(
            'screen_name' => $collector->getUserData()->screen_name
        ));
        $twitterUser->user()->associate(Auth::user());
        $twitterUser->save();

    }

    /**
     * populateData
     * --------------------------------------------------
     * Collecting the initial data from the service.
     * @param array $criteria
     * --------------------------------------------------
     */
    public function populateData($criteria) {
        Queue::push('TwitterPopulateData', array(
            'user_id' => $this->user->id
        ));
    }

} /* TwitterConnector */
