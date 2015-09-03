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
     * All created widgets.
     *
     * @var array
     */
    protected $widgets = array();

    /**
     * All created dataManagers.
     *
     * @var array
     */
    protected $dataManagers = array();

    /**
     * The dashboard.
     *
     * @var Dashboard
     */
    protected $dashboard = null;

    abstract protected function setup($args);
    abstract protected function populateData();

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
        $this->createManagers();
        $this->populateData();
        $this->saveData();
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
            if (array_key_exists($descriptor->type, static::$positioning)) {
                /* Creating widget instance. */
                $className = $descriptor->getClassName();
                $widget = new $className(array(
                    'position' => static::$positioning[$descriptor->type],
                    'state'    => 'loading'
                ));
                $widget->dashboard()->associate($this->dashboard);
                $widget->save();

                $this->widgets[$descriptor->type] = $widget;
            }
        }
    }

    /**
     * Creating the widgets and adding them to the dashboard.
     */
    protected function createManagers() {
        foreach(WidgetDescriptor::where('category', static::$service)->get() as $descriptor) {
            /* Creating widget instance. */
            $className = str_replace('Widget', 'DataManager', $descriptor->getClassName());

            /* No manager found */
            if ( ! class_exists($className)) {
                continue;
            }

            /* Creating data */
            $data = new Data(array('raw_value' => json_encode(array())));
            $data->save();

            /* Creating DataManager instance */
            $dataManager = new $className;
            $dataManager->descriptor()->associate($descriptor);
            $dataManager->user()->associate($this->user);

            /* Assigning data */
            $dataManager->data()->associate($data);
            $dataManager->save();

            /* Getting widget */
            if (array_key_exists($descriptor->type, $this->widgets)) {
                $widget = $this->widgets[$descriptor->type];
                $widget->data()->associate($data);
                $widget->save();
            }

            $this->dataManagers[$descriptor->type] = $dataManager;
        }
    }

    /**
     * Activating the widgets, that are shown on the dashboard.
     */
    protected function activateWidgets() {
        foreach ($this->widgets as $widget) {
            $widget->state = 'active';
            $widget->save();
        }
    }

    /**
     * Saving the data of all widgets, and dataManagers.
     */
    protected function saveData() {
        foreach ($this->dataManagers as $manager) {
            $manager->data->save();
        }
    }
}