<?php
abstract class GeneralAutoDashboardCreator {

    /**
     * The positioning of the widgets visible on the dashboard.
     *
     * @var array
     */
    protected static $widgets = array();

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
    protected $globalWidgetSettings = null;

    function __construct($user, $widgetSettings=array()) {
        /* Getting the user */
        $this->user = $user;
        $this->globalWidgetSettings = $widgetSettings;
    }

    public function create($dashboard_name=null) {

        $this->createDashboard(is_null($dashboard_name) ? Utilities::underscoreToCamelCase(static::$service, FALSE) : $dashboard_name);
        $this->createWidgets();
    }

    /**
     * Creating a dashboard.
     */
    protected function createDashboard($dashboard_name) {
        $this->dashboard = new Dashboard(array(
            'name'       => $dashboard_name . ' dashboard',
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
        foreach(WidgetDescriptor::where('category', static::$service)->orderBy('number', 'asc')->get() as $descriptor) {
            if (array_key_exists($descriptor->type, static::$widgets)) {
                $widgetType = $descriptor->type;
                $widgetMeta = static::$widgets[$widgetType];
                Log::info($widgetMeta);

                /* Creating widget instance. */
                $className = $descriptor->getClassName();
                $widget = new $className(array('state' => 'loading'));
                $widget->position = $this->getWidgetPosition(
                    $descriptor,
                    $this->dashboard,
                    $widgetMeta
                );

                /* Saving widget settings. */
                $widget->dashboard()->associate($this->dashboard);
                $settings = $this->globalWidgetSettings;
                if (array_key_exists('settings', $widgetMeta)) {
                    $settings = array_merge($settings, $widgetMeta['settings']);
                }
                $widget->saveSettings($settings);

                /* Checking if the data is already available. */
                if ( ! is_null($widget->data) && $widget->data->raw_value != 'loading') {
                    $widget->setState('active');
                }
            }
        }
    }
    /**
     * getWidgetPosition
     * Getting the widget position.
     * --------------------------------------------------
     * @param WidgetDescriptor $descriptor
     * @param Dashboard $dashboard
     * @param array $widgetMeta
     * @returns Position
     * --------------------------------------------------
     */
    protected function getWidgetPosition($descriptor, $dashboard, $widgetMeta) {
        if (array_key_exists('position', $widgetMeta)) {
            return $widgetMeta['position'];
        }
        /* Position not provided, calculating for ourselves. */
        return $dashboard->getNextAvailablePosition($descriptor->default_cols, $descriptor->default_rows);
    }
}