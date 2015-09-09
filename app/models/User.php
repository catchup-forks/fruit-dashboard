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
    public function dataManagers() { return $this->hasmany('DataManager'); }

    /* -- Libraries -- */
    public function stripePlans() { return $this->hasMany('StripePlan', 'user_id'); }
    public function braintreePlans() { return $this->hasMany('BraintreePlan'); }
    public function facebookPages() { return $this->hasMany('FacebookPage'); }
    public function googleAnalyticsProperties() { return $this->hasMany('GoogleAnalyticsProperty'); }

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

    /**
     * checkOrCreateDefaultDashboard
     * --------------------------------------------------
     * Checks if the user has a default dashboard, and
     * creates / makes the first if not.
     * @return (Dashboard) $dashboard the default dashboard object
     * --------------------------------------------------
     */
    public function checkOrCreateDefaultDashboard() {
        /* Get the dashboard and return if exists */
        if ($this->dashboards()->where('is_default', TRUE)->count()) {
            /* Return */
            Log::info('1');
            Log::info($this->dashboards()->where('is_default', TRUE)->count());
            return $this->dashboards()->where('is_default', TRUE)->first();

        /* Default dashboard doesn't exist */
        } else {
            /* Dashboard exists, but none of them is default */
            if ($this->dashboards()->count()) {
                Log::info('2');
                Log::info($this->dashboards()->count());

                /* Make the first default */
                $dashboard = $this->dashboards()->first();
                $dashboard->is_default = TRUE;
                $dashboard->save();

                /* Return */
                return $dashboard;
            
            /* No dashboard object exists */
            } else {
                Log::info('3');
                /* Create a new dashboard objec*/
                $dashboard = new Dashboard(array(
                    'name'       => 'Default dashboard',
                    'number'     => 1,
                    'background' => TRUE,
                    'is_default' => TRUE,
                    'is_locked'  => FALSE
                ));
                $dashboard->user()->associate($this);
                $dashboard->save();

                /* Return */
                return $dashboard;
            }
        }
    }

}
