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

class DevController extends BaseController
{

    public function showEmail($whichOne)
    {
        return View::make('email.notification.connected');
        return View::make('email.notification.summary');
        return View::make('email.payment.downgrade');
        return View::make('email.payment.upgrade');
    }

    public function showTesting() {
        Log::info("TESTING");
        Log::info(User::find(1)->dashboards->all());
        return "hello";
    }

    public function show()
    {
        $encrypted = Crypt::encrypt(3);
        $encrypted_start = substr($encrypted,0,12);
        $encrypted_end = substr($encrypted,176,12);
        $decrypted = Crypt::decrypt($encrypted);
    }

    public function showTest()
    {
        return View::make('dev.test',array(
        //    'time' => $time
        ));
    }
    /*
    |====================================================
    | <GET> | showUsers: showing the current users
    |====================================================
    */
    public function showUsers()
    {
        // returning the current users
        return View::make(
            'dev.users',
            array(
                'users' => User::all()
            )
        );
    }




    public function showBraintree()
    {
        $clientToken = 'eyJ2ZXJzaW9uIjoyLCJhdXRob3JpemF0aW9uRmluZ2VycHJpbnQiOiIxZWQ5MWFiZmVhMzRmNTcyYTE2NjRjYTVjMTU5OTllMmNjOTZlMzE0MmI5ZTY3NTNmMGNhN2U2MzQ2ZDdhYTc3fGNyZWF0ZWRfYXQ9MjAxNS0wNS0yNlQwODo1MzowNy41NjI3NjkzMTIrMDAwMFx1MDAyNm1lcmNoYW50X2lkPWRjcHNweTJicndkanIzcW5cdTAwMjZwdWJsaWNfa2V5PTl3d3J6cWszdnIzdDRuYzgiLCJjb25maWdVcmwiOiJodHRwczovL2FwaS5zYW5kYm94LmJyYWludHJlZWdhdGV3YXkuY29tOjQ0My9tZXJjaGFudHMvZGNwc3B5MmJyd2RqcjNxbi9jbGllbnRfYXBpL3YxL2NvbmZpZ3VyYXRpb24iLCJjaGFsbGVuZ2VzIjpbXSwiZW52aXJvbm1lbnQiOiJzYW5kYm94IiwiY2xpZW50QXBpVXJsIjoiaHR0cHM6Ly9hcGkuc2FuZGJveC5icmFpbnRyZWVnYXRld2F5LmNvbTo0NDMvbWVyY2hhbnRzL2RjcHNweTJicndkanIzcW4vY2xpZW50X2FwaSIsImFzc2V0c1VybCI6Imh0dHBzOi8vYXNzZXRzLmJyYWludHJlZWdhdGV3YXkuY29tIiwiYXV0aFVybCI6Imh0dHBzOi8vYXV0aC52ZW5tby5zYW5kYm94LmJyYWludHJlZWdhdGV3YXkuY29tIiwiYW5hbHl0aWNzIjp7InVybCI6Imh0dHBzOi8vY2xpZW50LWFuYWx5dGljcy5zYW5kYm94LmJyYWludHJlZWdhdGV3YXkuY29tIn0sInRocmVlRFNlY3VyZUVuYWJsZWQiOnRydWUsInRocmVlRFNlY3VyZSI6eyJsb29rdXBVcmwiOiJodHRwczovL2FwaS5zYW5kYm94LmJyYWludHJlZWdhdGV3YXkuY29tOjQ0My9tZXJjaGFudHMvZGNwc3B5MmJyd2RqcjNxbi90aHJlZV9kX3NlY3VyZS9sb29rdXAifSwicGF5cGFsRW5hYmxlZCI6dHJ1ZSwicGF5cGFsIjp7ImRpc3BsYXlOYW1lIjoiQWNtZSBXaWRnZXRzLCBMdGQuIChTYW5kYm94KSIsImNsaWVudElkIjpudWxsLCJwcml2YWN5VXJsIjoiaHR0cDovL2V4YW1wbGUuY29tL3BwIiwidXNlckFncmVlbWVudFVybCI6Imh0dHA6Ly9leGFtcGxlLmNvbS90b3MiLCJiYXNlVXJsIjoiaHR0cHM6Ly9hc3NldHMuYnJhaW50cmVlZ2F0ZXdheS5jb20iLCJhc3NldHNVcmwiOiJodHRwczovL2NoZWNrb3V0LnBheXBhbC5jb20iLCJkaXJlY3RCYXNlVXJsIjpudWxsLCJhbGxvd0h0dHAiOnRydWUsImVudmlyb25tZW50Tm9OZXR3b3JrIjp0cnVlLCJlbnZpcm9ubWVudCI6Im9mZmxpbmUiLCJ1bnZldHRlZE1lcmNoYW50IjpmYWxzZSwiYnJhaW50cmVlQ2xpZW50SWQiOiJtYXN0ZXJjbGllbnQzIiwibWVyY2hhbnRBY2NvdW50SWQiOiJzdGNoMm5mZGZ3c3p5dHc1IiwiY3VycmVuY3lJc29Db2RlIjoiVVNEIn0sImNvaW5iYXNlRW5hYmxlZCI6dHJ1ZSwiY29pbmJhc2UiOnsiY2xpZW50SWQiOiIxMWQyNzIyOWJhNThiNTZkN2UzYzAxYTA1MjdmNGQ1YjQ0NmQ0ZjY4NDgxN2NiNjIzZDI1NWI1NzNhZGRjNTliIiwibWVyY2hhbnRBY2NvdW50IjoiY29pbmJhc2UtZGV2ZWxvcG1lbnQtbWVyY2hhbnRAZ2V0YnJhaW50cmVlLmNvbSIsInNjb3BlcyI6ImF1dGhvcml6YXRpb25zOmJyYWludHJlZSB1c2VyIiwicmVkaXJlY3RVcmwiOiJodHRwczovL2Fzc2V0cy5icmFpbnRyZWVnYXRld2F5LmNvbS9jb2luYmFzZS9vYXV0aC9yZWRpcmVjdC1sYW5kaW5nLmh0bWwiLCJlbnZpcm9ubWVudCI6Im1vY2sifSwibWVyY2hhbnRJZCI6ImRjcHNweTJicndkanIzcW4iLCJ2ZW5tbyI6Im9mZmxpbmUiLCJhcHBsZVBheSI6eyJzdGF0dXMiOiJtb2NrIiwiY291bnRyeUNvZGUiOiJVUyIsImN1cnJlbmN5Q29kZSI6IlVTRCIsIm1lcmNoYW50SWRlbnRpZmllciI6Im1lcmNoYW50LmNvbS5icmFpbnRyZWVwYXltZW50cy5zYW5kYm94LkJyYWludHJlZS1EZW1vIiwic3VwcG9ydGVkTmV0d29ya3MiOlsidmlzYSIsIm1hc3RlcmNhcmQiLCJhbWV4Il19fQ';
        return View::make('dev.braintree',array(
            'clientToken' => $clientToken));
    }

