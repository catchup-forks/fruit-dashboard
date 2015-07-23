<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

if (!App::environment('local'))
{
    App::before(function($request)
    {
        if(!Request::secure())
        {
            return Redirect::secure(Request::path());
        }
    });
}


App::after(function($request, $response)
{
    //
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
    # not auth, ajax request
    if (Auth::guest() && Request::ajax()) {
        return Response::make('Unauthorized', 401);
    }
    # not auth
    if (Auth::guest()) {
        return Redirect::route('auth.signin');
    }
    # auth, but with user id 1
    if (Auth::check() && Auth::user()->id == 1) {
        Auth::logout();
        return Redirect::route('auth.signin');
    }
});


Route::filter('auth.basic', function()
{
    return Auth::basic();
});

Route::filter('api_key', function() { 
    // if (!Auth::user()->isConnected())
    // {
    //     // no valid key
    //     return Redirect::route('connect.connect')
    //         ->with(Session::all())
    //         ->with('error','Connect a payment provider');
    // }
});

Route::filter('cancelled', function()
{
    // if (Auth::user()->plan == 'cancelled')
    // {
    //     return Redirect::route('payment.plan')
    //         ->with('error','Please subscribe.');
    // }
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
    if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
    if (Session::token() !== Input::get('_token'))
    {
        throw new Illuminate\Session\TokenMismatchException;
    }
});
