<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;


class User extends Eloquent implements UserInterface
{
    protected $guarded = array();

    // DEFINE RELATIONSHIPS --------------------------------------------------
    // each user has many connection
    public function connections() {
        return $this->hasMany('Connection');
    }

    // each user BELONGS to many dashboards
    public function dashboards() {
        return $this->belongsToMany('Dashboard', 'users_dashboards', 'user_id', 'dashboard_id');
    }

    use UserTrait;
    /**
     * Testing if the user has connected a stripe account
     *
     * @return boolean
    */
    public function isStripeConnected()
    {
        // at this point validation like this is all right
        if (strlen($this->stripe_key) > 16 
            || strlen($this->stripeUserId) > 1) {
            // long enough key
            return True;
        }
        // no key is given
        return False;
    }

     /**
     * Testing if the user has connected a paypal account
     *
     * @return boolean
    */
    public function isPayPalConnected()
    {
        // at this point validation like this is all right
        if (strlen($this->paypal_key) > 16) {
            // refreshtoken is longer than 16
            return True;
        }
        // no valid refreshtoken is stored
        return False;
    }

    public function isGoogleSpreadsheetConnected()
    {
        // at this point validation like this is all right
        if (strlen($this->googleSpreadsheetRefreshToken) > 1) {
            // long enough key
            return True;
        }
        // no key is given
        return False;
    }


    /**
     * Testing if the user has connected at least one financial account
     *
     * @return boolean
    */
    public function isFinancialStuffConnected()
    {
        if ($this->isStripeConnected() 
            || $this->isPayPalConnected()
            ) 
        {
            // connected
            return True;
        }
        // not connected
        return False;
    }


    /**
     * Testing if the user has connected at least one account
     *
     * @return boolean
    */
    public function isConnected()
    {
        if ($this->isStripeConnected() 
            || $this->isPayPalConnected()
            || $this->isBraintreeConnected() 
            || $this->isGoogleSpreadsheetConnected()
            ) 
        {
            // connected
            return True;
        }
        // not connected
        return False;
    }

    public function isBraintreeConnected()
    {
        if ($this->isBraintreeCredentialsValid() && $this->btWebhookConnected && $this->ready=='connected')
        {
            return true;
        }

        return false;
    }

    public function isBraintreeCredentialsValid()
    {
        if (strlen($this->btPublicKey) > 2)
        {
            return true;
        }

        return false;
    }


    /*
    |-------------------------------------
    | Trial checking helpers
    |-------------------------------------
    */

    public function isTrialEnded()
    {
        $trialEndDate = Carbon::parse($this->trial_started)->addDays(30);

        if ($this->plan == 'trial' && $trialEndDate->isPast()){
            $this->detachPremiumWidgets();
            return true;
        } 
        else if ($this->plan == 'trial_ended'){
            return true;
        }
        else 
        {
            return false;
        }
    }

    public function trialWillEndInDays($days)
    {   
        $daysRemaining = $this->daysRemaining();

        if ($this->plan == 'trial' && $daysRemaining < $days)
        {
            return true;
        } else {
            return false;
        }
    }

    public function trialWillEndExactlyInDays($days)
    {
        $daysRemaining = $this->daysRemaining();

        if (($this->plan == 'trial' || $this->plan == 'trial_ended') && $daysRemaining == $days)
        {
            return true;
        } else {
            return false;
        }
    }

    public function daysRemaining()
    {
        $days = 100;

        $now = Carbon::now();
        $signup = Carbon::parse($this->trial_started);

        $days = $now->diffInDays($signup->addDays(30), false);

        return $days;
    }


    /*
    |------------------------------------------
    | Connected services checking
    |------------------------------------------
    */

    public function canConnectMore()
    {
        if($this->paymentStatus == 'overdue')
        {
            // user is a paying customer, but its payment is overdue
            // don't let more connections
            return false;
        }
        if($this->plan != 'free')
        {
            // the user is good paying customer (or trial period, whatever), 
            // let him/her connect more
            return true;
        } elseif($this->connectedServices < $_ENV['MAX_FREE_CONNECTIONS'])
        {
            // not yet reached the maximum number of allowed connections
            return true;
        } else
        {
            // the user is not paying (or trial ended), 
            // and reached maximum number of allowed connections
            // don't let more connections
            return false;
        }
    }


    /*
    |------------------------------------------
    | App background for user
    |------------------------------------------
    */

    public function dailyBackgroundURL() {

        # get the number of day in the year
        $numberOfDayInYear = date('z');

        # if there is backgrounds-production directory, go with that, otherwise go with backgrounds 
        # (backgrounds-production is too large to be included in the git repository)

        $directory = '/img/backgrounds-production/';
        if (!file_exists(public_path().$directory)) {
            $directory = '/img/backgrounds/';
        }

        # get the number of background images & collect them in an array
        $i = 0;
        $fileListArray = array();
        $dir = public_path().$directory;

        if ($handle = opendir($dir)) {
            while (($file = readdir($handle)) !== false){
                if (!in_array($file, array('.', '..')) && !is_dir($dir.$file) && !(substr($file, 0, 1 ) === ".")) {
                    $fileListArray = array_add($fileListArray, $i, $file);                    
                    $i++;
                }
            }
        }
        $numberOfBackgroundFiles = $i;

        # calculate which image will we use
        $imageNumber = $numberOfDayInYear % $numberOfBackgroundFiles;

        # create the url that will be passed to the view
        $imageName = $fileListArray[$imageNumber];
        $dailyBackgroundURL = $directory.$imageName;

        return $dailyBackgroundURL;

    }

    /*
    |-------------------------------------
    | Widget detach helper
    |-------------------------------------
    */

    public function detachPremiumWidgets () {
         
        foreach ($this->dashboards as $dashboard){
            $widgets = Widget::where('dashboard_id','=', $dashboard->pivot->dashboard_id)->get();
            Log::info($widgets);
            foreach ($widgets as $widget){
               
                if((strpos($widget->widget_type, 'google-spreadsheet') !== false ) ||
                        ($widget->widget_type == 'api')){
                    $widget->delete();
                }
            }
            $dashboard->save();
        }

        $this->plan == 'trial_ended';
    }

}