    public function doBraintreePayment()
    {
        if(Input::has('payment_method_nonce'))
        {

            $plans = BraintreeHelper::getPlanDictionary();

            $planName = 'public';

            $result = Braintree_Subscription::create(array(
                'planId'             => $plans[$planName]->id,
                'paymentMethodNonce' => Input::get('payment_method_nonce'))
            );

            return View::make('dev.braintree',array(
                'result' => $result
            ));
        }
    }




    /*
    |====================================================
    | <GET> | showPaypal: renders the paypal testing page
    |====================================================
    */
    public function showPaypal()
    {
        $api_context = PayPalHelper::getApiContext();
        try {
            $params = array('access_token' => PayPalHelper::generateAccessTokenFromRefreshToken(Auth::user()->paypal_key));
            $user = OpenIdUserinfo::getUserinfo($params, $api_context);
        } catch (Exception $ex) {
            print "no pp key";
        }
        /*
        // Create a new instance of Plan object
        $plan = new Plan();

        // # Basic Information
        // Fill up the basic information that is required for the plan
        $plan->setName('Welltakeyourmoney')
            ->setDescription('If you register we can take all your money.')
            ->setType('fixed');

        $paymentDefinition = new PaymentDefinition();

        // The possible values for such setters are mentioned in the setter method documentation.
        // Just open the class file. e.g. lib/PayPal/Api/PaymentDefinition.php and look for setFrequency method.
        // You should be able to see the acceptable values in the comments.
        $paymentDefinition->setName('Regular Payments')
            ->setType('REGULAR')
            ->setFrequency('Month')
            ->setFrequencyInterval("5")
            ->setCycles("12")
            ->setAmount(new Currency(array('value' => 999, 'currency' => 'USD')));
        // ### Create Plan
        $merchantPreferences = new MerchantPreferences();

        $merchantPreferences->setReturnUrl(route('auth.dashboard'))
            ->setCancelUrl(route('auth.dashboard'))
            ->setAutoBillAmount("yes")
            ->setInitialFailAmountAction("CONTINUE")
            ->setMaxFailAttempts("0")
            ->setSetupFee(new Currency(array('value' => 9999, 'currency' => 'USD')));

        $plan->setPaymentDefinitions(array($paymentDefinition));
        $plan->setMerchantPreferences($merchantPreferences);

        $request = clone $plan;

        try {
            $output = $plan->create($api_context);
        } catch (PayPal\Exception\PPConnectionException $ex) {
            echo '<pre>';print_r(json_decode($ex->getData()));
            exit(1);
        }
        */

        try {
            $params = array('page_size' => '20');
            $planList = Plan::all($params, $api_context);
        } catch (PayPal\Exception\ConnectionException $ex) {
            echo '<pre>';print_r(json_decode($ex->getData()));
            exit(1);
        }
        Log::info($planList);

        try {
            $plan = Plan::get("P-0TA38541GG196850X3XYQ2KI", $api_context);
        } catch (PayPal\Exception\ConnectionException $ex) {
            echo '<pre>';print_r(json_decode($ex->getData()));
            exit(1);
        }

        return View::make(
            'dev.paypal',
            array('output' => $plan)
        );

    }
}
