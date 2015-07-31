<?php

class ControllerTest extends TestCase
{

    public function setUp()
    {
        // calling parent setUp()
        parent::setUp();
        // we'll need a user for each view
        // we're not using the seeded one
        // creating a new with a valid stripe key
        User::create(array(
            'id'       => '42',
            'email'    => 'AppXplorer@test.com',
            'password' => Hash::make('supersecret'),
            'balance' => 4107088039,
            'stripe_key'=> 'sk_test_YOhLG7AgROpHWUyr62TlGXmg',
        ));
        // creating a new without a valid stripe key
        User::create(array(
            'id'       => '12',
            'email'    => 'AppXplorer@test2.com',
            'password' => Hash::make('supersecret'),
        ));
        Route::enableFilters();
    }


    /**
     * Testing the auth.* views
     *
     * @return void
    */
    public function testAuthViews()
    {
        // building redirect matrix:
        $get_route_array = array(
            array(
                'action' => 'showSignup',
                'striped_user' => array('code' => '302', 'redirect_url' => route('auth.addkey')),
                'non_striped_user' => array('code' => '302', 'redirect_url' => route('auth.addkey')),
                'nlg' => array('code' => '200'),
            ),
            array(
                'action' => 'showSignin',
                'striped_user' => array('code' => '302', 'redirect_url' => route('auth.addkey')),
                'non_striped_user' => array('code' => '302', 'redirect_url' => route('auth.addkey')),
                'nlg' => array('code' => '200'),
            ),
            array(
                'action' => 'doSignout',
                'striped_user' => array('code' => '302', 'redirect_url' => route('auth.signin')),
                'non_striped_user' => array('code' => '302', 'redirect_url' => route('auth.signin')),
                'nlg' => array('code' => '302', 'redirect_url' => route('auth.signin')),
            ),
            array(
                'action' => 'showAddKey',
                'striped_user' => array('code' => '302', 'redirect_url' => route('development.stripe')),
                'non_striped_user' => array('code' => '200'),
                'nlg' => array('code' => '302', 'redirect_url' => route('auth.signin')),
            ),

        );
        // running the tests on controllers
        $this->runTests($get_route_array, "AuthController");
    }


    /**
     * HELPER: checks the status code and redirect
     *
     * @return void
    */
    private function checkUser($route, $user, $response)
    {
        if ($route[$user]['code'] == 200) {
            // should be ok
            $this->assertResponseOK();
        } elseif ($route[$user]['code'] == 302) {
            // should be redirect
            $this->assertRedirectedTo($route[$user]['redirect_url']);
        }
    }
    /**
     * this function runs a test on a redirect array
     *
     * @return void
    */
    private function runTests($status_array, $controller)
    {
        // going through the array
        foreach ($status_array as $route) {
            // first testing nlg
            // building up controller string
            $cntrl = $controller."@".$route['action'];
            // if we have parameteres set the response accordingly
            if (isset($route['parameters'])) {
                $response = $this->action('GET', $cntrl, $route['parameters']);
            } else {
                $response = $this->action('GET', $cntrl);
            }
            // testing the status code
            $this->checkUser($route, 'nlg', $response);

            // logging in (not using signin/signout)
            $this->be(User::find(42)); //striped user

            // calling the same response
            if (isset($route['parameters'])) {
                $response = $this->action('GET', $cntrl, $route['parameters']);
            } else {
                $response = $this->action('GET', $cntrl);
            }
            // testing status code
            $this->checkUser($route, 'striped_user', $response);

            // logging out
            Auth::logout();

            // logging in (not using signin/signout)
            $this->be(User::find(12)); //non-striped user

            // calling the same response
            if (isset($route['parameters'])) {
                $response = $this->action('GET', $cntrl, $route['parameters']);
            } else {
                $response = $this->action('GET', $cntrl);
            }
            // testing status code
            $this->checkUser($route, 'non_striped_user', $response);

            // logging out
            Auth::logout();
        }
    }

    public function tearDown()
    {
        Route::disableFilters();
    }
}
