<?php


/**
 * --------------------------------------------------------------------------
 * SettingsController: Handles the settings related sites
 * --------------------------------------------------------------------------
 */
class SettingsController extends BaseController
{
    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * anySettings
     * --------------------------------------------------
     * @return Renders the settings page
     * --------------------------------------------------
     */
    public function anySettings() {
        /* Render view */
        return View::make('settings.settings');
    }

    /**
     * postSettingsChange
     * --------------------------------------------------
     * @param (string) ($attrName) The name of the attribute
     * @return Changes an attribute coming from th url
     * --------------------------------------------------
     */
    public function postSettingsChange($attrName) {
        /* Get the attribute, and call handler function */
        switch ($attrName) {
            case 'name':
                return $this->changeUserName(Input::all());
                break;
            case 'email':
                return $this->changeUserEmail(Input::all());
                break;
            case 'background-enabled':
                return $this->changeBackgroundEnabled(Input::all());
                break;
            case 'background-change':
                return $this->changeBackground(Input::all());
                break;
            default:
                return Redirect::route('settings.settings');
        }
    }

    /**
     * postTimeZone
     * --------------------------------------------------
     * @return Sets the user TimeZone and stores to the session.
     * --------------------------------------------------
     */
    public function postTimeZone() {
        /* Get timezone from the POST data */
        if (Input::get('timeZone')) {
            Session::put('timeZone', Input::get('timeZone'));
        }

        /* Return empty json */
        return Response::json();
    }

    /**
     * ================================================== *
     *                   PRIVATE SECTION                  *
     * ================================================== *
     */

    /**
     * changeUserName
     * --------------------------------------------------
     * @param (array) ($postData) The POST data
     * @return Changes the user name
     * --------------------------------------------------
     */
    private function changeUserName($postData)
    {
        /* Initialize status */
        $status = true;

        /* Get the user and necessary object(s) */
        $user = Auth::user();

        /* Get the new attribute(s) */
        $newattr = $postData['name'];

        /* Change the attribute(s) */
        $user->name = $newattr;

        /* Save object(s) */
        $user->save();

        /* Return */
        /* AJAX CALL */
        if (Request::ajax()) {
            if ($status) {
                /* Everything OK, return empty json */
                return Response::json(array('success' => 'You successfully modified your name.'));
            } else {
                /* Something went wrong, send error */
                return Response::json(array('error' => 'Something went wrong with changing your name. Please try again.'));
            }
        /* POST */
        } else {
            if ($status) {
                return Redirect::route('settings.settings')
                    ->with('success', 'You successfully modified your name.');
            } else {
                return Redirect::route('settings.settings')
                    ->with('error', 'Something went wrong with changing your name. Please try again.');
            }
        }
    }

    /**
     * changeUserEmail
     * --------------------------------------------------
     * @param (array) ($postData) The POST data
     * @return Changes the user email
     * --------------------------------------------------
     */
    private function changeUserEmail($postData)
    {
        /* Initialize status */
        $status = true;

        /* Get the user and necessary object(s) */
        $user = Auth::user();

        /* Get the new attribute(s) */
        $newattr = $postData['email'];

        /* Change the attribute(s) */
        $user->email = $newattr;

        /* Save object(s) */
        $user->save();

        /* Return */
        /* AJAX CALL */
        if (Request::ajax()) {
            if ($status) {
                /* Everything OK, return empty json */
                return Response::json(array('success' => 'You successfully modified your email address.'));
            } else {
                /* Something went wrong, send error */
                return Response::json(array('error' => 'Something went wrong with changing your email address. Please try again.'));
            }

        /* POST */
        } else {
            if ($status) {
                return Redirect::route('settings.settings')
                    ->with('success', 'You successfully modified your email address.');
            } else {
                return Redirect::route('settings.settings')
                    ->with('error', 'Something went wrong with changing your email address. Please try again.');
            }
        }
    }

    /**
     * changeBackgroundEnabled
     * --------------------------------------------------
     * @param (array) ($postData) The POST data
     * @return Changes the background enabled option
     * --------------------------------------------------
     */
    private function changeBackgroundEnabled($postData)
    {
        /* Initialize status */
        $status = true;

        /* Get the user and necessary object(s) */
        $background = Auth::user()->background;

        /* Get the new attribute(s) */
        $newattr = $postData['background-enabled'];

        /* Change the attribute(s) */
        $background->is_enabled = (bool)$newattr;

        /* Save object(s) */
        $background->save();

        /* Update dashboard cache */
        Auth::user()->updateDashboardCache();

        /* Return */
        /* AJAX CALL */
        if (Request::ajax()) {
            if ($status) {
                /* Everything OK, return empty json */
                return Response::json(array('success' => 'You successfully modified your background settings.'));
            } else {
                /* Something went wrong, send error */
                return Response::json(array('error' => 'Something went wrong with changing your background settings. Please try again.'));
            }
        /* POST */
        } else {
            if ($status) {
                return Redirect::route('settings.settings')
                    ->with('success', 'You successfully modified your background settings.');
            } else {
                return Redirect::route('settings.settings')
                    ->with('error', 'Something went wrong with changing your background settings. Please try again.');
            }
        }

    }

