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
        'startup_type'
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
    public function twitterUsers() { return $this->hasMany('TwitterUser'); }
    public function googleAnalyticsProperties() { return $this->hasMany('GoogleAnalyticsProperty'); }
    public function googleAnalyticsProfiles() { return $this->hasManyThrough('GoogleAnalyticsProfile', 'GoogleAnalyticsProperty', 'user_id', 'property_id'); }

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
     * hasUnseenWidgetSharings
     * --------------------------------------------------
     * Returns whether or not the user has any pending
     * widget sharings.
     * @return boolean
     * --------------------------------------------------
     */
    public function hasUnseenWidgetSharings() {
        $sharings = $this->widgetSharings()->where('state', 'not_seen')->get();
        if (count($sharings) > 0) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * getPendingWidgetSharings
     * --------------------------------------------------
     * Returns whether or not the user has any pending
     * widget sharings.
     * @return boolean
     * --------------------------------------------------
     */
    public function getPendingWidgetSharings() {
        return $this->widgetSharings()->where('state', 'not_seen')->orWhere('state', 'seen')->get();
    }

    /**
     * createDashboardView
     * --------------------------------------------------
     * Creating a dashboard view.
     * @param array $params
     * @return View
     * --------------------------------------------------
     */
    public function createDashboardView(array $params=array()) {
        $dashboards = array();
        foreach ($this->dashboards as $dashboard) {
            /* Creating dashboard array. */
            $dashboards[$dashboard->id] = array(
                'name'      => $dashboard->name,
                'is_locked' => $dashboard->is_locked,
                'widgets' => array()
            );
            /* Iterating through the widgets. */
            foreach ($dashboard->widgets as $generalWidget) {
                $widget = $generalWidget->getSpecific();
                array_push($dashboards[$dashboard->id]['widgets'], array(
                    'specific' => $widget,
                    'meta'     => $widget->getTemplateMeta()
                ));

            }
        }
        return View::make('dashboard.dashboard', $params)
            ->with('dashboards', $dashboards);
    }

    /**
     * checkWidgetsIntegrity
     * --------------------------------------------------
     * Checking the overall integrity of the user's widgets.
     * @return boolean
     * --------------------------------------------------
     */
    public function checkWidgetsIntegrity() {
        foreach ($this->widgets as $generalWidget) {
            $generalWidget->getSpecific()->checkIntegrity();
        }
    }

    /**
     * checkDataManagersIntegrity
     * --------------------------------------------------
     * Checking the overall integrity of the user's data managers.
     * @return boolean
     * --------------------------------------------------
     */
    public function checkDataManagersIntegrity() {
        foreach ($this->dataManagers as $generalDataManager) {
            $generalDataManager->getSpecific()->checkIntegrity();
        }
    }

    /**
     * turnOffBrokenWidgets
     * --------------------------------------------------
     * Setting all broken widget's state to setup required.
     * @return boolean
     * --------------------------------------------------
     */
    public function turnOffBrokenWidgets() {
        foreach ($this->widgets as $generalWidget) {
            $widget = $generalWidget->getSpecific();
            if ($widget instanceof SharedWidget) {
                continue;
            }
            $view = View::make($widget->descriptor->getTemplateName())->with('widget', $widget);
            try {
                $view->render();
            } catch (Exception $e) {
                Log::error($e->getMessage());
                $widget->setState('setup_required');
            }
        }
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
        $settings = new Settings(array(
            'newsletter_frequency' => 0,
        ));
        $settings->user()->associate($this);
        $settings->save();

        /* Create default notifications for the user */
        $emailNotification = new Notification(array(
            'type' => 'email',
            'frequency' => 'daily',
            'address' => $this->email,
            'send_time' => Carbon::createFromTime(7, 0, 0, 'Europe/Budapest')
        ));
        $emailNotification->user()->associate($this);
        $emailNotification->save();

        $slackNotification = new Notification(array(
            'type' => 'slack',
            'frequency' => 'daily',
            'address' => null,
            'send_time' => Carbon::createFromTime(7, 0, 0, 'Europe/Budapest')
        ));
        $slackNotification->user()->associate($this);
        $slackNotification->save();

        /* Create default background for the user */
        $background = new Background;
        $background->user()->associate($this);
        $background->changeUrl();
        $background->save();

        /* Create default subscription for the user */
        $plan = Plan::getFreePlan();
        $subscription = new Subscription(array(
            'status'       => 'active',
            'trial_status' => 'possible',
            'trial_start'  => null,
        ));
        $subscription->user()->associate($this);
        $subscription->plan()->associate($plan);
        $subscription->save();
    }

}
