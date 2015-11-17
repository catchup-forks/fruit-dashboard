<?php

class EmailNotification extends Notification
{
    protected $table = 'notifications';
    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * fire
     * --------------------------------------------------
     * @return Fires the notification event
     * --------------------------------------------------
     */
    public function fire() {
        /* Build Widgets data */
        $widgetsData = $this->buildWidgetsData();

        /* Send Customer IO event */
        $tracker = new CustomerIOTracker();
        $eventData = array(
            'en' => '<TRIGGER>SendSummaryEmail',
            'md' => array(
                'frequency' => $this->frequency,
                'data'      => $widgetsData,
            ),
        );
        $tracker->sendEvent($eventData);

        /* Return */
        return true;
    }

    /**
     * save
     * --------------------------------------------------
     * @return Saves the Notification object
     * --------------------------------------------------
     */
    public function save(array $options=array()) {
        /* Set type to email */
        $this->type = 'email';
        /* Call parent save */
        parent::save($options);
    }

    /**
     * ================================================== *
     *                   PRIVATE SECTION                  *
     * ================================================== *
     */

    /**
     * buildWidgetsData
     * --------------------------------------------------
     * @return Builds the widgets data for the email notification
     * --------------------------------------------------
     */
    private function buildWidgetsData() {
        $finalData = array();

        /* Iterate through dashboards */
        Log::info($this);

        foreach ($this->user->dashboards as $dashboard) {
            $dashboardData = array(
                'name' => $dashboard->name,
                'widgets' => array()
            );

            /* Iterate through widgets */
            foreach ($dashboard->widgets as $widget) {
                /* Skip not enabled widgets */
                if (!$widget->canSendInNotification()) {
                    continue;
                }

                /* Skip not selected widgets */
                if (!in_array($widget->id, $this->getSelectedWidgets())) {
                    continue;
                }

                /* Build widget data */
                if ($widget instanceof HistogramWidget) {
                    $widgetData = array(
                        'name'   => $widget->getSettings()['name'],
                        'latest' => array_values($widget->getLatestValues())[0],
                    );
                } elseif ($widget instanceof CountWidget) {
                    $widgetData = array(
                        'name'   => $widget->getDescriptor()->name,
                        'latest' => $widget->getData()['latest']['value'],
                    );
                } else {
                    $widgetData = array(
                        //'url'   => 'http://code.openark.org/forge/wp-content/uploads/2010/02/mycheckpoint-dml-chart-hourly-88-b.png',
                        'name'   => '',
                        'latest' => '',
                    );
                }


                /* Append widget data to dashboard data */
                array_push($dashboardData['widgets'], $widgetData);
            }

            /* Append dashboard data to final data if there are enabled widgets */
            if (count($dashboardData['widgets'])) {
                array_push($finalData, $dashboardData);
            }
        }

        /* Return in JSON */
        return $finalData;
    }
}
