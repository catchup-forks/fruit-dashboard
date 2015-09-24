<?php

class Notification extends Eloquent
{
    /* -- Fields -- */
    protected $guarded = array(
    );

    protected $fillable = array(
        'type',
        'frequency',
        'address',
        'send_minute',
        'send_time',
        'send_weekday',
        'send_day',
        'send_month',
        'selected_widgets'
    );

    /* -- No timestamps -- */
    public $timestamps = false; 

    /* -- Relations -- */
    public function user() { return $this->belongsTo('User'); }


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
        /* Send notification based on its type */
        switch ($this->type) {
            case 'slack':
                $this->sendSlackNotification();
                break;
            case 'email':
            default:
                $this->sendEmailNotification();
                break;
        }
    }

    /**
     * ================================================== *
     *                   PRIVATE SECTION                  *
     * ================================================== *
     */
    /**
     * sendEmailNotification
     * --------------------------------------------------
     * @return Sends a notification in email.
     * --------------------------------------------------
     */
    private function sendEmailNotification() {
        /* Build Widgets data */
        $widgetsData = $this->buildWidgetsDataForEmail();

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
        return TRUE;
    }

    /**
     * sendSlackNotification
     * --------------------------------------------------
     * @return Sends a notification to slack.
     * --------------------------------------------------
     */
    private function sendSlackNotification() {
        /* Return */
        return TRUE;
    }

    /**
     * buildWidgetsDataForEmail
     * --------------------------------------------------
     * @return Builds the widgets data for the eamil notification
     * --------------------------------------------------
     */
    private function buildWidgetsDataForEmail() {
        $finalData = array();

        /* Iterate through dashboards */
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
                if (!in_array($widget->id, json_decode($this->selected_widgets))) {
                    continue;
                }

                $widgetData = array(
                    'url'   => 'http://code.openark.org/forge/wp-content/uploads/2010/02/mycheckpoint-dml-chart-hourly-88-b.png',
                    'name'  => $widget->getSettings()['name'],
                );

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
