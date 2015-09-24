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
        'last_activity',
        'api_key',
    );

    /* -- Relations -- */
    public function connections() { return $this->hasMany('Connection'); }
    public function subscription() { return $this->hasOne('Subscription'); }
    public function dashboards() { return $this->hasMany('Dashboard'); }
    public function settings() { return $this->hasOne('Settings'); }
    public function notifications() { return $this->hasMany('Notification'); }
    public function background() { return $this->hasOne('Background'); }
    public function dataManagers() { return $this->hasmany('DataManager'); }
    public function widgetSharings() { return $this->hasmany('WidgetSharing'); }

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
            return $this->dashboards()->where('is_default', TRUE)->first();

        /* Default dashboard doesn't exist */
        } else {
            /* Dashboard exists, but none of them is default */
            if ($this->dashboards()->count()) {
                /* Make the first default */
                $dashboard = $this->dashboards()->first();
                $dashboard->is_default = TRUE;
                $dashboard->save();

                /* Return */
                return $dashboard;

            /* No dashboard object exists */
            } else {
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

    /**
     * createDefaultProfile
     * Creating a default profile for the user including
     * settings, background, subscription.
     */
    public function createDefaultProfile() {
        /* Create extra attributes to user */
        $this->api_key = md5(str_random(32));
        $this->save();

        /* Create default settings for the user */
        $settings = new Settings;
        $settings->user()->associate($this);
        $settings->newsletter_frequency = 0;

        /* Save settings */
        $settings->save();

        /* Create default background for the user */
        $background = new Background;
        $background->user()->associate($this);
        $background->changeUrl();

        /* Save background */
        $background->save();

        /* Create default subscription for the user */
        $plan = Plan::getFreePlan();
        $subscription = new Subscription;
        $subscription->user()->associate($this);
        $subscription->plan()->associate($plan);
        $subscription->status = 'active';
        $subscription->trial_status = 'possible';
        $subscription->trial_start  = null;

        /* Save subscription */
        $subscription->save();
    }

}
