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
        $this->update_cache = TRUE;
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
                    'background' => TRUE,
                    'number'     => $this->dashboards->count() + 1,
                    'is_locked'  => FALSE,
                    'is_default' => FALSE
                ));
                $sharingDashboard->user()->associate($this);
                $sharingDashboard->save();
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
     * createDashboardView
     * --------------------------------------------------
     * Creating a dashboard view.
     * @param array $params
     * @return View
     * --------------------------------------------------
     */
    public function createDashboardView(array $params=array()) {
        if(array_key_exists('activeDashboard', $params)) {
            $existsActiveDashboard = false;
        }
        $dashboards = array();
        $i = 0;
        foreach ($this->dashboards as $dashboard) {
            /* Creating dashboard array. */
            $dashboards[$dashboard->id] = array(
                'name'       => $dashboard->name,
                'is_locked'  => $dashboard->is_locked,
                'is_default' => $dashboard->is_default,
                'widgets'    => array(),
                'count'      => $i++
            );

            /* Check activeDashboard exists */
            if(isset($existsActiveDashboard) &&
                    $params['activeDashboard'] == $dashboard->id) {
                $existsActiveDashboard = true;
            }
        }

        /* Populating widget data. */
        foreach ($this->widgets()->with('dashboard')->get() as $widget) {
            /* Getting template data for the widget. */
            if ($widget->renderable()) {
                /* Widget is loading, no data is available yet. */
                try {
                    $templateData = $widget->getTemplateData();
                } catch (Exception $e) {
                    /* Something went wrong during data population. */
                    Log::error($e->getMessage());
                    $widget->setState('rendering_error');
                    /* Falling back to default template data. */
                    $templateData = Widget::getDefaultTemplateData($widget);
                }
            } else {
                $templateData = Widget::getDefaultTemplateData($widget);
            }
            /* Adding widget to the dashboard array. */
            if (array_key_exists($widget->dashboard_id, $dashboards)) {
                array_push($dashboards[$widget->dashboard_id]['widgets'], array(
                    'meta'         => $widget->getTemplateMeta(),
                    'templateData' => $templateData
                ));
            }
        }

        /* Set default dashboard if activeDashboard not exists */
        if(isset($existsActiveDashboard) && !$existsActiveDashboard) {
            unset($params['activeDashboard']);
        }

        return View::make('dashboard.dashboard', $params)
            ->with('dashboards', $dashboards);
    }

    /**
     * checkDataIntegrity
     * --------------------------------------------------
     * Checking the overall integrity of the user's data.
     * @return boolean
     * --------------------------------------------------
     */
    public function checkDataIntegrity() {
        foreach ($this->dataObjects as $data) {
            $data->checkIntegrity();
        }
    }

    /**
     * checkWidgetsIntegrity
     * --------------------------------------------------
     * Checking the overall integrity of the user's widgets.
     * @return boolean
     * --------------------------------------------------
     */
    public function checkWidgetsIntegrity() {
        foreach ($this->widgets()->with('data')->get() as $widget) {
            try {
                $widget->checkIntegrity();
            } catch (WidgetFatalException $e) {
                /* Cannot recover widget. */
                $widget->setState('setup_required');
            } catch (WidgetException $e) {
                /* A simple save might help. */
                $widget->save();
                try {
                    $widget->checkIntegrity();
                } catch (WidgetException $e) {
                    /* Did not help. */
                    $widget->setState('setup_required');
                }
            }
        }
    }

    /**
     * turnOffBrokenWidgets
     * --------------------------------------------------
     * Setting all broken widget's state.
     * @return boolean
     * --------------------------------------------------
     */
    public function turnOffBrokenWidgets() {
        foreach ($this->widgets()->with('data')->get() as $widget) {
            if ($widget instanceof SharedWidget) {
                continue;
            }
            if ($widget->renderable()) {
                $templateData = $widget->getTemplateData();
            } else {
                $templateData = Widget::getDefaultTemplateData($widget);
            }
            $view = View::make($widget->getDescriptor()->getTemplateName())
                ->with('widget', $templateData);
            try {
                $view->render();
            } catch (Exception $e) {
                Log::error($e->getMessage());
                $widget->setState('rendering_error');
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
        /* Create default settings for the user */
        $settings = new Settings(array(
            'api_key' => md5(str_random(32)),
            'onboarding_state' => SiteConstants::getSignupWizardStep('first'),
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
        if ($_ENV['SUBSCRIPTION_MODE'] == 'premium_feature_and_trial') {
            $trialStatus = 'possible';
        } elseif ($_ENV['SUBSCRIPTION_MODE'] == 'premium_feature_only') {
            $trialStatus = 'possible';
        } elseif ($_ENV['SUBSCRIPTION_MODE'] == 'trial_only') {
            $trialStatus = 'active';
            /* Track event | TRIAL STARTS */
            $tracker = new GlobalTracker();
            $tracker->trackAll('lazy', array(
                'en' => 'Trial starts',
                'el' => $this->email)
            );
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
    }

    /**
     * createDefaultDashboards
     * Creating the default dashboards for the user.
     */
    private function createDefaultDashboards() {
        /* Make ARRRR dashboards */
        foreach (SiteConstants::getAutoDashboards() as $name=>$widgets) {
            $dashboard = new Dashboard(array(
                'name'       => $name . ' dashboard',
                'background' => TRUE,
                'number'     => $this->dashboards->max('number') + 1
            ));
            $dashboard->user()->associate($this);
            $dashboard->save();
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

        /* Make personal dashboard */
        $this->makePersonalAutoDashboard('auto', null);
    }

    /**
     * makePersonalAutoDashboard
     * creates a new Dashboard object and personal widgets
     * optionally from the POST data
     * --------------------------------------------------
     * @param (string)  ($mode) 'auto' or 'manual'
     * @param (array)   ($widgetdata) Personal widgets data
     * @return (Dashboard) ($dashboard) The new Dashboard object
     * --------------------------------------------------
     */
    private function makePersonalAutoDashboard($mode, $widgetdata) {
        /* Create new dashboard */
        $dashboard = new Dashboard(array(
            'name'       => 'Personal dashboard',
            'background' => 'On',
            'number'     => $this->dashboards->max('number') + 1,
            'is_default' => FALSE
        ));
        $dashboard->user()->associate($this);
        $dashboard->save();

        /* Create clock widget */
        if (($mode == 'auto') or
            array_key_exists('widget-clock', $widgetdata)) {
            $clockwidget = new ClockWidget(array(
                'state'    => 'active',
                'position' => '{"row":1,"col":3,"size_x":8,"size_y":3}',
            ));
            $clockwidget->dashboard()->associate($dashboard);
            $clockwidget->save();
        }

        /* Create greetings widget */
        if (($mode == 'auto') or
            array_key_exists('widget-greetings', $widgetdata)) {
            $greetingswidget = new GreetingsWidget(array(
                'state'    => 'active',
                'position' => '{"row":4,"col":3,"size_x":8,"size_y":1}',
            ));
            $greetingswidget->dashboard()->associate($dashboard);
            $greetingswidget->save();
        }

        /* Create quote widget */
        if (($mode == 'auto') or
            array_key_exists('widget-quote', $widgetdata)) {
            $quotewidget = new QuoteWidget(array(
                'state'    => 'active',
                'position' => '{"row":10,"col":1,"size_x":12,"size_y":2}',
            ));
            $quotewidget->dashboard()->associate($dashboard);
            $quotewidget->saveSettings(array('type' => 'inspirational'));
        }

        /* Return */
        return $dashboard;
    }

}
