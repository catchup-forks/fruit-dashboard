<?php
abstract class GeneralAutoDashboardCreator {
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
     * The dashboard.
     *
     * @var Dashboard
     */
    protected $dashboard = null;

    /**
     * The custom widget settings.
     *
     * @var array
     */
    protected $widgetSettings = null;

    function __construct($user, $widgetSettings=array()) {
        /* Getting the user */
        $this->user = $user;
        $this->widgetSettings = $widgetSettings;
    }

    public function create() {
        $this->createDashboard();
        $this->createWidgets();
    }

    /**
     * Creating a dashboard.
     */
    protected function createDashboard() {
        /* Creating dashboard. */
        $this->dashboard = new Dashboard(array(
            'name'       => ucwords(str_replace('_', ' ', static::$service)) . ' dashboard',
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

                /* Saving widget settings. */
                $widget->saveSettings($this->widgetSettings);

                /* Checking if the data is already available. */
                if (json_decode($widget->data->raw_value) != FALSE) {
                    $widget->state = 'active';
                    $widget->save();
                }
            }
        }
    }
}