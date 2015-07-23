<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;

class User extends Eloquent implements UserInterface
{
    /* UserTrait implements the functions from UserInterface */
    use UserTrait;

    /* -- Fields -- */
    protected $guarded = array(
        'password',
        'remember_token',
    );

    protected $fillable = array(
        'email',
        'name',
        'gender',
        'phone_number',
        'date_of_birth'
    );

    /* -- Relations -- */
    public function connections() { return $this->hasMany('Connection'); }
    public function subscriptions() { return $this->hasMany('Subscriptions'); }
    public function dashboards() { return $this->hasMany('Dashboard'); }
    public function settings() { return $this->hasOne('Settings'); }
    public function stripePlans() { return $this->hasMany('StripePlan'); }

    /**
     * isStripeConnected
     * --------------------------------------------------
     * Testing if the user has connected a stripe account.
     * @return boolean
     * --------------------------------------------------     
     */
    public function isStripeConnected() {
        if ($this->connections()->where('service', 'stripe')
                                ->first() !== null) {
            return True;
        }
        return False;
    }

     /**
     * isBraintreeConnected
     * --------------------------------------------------
     * Testing if the user has connected a braintree account
     * @return boolean
     * --------------------------------------------------
     */
    public function isBraintreeConnected() {
        return False;
    }


    /**
    * 
    * --------------------------------------------------
    * @todo clean the lines below
    * --------------------------------------------------
    */
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
