<?php

class Notification extends Eloquent
{
    private static $slackConsts = array(
        'text'        => "Here are some info about your startup that will make you happy.",
        'username'    => "FruitDashboard",
        'icon_emoji' => ':apple:',
    );

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
     * @return boolean
     * --------------------------------------------------
     */
    private function sendSlackNotification() {
        /* Initializing cURL */
        $ch = curl_init($this->address);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        /* Populating POST data */
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(
            $this->buildWidgetsDataForSlack()
        ));

        /* Sending request. */
        $success = curl_exec($ch);

        /* Cleanup and return. */
        curl_close($ch);
        return $success;

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

    /**
     * buildWidgetsDataForSlack
     * --------------------------------------------------
     * @return Builds the widgets data for the slack notification
     * --------------------------------------------------
     */
    private function buildWidgetsDataForSlack() {
        $attachments = array();
        /* Iterating throurh the widgets. */
        foreach ($this->getSelectedWidgets() as $i=>$widgetId) {
            $generalWidget = Widget::find($widgetId);
            if (is_null($generalWidget)) {
                /* Widget not found */
                continue;
            }

            /* Preparing data. */
            $widget = $generalWidget->getSpecific();
            $widgetData = array(
                'color' => SiteConstants::getSlackColor($i)
            );

            if ($widget instanceof HistogramWidget) {
                $widgetData = array_merge(
                    $widgetData,
                    $this->buildHistogramWidgetDataForSlack($widget)
                );
            } else {
                /* Right now we support only histogram widgets. */
                continue;
            }

            /* Appending data as an attachment. */
            array_push($attachments, $widgetData);
        }

        /* Merging attachments with constants. */
        return array_merge(
            self::$slackConsts,
            array('attachments' => $attachments)
        );
    }

    /**
     * getSelectedWidgets
     * Returns an array of the selected widgets.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    private function getSelectedWidgets() {
        if (is_array($this->selected_widgets)) {
            return $this->selected_widgets;
        } else if (is_string($this->selected_widgets)) {
            $decoded = json_decode($this->selected_widgets);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
        return array();
    }

    /**
     * buildHistogramWidgetDataForSlack
     * --------------------------------------------------
     * @param Widget $widget
     * @return Builds the widget data for histogram widgets.
     * --------------------------------------------------
     */
    private function buildHistogramWidgetDataForSlack($widget) {
        return array(
            'title'     => $widget->getSettings()['name'],
            'text'      => 'Your latest values: ' . Utilities::formatNumber(array_values($widget->getLatestValues())[0], $widget->getFormat())
            //'image_url' => 'http://orig14.deviantart.net/1fb5/f/2012/154/2/e/random_candy_bg_twitter__by_sleazyicons-d526c6j.gif'
        );
    }
}
