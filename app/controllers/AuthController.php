<?php


/**
 * --------------------------------------------------------------------------
 * AuthController: Handles the authentication related sites
 * --------------------------------------------------------------------------
 */
class AuthController extends BaseController
{

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * getSignin
     * --------------------------------------------------
     * @return Renders the signin page
     * --------------------------------------------------
     */
    public function getSignin()
    {
        /* Redirect if the user has already signed in */
        if (Auth::check()) {
            return Redirect::route('dashboard.dashboard');
        }

        /* Render the page */
        return View::make('auth.signin');
    }

    /**
     * postSignin
     * --------------------------------------------------
     * @return Processes the signin request, signs in the user
     * --------------------------------------------------
     */
    public function postSignin()
    {
        /* Validation */
        $rules = array(
            'email'    => 'required|email',
            'password' => 'required'
        );

        /* run the validation rules on the inputs */
        $validator = Validator::make(Input::all(), $rules);

        /* Everything is OK */
        if ((!$validator->fails()) and (Auth::attempt(Input::only('email', 'password')))) {

            /* Track event | SIGN IN */
            $tracker = new GlobalTracker();
            $tracker->trackAll('lazy', array(
                'en' => 'Sign in',
                'el' => Auth::user()->email)
            );

            /* Make welcome message */
            if (Auth::user()->name) {
                $message = 'Welcome back, '.Auth::user()->name.'!';
            } else {
                $message = 'Welcome back.';
            }

            /* Redirect to dashboard */
            return Redirect::route('dashboard.dashboard')
                    ->with('success', $message);

        /* Something is not OK (bad credentials) */
        } else {
            /* Redirect to signin with error message */
            return Redirect::route('auth.signin')
                ->with('error','The provided email address or password is incorrect.')
                ->withInput(Input::except('password'));
        }
    }

    /**
     * anySignout
     * --------------------------------------------------
     * @return Signs out the user
     * --------------------------------------------------
     */
    public function anySignout()
    {
        /* Sign out the user */
        Auth::logout();

        /* Redirect and add bye message */
        return Redirect::route('dashboard.dashboard')->with('success', 'Good bye.');
    }

    /**
     * postCheckExistingEmail
     * --------------------------------------------------
     * @return Ajax call, to check the provided email address
     *      if it exists in the database or not.
     * --------------------------------------------------
     */
    public function postCheckExistingEmail()
    {
        /* Initialize status */
        $status = true;

        $checkedEmail = Input::json()->get('email');
        $existingUser = User::where('email', $checkedEmail)->count();

        /* Return (AJAX CALL) */
        if (Request::ajax()) {
            if ($existingUser) {
                return Response::json(array('email-taken' => true));
            } else {
                return Response::json(array('email-taken' => false));
            }
        }
    }

} /* AuthController */
