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
     * anyTest
     * --------------------------------------------------
     * @return Renders the test page
     * --------------------------------------------------
     */
    public function anyTest() {
        /* Get the notification objects for the user */
        $notifications = Auth::user()->notifications;

        /* Render view */
        return View::make('notification.notification-test', ['notifications' => $notifications]);
    }

     /**
     * anySend
     * --------------------------------------------------
     * @param (integer) ($notificationId) The notification id
     * @return Sends the selected notification
     * --------------------------------------------------
     */
    public function anySend($notificationId) {
        /* Get the requested notification */
        $notification = Notification::where('id', $notificationId)->where('user_id', Auth::user()->id)->first();

        if ($notification == null) {
            return Redirect::route('notification.test')->with(['error' => 'We couldn\'t send the requested notification.']);
        }

        /* Send notification */
        $notification->fire();

        /* Return */
        return Redirect::route('notification.test')->with(['success' => 'Your notification has been sent.']);;
    }

    /**
     * ================================================== *
     *                   PRIVATE SECTION                  *
     * ================================================== *
     */

} /* NotificationController */
