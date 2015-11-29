<?php

/**
 * --------------------------------------------------------------------------
 * NotificationController: Handles the notifications
 * --------------------------------------------------------------------------
 */
class NotificationController extends BaseController
{
    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */
     
    /**
     * getConfigureSlack
     * --------------------------------------------------
     * @return Renders the Slack configuration page
     * --------------------------------------------------
     */
    public function getConfigureSlack() {
        /* Get the notification objects for the user */
        $slackNotification = Auth::user()->notifications()->where('type','slack')->first();

        /* Render view */
        return View::make('notification.configure-slack', ['notification' => $slackNotification]);
    }

    /**
     * postConfigureSlack
     * --------------------------------------------------
     * @return Renders the Slack configuration page
     * --------------------------------------------------
     */
    public function postConfigureSlack() {
        /* Initialize variables */
        $errors = array();

        /* Get the slack notification instance */
        $slackNotification = Auth::user()->notifications()->where('type','slack')->first();

        /* Get selected widgets */
        $selectedWidgets = array();
        foreach (Input::all() as $key => $value) {
            $number = str_replace('widget-', "", $key);
            if (is_numeric($number)) {
                array_push($selectedWidgets, $number);
            }
        }

        /* Save selected widgets */
        $slackNotification->selected_widgets = json_encode($selectedWidgets);
        $slackNotification->save();

        /* Get the address */
        $address = Input::get('address');
        /* Proceed only with provided address */
        if ($address) {
            /* Create Slack notification */
            $slackNotification->address = $address;
            $slackNotification->save();

            /* Try to send welcome message */
            if ($slackNotification->sendWelcome()) {
                /* Enable notification */
                $slackNotification->is_enabled = true;
                $slackNotification->save();
            } else {
                /* Disable notification */
                $slackNotification->is_enabled = false;
                $slackNotification->save();

                /* Add error */
                array_push($errors, 'We couldn\'t send any message to the provided url. Please check it, and try again');
            }
        } else {
            array_push($errors, 'You need to enter your valid url to the url field to connect with Slack.');
        }

        /* Render view */
        if (count($errors)) {
            return Redirect::route('notification.configureSlack', ['notification' => $slackNotification])
                        ->with(['error' => $errors[0]]);
        } else {
            return Redirect::route('notification.configureSlack', ['notification' => $slackNotification])
                        ->with(['success' => 'Slack Integration successfully connected']);
        }
    }

    /**
     * anySendSlackMessage
     * --------------------------------------------------
     * @return Sends the actual slack message
     * --------------------------------------------------
     */
    public function anySendSlackMessage() {
        /* Get the slack notification instance */
        $slackNotification = Auth::user()->notifications()->where('type','slack')->first();

        /* Send notification if possible */
        if ($slackNotification->is_enabled) {
            $slackNotification->sendDailyMessage();
            return Redirect::route('notification.configureSlack', ['notification' => $slackNotification])
                        ->with(['success' => 'Message succesfully sent.']);
        } else {
            return Redirect::route('notification.configureSlack', ['notification' => $slackNotification])
                        ->with(['error' => 'Your Slack configuration is not correct. Please setup first']);
        }

    }

    /**
     * ================================================== *
     *                   PRIVATE SECTION                  *
     * ================================================== *
     */

} /* NotificationController */
