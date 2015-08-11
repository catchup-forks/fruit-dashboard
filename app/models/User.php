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
    public function subscription() { return $this->hasOne('Subscription'); }
    public function dashboards() { return $this->hasMany('Dashboard'); }
    public function settings() { return $this->hasOne('Settings'); }

    /* -- Libraries -- */
    public function stripePlans() { return $this->hasMany('StripePlan', 'user_id'); }
    public function braintreePlans() { return $this->hasMany('BraintreePlan'); }

    /* -- Custom relations. -- */
    public function widgets() {
        $widgets = array();
        foreach ($this->dashboards as $dashboard) {
            foreach ($dashboard->widgets as $widget) {
                array_push($widgets, $widget->getSpecific());
            }
        }
        return $widgets;
    }
    /**
     * isStripeConnected
     * --------------------------------------------------
     * Returns true if the user has connected a stripe account
     * @return (boolean) ($status)
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
     * Returns true if the user has connected a braintree account
     * @return (boolean) ($status)
     * --------------------------------------------------
     */
    public function isBraintreeConnected() {
        if ($this->connections()->where('service', 'braintree')
                                ->first() !== null) {
            return True;
        }
        return False;
    }

    /**
     * isFacebookConnected
     * --------------------------------------------------
     * Returns true if the user has connected a facebook account
     * @return (boolean) ($status)
     * --------------------------------------------------
     */
    public function isFacebookConnected() {
        if ($this->connections()->where('service', 'facebook')
                                ->first() !== null) {
            return True;
        }
        return False;
    }

    /**
     * isGoogleAnalitycsConnected
     * --------------------------------------------------
     * Returns true if the user has connected a google_analitycs account
     * @return (boolean) ($status)
     * --------------------------------------------------
     */
    public function isGoogleAnalitycsConnected() {
        if ($this->connections()->where('service', 'google_analitycs')
                                ->first() !== null) {
            return True;
        }
        return False;
    }

    /**
     * isTwitterConnected
     * --------------------------------------------------
     * Returns true if the user has connected a twitter account
     * @return (boolean) ($status)
     * --------------------------------------------------
     */
    public function isTwitterConnected() {
        if ($this->connections()->where('service', 'twitter')
                                ->first() !== null) {
            return True;
        }
        return False;
    }
}
