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
        'send_month'
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
        Log::info('Email notification triggered');

        /* Build Widgets data */
        $widgetsData = $this->buildWidgetsDataForEmail();

        /* Send Customer IO event */
        $tracker = new CustomerIOTracker();
        $eventData = array(
            'en' => '<TRIGGER>SendEmailNotification2',
            'md' => array(
                'widgets-data' => $widgetsData,
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
     * @return Sends a notification in email.
     * --------------------------------------------------
     */
    private function buildWidgetsDataForEmail() {
        /* Build Widgets data */
        $widgetsData = array(
            'dashboard-1' => array(
                'name' => 'Marketing dashboard',
                'widgets' => array(
                    'widget-19' => array(
                        'url'   => 'http://code.openark.org/forge/wp-content/uploads/2010/02/mycheckpoint-dml-chart-hourly-88-b.png',
                        'name'  => 'Fruid Dashboard Facebook Likes',
                        'value' => 648,
                        'diff'  => "-9%",
                        'diff_unit' => '1 month'
                    ),
                    'widget-27' => array(
                        'url'   => 'http://code.openark.org/forge/wp-content/uploads/2010/02/mycheckpoint-dml-chart-sample-88-b.png',
                        'name'  => 'Fruid Dashboard Twitter Followers',
                        'value' => 199,
                        'diff'  => "+29%",
                        'diff_unit' => '1 day'
                    ),
                )
            ),
            'dashboard-2' => array(
                'name' => 'Custom webhooks',
                'widgets' => array(
                    'widget-1' => array(
                        'url'   => 'http://howto.wired.com/mediawiki/images/Chart4.png',
                        'name'  => 'Custom webhook - FD users',
                        'value' => 122,
                        'diff'  => "+1%",
                        'diff_unit' => '1 day'
                    ),
                )
            )
        );

        /* Return in JSON */
        return $widgetsData;
    }

}
