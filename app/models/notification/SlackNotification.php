<?php

class SlackNotification extends Notification
{
    protected $table = 'notifications';

    private static $slackConsts = array(
        'text'        => "Here are some info about your startup that will make you happy.",
        'username'    => "Fruit Dashboard",
        'icon_emoji' => ':tangerine:',
    );

    private static $welcomeMessage = array(
        'text'       => "Congratulations! You've just connected Fruit Dashboard with Slack. We're very happy to greet you on board.",
        'username'   => "Fruit Dashboard",
        'icon_emoji' => ':peach:',
    );

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * sendWelcome
     * --------------------------------------------------
     * @return Sends the welcome message to the connected slack channel
     * --------------------------------------------------
     */
    public function sendWelcome() {
        /* Initializing cURL */
        $ch = curl_init($this->address);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        /* Populating POST data */
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(
            self::$welcomeMessage
        ));

        /* Sending request. */
        $success = curl_exec($ch);

        /* Cleanup and return. */
        curl_close($ch);
        return $success;
    }

    /**
     * fire
     * --------------------------------------------------
     * @return Fires the notification event
     * --------------------------------------------------
     */
    public function fire() {
        /* Initializing cURL */
        $ch = curl_init($this->address);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        /* Populating POST data */
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(
            $this->buildWidgetsData()
        ));

        /* Sending request. */
        $success = curl_exec($ch);

        /* Cleanup and return. */
        curl_close($ch);
        return $success;
    }

    /**
     * save
     * --------------------------------------------------
     * @return Saves the Notification object
     * --------------------------------------------------
     */
    public function save(array $options=array()) {
        /* Set type to email */
        $this->type = 'slack';
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
     * @return Builds the widgets data for the slack notification
     * --------------------------------------------------
     */
    private function buildWidgetsData() {
        $attachments = array();
        /* Iterating throurh the widgets. */
        foreach ($this->getSelectedWidgets() as $i=>$widgetId) {
            $widget = Widget::find($widgetId);
            if (is_null($widget)) {
                /* Widget not found */
                continue;
            }

            /* Preparing data. */
            $widgetData = array(
                'color' => SiteConstants::getSlackColor($i)
            );

            if ($widget instanceof HistogramWidget) {
                $widgetData = array_merge(
                    $widgetData,
                    $this->buildHistogramWidgetData($widget)
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
     * buildHistogramWidgetData
     * --------------------------------------------------
     * @param Widget $widget
     * @return Builds the widget data for histogram widgets.
     * --------------------------------------------------
     */
    private function buildHistogramWidgetData($widget) {
        return array(
            'title'     => $widget->getSettings()['name'],
            'text'      => 'Your latest values: ' . Utilities::formatNumber(array_values($widget->getLatestValues())[0], $widget->getFormat())
            //'image_url' => 'http://orig14.deviantart.net/1fb5/f/2012/154/2/e/random_candy_bg_twitter__by_sleazyicons-d526c6j.gif'
        );
    }
}
