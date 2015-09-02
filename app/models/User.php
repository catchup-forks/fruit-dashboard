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
        'date_of_birth',
        'created_at',
        'updated_at',
        'last_activity'
    );

    /* -- Relations -- */
    public function connections() { return $this->hasMany('Connection'); }
    public function subscription() { return $this->hasOne('Subscription'); }
    public function dashboards() { return $this->hasMany('Dashboard'); }
    public function settings() { return $this->hasOne('Settings'); }
    public function background() { return $this->hasOne('Background'); }

    /* -- Libraries -- */
    public function stripePlans() { return $this->hasMany('StripePlan', 'user_id'); }
    public function braintreePlans() { return $this->hasMany('BraintreePlan'); }
    public function facebookPages() { return $this->hasMany('FacebookPage'); }

    /* -- Custom relations. -- */
    public function widgets() { return $this->hasManyThrough('Widget', 'Dashboard'); }

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
