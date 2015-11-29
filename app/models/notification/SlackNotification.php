<?php

class SlackNotification extends Notification
{
    protected $table = 'notifications';

    private static $welcomeMessage = array(
        'text'       => "Congratulations! You've just connected Fruit Dashboard with Slack. We're very happy to welcome you on board.",
        'username'   => "Fruit Dashboard",
        'icon_emoji' => ':peach:',
    );

    private static $reconnectMessage = array(
        'text'       => "Hi! You've modified your Slack integration settings on Fruit Dashboard. You see this message, because we're making sure, that everything is still working.",
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
        if ($this->is_enabled) {
            $message = self::$reconnectMessage;
        } else {
            $message = self::$welcomeMessage;
            
            /* Track event | SLACK INTEGRATION CONNECTED */
            $tracker = new GlobalTracker();
            $tracker->trackAll('lazy', array(
                'en' => 'Slack integration connected',
                'el' => $this->address)
            );
        }
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
        $message = $this->buildDailyData();
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
            /* Widget not found, silent skip */
            if (is_null($widget)) {
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
        /* FIXME: This is only to set active histogram */
        $widgetData = $widget->getTemplateData();
        return array(
            'title'     => $widget->getTemplateMeta()['general']['name'],
            'text'      => 
                Utilities::formatNumber($widget->getLatestValues()['value'], $widget->getFormat())
                //"Today: "        . Utilities::formatNumber($widget->getHistory(1, 'days')['value'], $widget->getFormat())
                //. "\nThis week: "  . Utilities::formatNumber($widget->getHistory(1, 'weeks')['value'], $widget->getFormat()) .
                //. "\nThis month: " . Utilities::formatNumber($widget->getHistory(1, 'months')['value'], $widget->getFormat()) .
                //. "\nThis year: "  . Utilities::formatNumber($widget->getHistory(1, 'years')['value'], $widget->getFormat()) .
                //. "\nAll time:"    . Utilities::formatNumber($widget->getLatestValues(), $widget->getFormat())
        );
    }
}
