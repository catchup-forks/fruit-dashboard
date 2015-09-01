<?php

abstract class GeneralAutoDashboardCreator
{
    /* -- Class properties -- */

    /**
     * The positioning of the widgets visible on the dashboard.
     *
     * @var array
     */
    protected static $positioning = array();

    /**
     * The service we're using.
     *
     * @var string
     */
    protected static $service = '';

    /**
     * The user object.
     *
     * @var User
     */
    protected $user = null;

    /**
     * All calculated widgets.
     *
     * @var array
     */
    protected $widgets = array();

    /**
     * The dashboard.
     *
     * @var Dashboard
     */
    protected $dashboard = null;

    abstract protected function setup($args);
    abstract protected function populateDashboard();

    /**
     * Main function of the job.
     *
     * @param $job
     * @param array $args
    */
    public function fire($job, $args) {
        /* Getting the user */
        $this->user = $this->getUser($args['user_id']);
        $this->setup($args);
        $this->run();
    }

    /**
     * Getting the user and setting trial.
     *
     * @param $job
     * @param array $args
    */
    protected function getUser($userId) {
        /* Getting the user */
        $user = User::find($userId);

        /* Change trial period settings */
        $user->subscription->changeTrialState('active');

        return $user;
    }

    /**
     * Running dashboard creation.
    */
    protected function run() {
        $this->createDashboard();
        $this->createWidgets();
        $this->populateDashboard();
        $this->activateWidgets();
    }

    /**
     * Creating a dashboard.
     */
    protected function createDashboard() {
        /* Creating dashboard. */
        $this->dashboard = new Dashboard(array(
            'name'       => ucwords(static::$service) . ' dashboard',
            'background' => TRUE,
            'number'     => $this->user->dashboards->max('number') + 1
        ));
        $this->dashboard->user()->associate($this->user);
        $this->dashboard->save();
    }

    /**
     * Creating the widgets and adding them to the dashboard.
     */
    protected function createWidgets() {
        foreach(WidgetDescriptor::where('category', static::$service)->get() as $descriptor) {
            /* Creating widget instance. */
            $className = $descriptor->getClassName();
            $widget = new $className;
            $widget->dashboard()->associate($this->dashboard);

            /* Looking for positioning. */
            if (array_key_exists($descriptor->type, static::$positioning)) {
                $widget->position = static::$positioning[$descriptor->type];
                $widget->state = 'loading';
            } else {
                $widget->position = '';
                $widget->state = 'hidden';
            }

            /* Saving instance */
            $widget->save();
            $this->widgets[$descriptor->type] = $widget;
        }
    }

    /**
     * Activating the widgets, that are shown on the dashboard.
     */
    protected function activateWidgets() {
        foreach ($this->widgets as $widget) {
            if (array_key_exists($widget->descriptor->type, static::$positioning)) {
                $widget->state = 'active';
                $widget->save();
            }
        }
    }

}