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
     * getDaysRemainingFromTrial
     * --------------------------------------------------
     * Returns the remaining time from the trial period in days
     * @return (integer) ($daysRemainingFromTrial) The number of days
     * --------------------------------------------------
     */
    public function getDaysRemainingFromTrial() {
        /* Get the difference */
        $diff = Carbon::now()->diffInDays($this->created_at);

        /* Check if trial period is still available for the user */
        if ($diff <= SiteConstants::getTrialPeriodInDays() ) {
            return SiteConstants::getTrialPeriodInDays()-$diff;
        } else {
            return 0;
        }
    }

     /**
     * getTrialEndDate
     * --------------------------------------------------
     * Returns the trial period ending date
     * @return (date) ($trialEndDate) The ending date
     * --------------------------------------------------
     */
    public function getTrialEndDate() {
        /* Return the date */
        return Carbon::instance($this->created_at)->addDays(SiteConstants::getTrialPeriodInDays());
    }

}
