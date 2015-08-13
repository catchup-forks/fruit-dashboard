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
     * isServiceConnected
     * --------------------------------------------------
     * Checking if the user is connected to the specific service.
     * @param string $service
     * @return boolean
     * --------------------------------------------------
     */
    public function isServiceConnected($service) {
        if ($this->connections()->where('service', $service)
                                ->first() !== null) {
            return True;
        }
        return False;
    }

}
