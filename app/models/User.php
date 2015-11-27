<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Auth\Reminders\RemindableTrait;

class User extends Eloquent implements UserInterface, RemindableInterface
{
    /* UserTrait implements the functions from UserInterface */
    use UserTrait;

    /* RemindableTrait implements the functions from RemindableInterface */
    use RemindableTrait;

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
        'update_cache',
    );

    /* -- Relations -- */
    public function connections() { return $this->hasMany('Connection'); }
    public function subscription() { return $this->hasOne('Subscription'); }
    public function dashboards() { return $this->hasMany('Dashboard'); }
    public function settings() { return $this->hasOne('Settings'); }
    public function notifications() { return $this->hasMany('Notification'); }
    public function background() { return $this->hasOne('Background'); }
    public function dataObjects() { return $this->hasmany('Data'); }
    public function widgetSharings() { return $this->hasmany('WidgetSharing'); }

    /* -- Libraries -- */
    public function stripePlans() { return $this->hasMany('StripePlan', 'user_id'); }
    public function braintreePlans() { return $this->hasMany('BraintreePlan'); }
    public function facebookPages() { return $this->hasMany('FacebookPage'); }
    public function twitterUsers() { return $this->hasMany('TwitterUser'); }
    public function googleAnalyticsProperties() { return $this->hasMany('GoogleAnalyticsProperty'); }

    /* -- Custom relations. -- */
    public function widgets() {
        return $this->hasManyThrough('Widget', 'Dashboard');
    }

    public function googleAnalyticsProfiles() {
        return $this->hasManyThrough(
            'GoogleAnalyticsProfile',
            'GoogleAnalyticsProperty',
            'user_id', 'property_id'
        );
    }

    /**
     * updateDashboardCache
     * Setting the update_cache property.
     * --------------------------------------------------
     * @params
     * --------------------------------------------------
     */
    public function updateDashboardCache() {
        $this->update_cache = true;
        $this->save();
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

    /**
     * hasUnseenWidgetSharings
     * --------------------------------------------------
     * Returns whether or not the user has any pending
     * widget sharings.
     * @return boolean
     * --------------------------------------------------
     */
    public function hasUnseenWidgetSharings() {
        $sharings = $this->widgetSharings()
            ->where('state', 'not_seen')
            ->orWhere('state', 'auto_created')
            ->get();
        if (count($sharings) > 0) {
            return true;
        }
        return false;
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
        return $this->widgetSharings()
            ->where('state', 'not_seen')
            ->orWhere('state', 'seen')
            ->get();
    }

    /**
     * handleWidgetSharings
     * --------------------------------------------------
     * Creating the dashboard if necessary, adding the
     * widget automatically
     * --------------------------------------------------
     */
    public function handleWidgetSharings() {
        /* Check if user has any widgetSharings. */
        if ($this->hasUnseenWidgetSharings()) {
            $sharingDashboard = $this->dashboards()
                ->where('name', SiteConstants::getSharedWidgetsDashboardName())
                ->first();
            if (is_null($sharingDashboard)) {
                /* Dashboard does not exists, creating it automatically. */
                $sharingDashboard = new Dashboard(array(
                    'name'       => SiteConstants::getSharedWidgetsDashboardName(),
                    'background' => true,
                    'number'     => $this->dashboards->count() + 1,
                    'is_locked'  => false,
                    'is_default' => false
                ));
                $sharingDashboard->user()->associate($this);
                $sharingDashboard->save();
                $this->updateDashboardCache();
            }

            /* Accepting all sharings. */
            foreach ($this->getPendingWidgetSharings() as $sharing) {
                $sharing->autoCreate($sharingDashboard->id);
            }
        }

    }

    /**
     * getWidgetSharings
     * --------------------------------------------------
     * Returns the shared widgets.
     * @return object
     * --------------------------------------------------
     */
    public function getWidgetSharings() {
        return $this->widgetSharings()->where('state', '!=', 'rejected')->get();
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
        if ($this->dashboards()->where('is_default', true)->count()) {
            /* Return */
            return $this->dashboards()->where('is_default', true)->first();

        /* Default dashboard doesn't exist */
        } else {
            /* Dashboard exists, but none of them is default */
            if ($this->dashboards()->count()) {
                /* Make the first default */
                $dashboard = $this->dashboards()->first();
                $dashboard->is_default = true;
                $dashboard->save();

                /* Return */
                return $dashboard;

            /* No dashboard object exists */
            } else {
                /* Create a new dashboard objec*/
                $dashboard = new Dashboard(array(
                    'name'       => 'Default dashboard',
                    'number'     => 1,
                    'background' => true,
                    'is_default' => true,
                    'is_locked'  => false
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
        /* Create default settings for the user */
        $settings = new Settings(array(
            'api_key' => md5(str_random(32)),
            'onboarding_state' => SiteConstants::getSignupWizardStep('first'),
        ));
        $settings->user()->associate($this);
        $settings->save();

        /* Set timeZone for notifications */
        if (Session::get('timeZone')) {
            $timeZone = Session::get('timeZone');
        } else {
            $timeZone = 'Europe/Budapest';
        }

        /* Create default notifications for the user */
        $emailNotification = new EmailNotification(array(
            'type' => 'email',
            'frequency' => 'daily',
            'address' => $this->email,
            'send_time' => Carbon::createFromTime(7, 0, 0, $timeZone)
        ));
        $emailNotification->user()->associate($this);
        $emailNotification->save();

        $slackNotification = new SlackNotification(array(
            'type' => 'slack',
            'frequency' => 'daily',
            'address' => null,
            'send_time' => Carbon::createFromTime(7, 0, 0, $timeZone)
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
        if ($_ENV['SUBSCRIPTION_MODE'] == 'premium_feature_and_trial') {
            $trialStatus = 'possible';
        } elseif ($_ENV['SUBSCRIPTION_MODE'] == 'premium_feature_only') {
            $trialStatus = 'possible';
        } elseif ($_ENV['SUBSCRIPTION_MODE'] == 'trial_only') {
            $trialStatus = 'active';
        }

        $subscription = new Subscription(array(
            'status'       => 'active',
            'trial_status' => $trialStatus,
            'trial_start'  => null,
        ));
        $subscription->user()->associate($this);
        $subscription->plan()->associate($plan);
        $subscription->save();

        /* Creating Dashboard. */
        $this->createDefaultDashboards();

        /* Enable all created widget in notifications by default */
        $emailNotification->addToSelectedWidgets($this->widgets);
        $slackNotification->addToSelectedWidgets($this->widgets);
    }

    /**
     * createDefaultDashboards
     * Creating the default dashboards for the user.
     */
    private function createDefaultDashboards() {
        /* Make personal dashboard */
        $this->makeBigPictureDashboard('auto', null);
    }

    /**
     * makeBigPictureDashboard
     * creates a new Dashboard object and personal widgets
     * optionally from the POST data
     * --------------------------------------------------
     * @param (string)  ($mode) 'auto' or 'manual'
     * @param (array)   ($widgetdata) Personal widgets data
     * --------------------------------------------------
     */
    private function makeBigPictureDashboard($mode, $widgetdata) {
        /* Create new dashboard */
        foreach (SiteConstants::getAutoDashboards() as $dashboardName=>$widgets) {
            $dashboard = new Dashboard(array(
                'name'       => $dashboardName,
                'background' => 'Off',
                'number'     => 1,
                'is_default' => true
            ));
            $dashboard->user()->associate($this);
            $dashboard->save();

            /* Adding promo widgets. */
            foreach ($widgets as $widgetMeta) {
                $descriptor = WidgetDescriptor::where('type', $widgetMeta['type'])->first();
                /* Creating widget instance. */
                $widget = new PromoWidget(array(
                    'position' => $widgetMeta['position'],
                    'state'    => 'active'
                ));
                $widget->dashboard()->associate($dashboard);

                /* Saving settings. */
                $settings = array_key_exists('settings', $widgetMeta) ? $widgetMeta['settings'] : array ();
                $widget->saveSettings(array(
                    'widget_settings'    => json_encode($settings),
                    'related_descriptor' => $descriptor->id,
                    'photo_location'     => $widgetMeta['pic_url']
                ));
            }
        }
    }
}
