<?php

class SlackNotification extends Notification
{
    protected $table = 'notifications';

    private static $welcomeMessage = array(
        'text'       => "Congratulations! You've just connected Fruit Dashboard with Slack. We're very happy to welcome you on board.",
        'username'   => "Fruit Dashboard",
        'icon_emoji' => ':peach:',
    );

    private static $generalMessage = array(
        'text'        => "Here are your growth numbers for today.",
        'username'    => "Fruit Dashboard",
        'icon_emoji' => ':tangerine:',
    );

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * sendWelcome
     * --------------------------------------------------
     * Sends the welcome message to the connected slack channel
     * @return {boolean} result | The execution success
     * --------------------------------------------------
     */
    public function sendWelcome() {
        // Build message
        $message = self::$welcomeMessage;
        // Send the message
        $result = $this->sendMessage($message);
        // Return
        return $result;
    }

    /**
     * sendDailyMessage
     * --------------------------------------------------
     * Sends the daily message to the connected slack channel
     * @return {boolean} result | The execution success
     * --------------------------------------------------
     */
    public function sendDailyMessage() {
        // Build message
        $message = $this->buildDailyData;
        // Send the message
        $result = $this->sendMessage($message);
        // Return
        return $result;
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
     * sendMessage
     * --------------------------------------------------
     * Sends the welcome message to the connected slack channel
     * @param {array} message | The message array
     * @return {boolean} success | The curl execution success
     * --------------------------------------------------
     */
    private function sendMessage($message) {
        /* Initializing cURL */
        $ch = curl_init($this->address);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        /* Populating POST data */
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(
            $message
        ));

        /* Sending request. */
        $success = curl_exec($ch);

        /* Cleanup and return. */
        curl_close($ch);
        return $success;
    }

    /**
     * buildDailyData
     * --------------------------------------------------
     * Builds the data for the slack notification
     * @return {array} data | The built data
     * --------------------------------------------------
     */
    private function buildDailyData() {
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
            self::$generalMessage,
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