    /**
     * changeBackground
     * --------------------------------------------------
     * @param (array) ($postData) The POST data
     * @return Changes the background
     * --------------------------------------------------
     */
    private function changeBackground($postData)
    {
        /* Initialize status */
        $status = true;

        /* Get the user and necessary object(s) */
        $background = Auth::user()->background;

        /* Change the url(s) */
        $background->changeUrl();

        /* Save object(s) */
        $background->save();

        /* Update dashboard cache */
        Auth::user()->updateDashboardCache();

        /* Return */
        /* AJAX CALL */
        if (Request::ajax()) {
            if ($status) {
                /* Everything OK, return empty json */
                return Response::json(array('success' => 'You successfully changed your background.'));
            } else {
                /* Something went wrong, send error */
                return Response::json(array('error' => 'Something went wrong with changing your background. Please try again.'));
            }
        /* POST */
        } else {
            if ($status) {
                return Redirect::route('settings.settings')
                    ->with('success', 'You successfully changed your background.');
            } else {
                return Redirect::route('settings.settings')
                    ->with('error', 'Something went wrong with changing your background. Please try again.');
            }
        }

    }

    /**
     * --------------------------------------------------
     * @todo Remove the lines below
     * --------------------------------------------------
     */

    /*
    |===================================================
    | <POST> | doSettings: updates user data
    |===================================================
    */
    public function doSettingsName()
    {
        // Validation rules
        $rules = array(
            'name' => 'required',
            );
        // run the validation rules on the inputs
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            // validation error -> redirect
            $failedAttribute = $validator->invalid();
            return Redirect::to('/settings')
                ->with('error',$validator->errors()->get(key($failedAttribute))[0]) // send back errors
                ->withInput(); // sending back data
        } else {
            // validator success -> edit_profile
            // selecting logged in user
            $user = Auth::user();

            $user->name = Input::get('name');

            $user->save();
            // setting data
            return Redirect::to('/settings')
                ->with('success', 'Nice to have you here, '.$user->name.'.');
        }
    }

    public function doSettingsCountry()
    {
        // Validation rules
        $rules = array(
            'country' => 'required',
            );

        // run the validation rules on the inputs
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            // validation error -> redirect
            $failedAttribute = $validator->invalid();
            return Redirect::to('/settings')
                ->with('error',$validator->errors()->get(key($failedAttribute))[0]) // send back errors
                ->withInput(); // sending back data
        } else {

            // selecting logged in user
            $user = Auth::user();
            // if we have zoneinfo
            // changing zoneinfo
            $user->zoneinfo = Input::get('country');
            // saving user
            $user->save();

            // redirect to settings
            return Redirect::to('/settings')
                ->with('success', 'Edit was successful.');
        }
    }

    public function doSettingsEmail()
    {
        // Validation rules
        $rules = array(
            'email' => 'required|unique:users,email|email',
            'email_password' => 'required|min:4',
            );
        // run the validation rules on the inputs
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            // validation error -> redirect
            $failedAttribute = $validator->invalid();
            return Redirect::to('/settings')
                ->with('error',$validator->errors()->get(key($failedAttribute))[0]) // send back errors
                ->withInput(); // sending back data
        } else {
            // validator success -> edit_profile
            // selecting logged in user
            $user = Auth::user();

            // we need to check the password
            if (Hash::check(Input::get('email_password'), $user->password)){
                $user->email = Input::get('email');
            }

            $user->save();
            // setting data
            return Redirect::to('/settings')
                ->with('success', 'Edit was successful.');
        }
    }

    public function doSettingsPassword()
    {
        // Validation rules
        $rules = array(
            'old_password' => 'required|min:4',
            'new_password' => 'required|confirmed|min:4',
        );
        // run the validation rules on the inputs
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            // validation error -> redirect
            $failedAttribute = $validator->invalid();
            return Redirect::to('/settings')
                ->with('error',$validator->errors()->get(key($failedAttribute))[0]) // send back errors
                ->withInput(); // sending back data
        } else {
            // validator success -> edit_profile
            // selecting logged in user
            $user = Auth::user();

            // if we have data from the password change form
            // checking if old password is the old password
            if (Hash::check(Input::get('old_password'), $user->password)){
                $user->password = Hash::make(Input::get('new_password'));
            }
            else {
                return Redirect::to('/settings')
                    ->with('error', 'The old password you entered is incorrect.'); // send back errors
            }

            $user->save();
            // setting data
            return Redirect::to('/settings')
                ->with('success', 'Edit was successful.');
        }
    }

    public function doSettingsFrequency()
    {
        $user = Auth::user();

        $user->summaryEmailFrequency = Input::get('new_frequency');

        $user->save();

        return Redirect::to('/settings')
            ->with('success', 'Edit was succesful.');
    }

}

