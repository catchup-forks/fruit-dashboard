<?php


/*
|--------------------------------------------------------------------------
| AuthController: Handles the authentication related sites
|--------------------------------------------------------------------------
*/
class AuthController extends BaseController
{

    /*
    |===================================================
    | <GET> | showSignin: renders the signin page
    |===================================================
    */
    public function showSignin()
    {
        if (Auth::check()) {
            Auth::logout();
        }
        $user = User::find(1);
        
        return View::make('auth.signin',
            array(
                // background stuff
                'isBackgroundOn' => $user->isBackgroundOn,
                'dailyBackgroundURL' => $user->dailyBackgroundURL(), 
            )
        );
    }

    /*
    |===================================================
    | <POST> | doSignin: signs in the user
    |===================================================
    */
    public function doSignin()
    {
        // Validation
        $rules = array(
            'email'    => 'required|email',
            'password' => 'required'
        );

        // run the validation rules on the inputs
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            // validation error -> redirect
            return Redirect::route('auth.signin')
                ->with('error','Email address or password is incorrect.') // send back errors
                ->withInput(Input::except('password')); // sending back data
        } else {
            // validator success -> signin
            $credentials = Input::only('email', 'password');

            // attempt to do the login
            if (Auth::attempt($credentials)) {
                // auth successful!

                // if user has no dashboards created yet
                if (Auth::user()->dashboards->count() == 0)
                {
                    // create first dashboard for user
                    $dashboard = new Dashboard;
                    $dashboard->dashboard_name = "Dashboard #1";
                    $dashboard->save();

                    // attach dashboard & user
                    Auth::user()->dashboards()->attach($dashboard->id, array('role' => 'owner'));
                }

                # be more personal
                if (Auth::user()->name) {
                    $message = 'Welcome back, '.Auth::user()->name.'!';
                } else {
                    $message = 'Welcome back.';
                }

                return Redirect::route('dashboard.dashboard')
                    ->with('success', $message);
                    
            } else {
                // auth unsuccessful -> redirect to login
                return Redirect::route('auth.signin')
                    ->withInput(Input::except('password'))
                    ->with('error', 'Email address or password is incorrect.');
            }
        }
    }

    /*
    |===================================================
    | <GET> | showSignup: renders the signup page
    |===================================================
    */
    public function showSignup()
    {
        if (Auth::check()) {
            Auth::logout();
        } 
        $user = User::find(1);
        return View::make('auth.signup',
            array(
                // background stuff
                'isBackgroundOn' => $user->isBackgroundOn,
                'dailyBackgroundURL' => $user->dailyBackgroundURL(), 
            )
        );
    }

    /*
    |===================================================
    | <POST> | doSignin: signs up the user
    |===================================================
    */
    public function doSignup()
    {
        // Validation rules
        $rules = array(
            'email' => 'required|email|unique:users',
            'password' => 'required|min:4',
        );

        // run the validation rules on the inputs
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            // validation error -> redirect
            
            $failedAttribute = $validator->invalid();

            return Redirect::route('auth.signup')
                //->withErrors($validator)
                ->with('error', $validator->errors()->get(key($failedAttribute))[0]) // send back errors
                ->withInput(); // sending back data

        } else {
            // validator success -> signup

            // create user
            $user = new User;

            // set auth info
            $user->email = Input::get('email');
            $user->password = Hash::make(Input::get('password'));
            // if name input
            $user->name = Input::get('name');
            $user->ready = 'notConnected';
            $user->summaryEmailFrequency = 'daily';
            $user->plan = 'free';
            $user->connectedServices = 0;
            $user->save();

            // create first dashboard for user
            $dashboard = new Dashboard;
            $dashboard->dashboard_name = "Dashboard #1";
            $dashboard->save();

            // attach dashboard & user
            $user->dashboards()->attach($dashboard->id, array('role' => 'owner'));

            //
            // create default widgets

            // clock widget
            $widget = new Widget;
            $widget->widget_name = 'clock widget';
            $widget->widget_type = 'clock';
            $widget->widget_source = '{}';
            $widget->position = '{"size_x":8,"size_y":6,"col":1,"row":1}';
            $widget->dashboard_id = $user->dashboards()->first()->id;
            $widget->save();

            // greeting widget
            $widget = new Widget;
            $widget->widget_name = 'greeting widget';
            $widget->widget_type = 'greeting';
            $widget->widget_source = '{}';
            $widget->position = '{"size_x":6,"size_y":3,"col":2,"row":10}';
            $widget->dashboard_id = $user->dashboards()->first()->id;
            $widget->save();

            // quote widget
            $widget = new Widget;
            $widget->widget_name = 'quote widget';
            $widget->widget_type = 'quote';
            $widget_data = array(
                'type'      =>  'quote-inspirational',
                'refresh'   =>  'daily',
                'language'   =>  'english'
            );
            $widget_json = json_encode($widget_data);
            $widget->widget_source = $widget_json;
            $widget->position = '{"size_x":6,"size_y":2,"col":2,"row":7}';
            $widget->dashboard_id = $user->dashboards()->first()->id;
            $widget->save();


            // create user on intercom
            IntercomHelper::signedup($user);

            // signing the user in and redirect to dashboard
            Auth::login($user);
            return Redirect::route('dashboard.dashboard')
                ->with('success', 'Welcome to your new dashboard :)');
        }
    }

    /*
    |===================================================
    | <ANY> | doSignout: signs out the user
    |===================================================
    */
    public function doSignout()
    {
        Auth::logout();
        return Redirect::route('dashboard.dashboard')->with('success', 'Good bye.');
    }
}
