<?php


/**
 * --------------------------------------------------------------------------
 * MetricsController: Creates queries for our own metrics, and returns the
 *                  values in formatted json for out webhook widgets.
 * --------------------------------------------------------------------------
 */
class MetricsController extends BaseController
{
    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * getRegisteredUserCount
     * --------------------------------------------------
     * @return Returns the number of registered users in json
     * --------------------------------------------------
     */
    public function getRegisteredUserCount() {
        /* Create data for the json */
        $data = [
            "date"      => Carbon::now()->toDateString(),
            "timestamp" => Carbon::now()->getTimestamp(),
            "value"     => User::all()->count()
        ];

        /* Return json */
        return Response::json($data);
    }

    /**
     * getActiveUserCount
     * --------------------------------------------------
     * @return Returns the number of active users in json
     * --------------------------------------------------
     */
    public function getActiveUserCount() {
        /* Get active users */
        $activeUsers = 0;

        foreach (User::all() as $user) {
            $diff = Carbon::now()->diffInDays(Carbon::parse($user->last_activity));
            if ($diff <= 30) {
                $activeUsers += 1;
            }
        }

        /* Create data for the json */
        $data = [
            "date"      => Carbon::now()->toDateString(),
            "timestamp" => Carbon::now()->getTimestamp(),
            "value"     => $activeUsers
        ];

        /* Return json */
        return Response::json($data);
    }

    /**
     * getServiceWidgetUsersCount
     * --------------------------------------------------
     * @return Returns the number of users who have at least 
     *          one widget with the provided service 
     * --------------------------------------------------
     */
    public function getServiceWidgetUsersCount($service) {
        /* Get active users */
        $serviceWidgetUsers = 0;

        foreach (User::all() as $user) {
            /* Skip if service is not connected */
            if (!$user->isServiceConnected($service)) {
                continue;
            } else {
                /* Check for widgets */
                foreach ($user->widgets as $widget) {
                    if ( ($widget->descriptor->category == $service) and
                         ($widget->state == 'active') ) {
                        /* Increase counter */
                        $serviceWidgetUsers += 1;
                        break;
                    }
                }
            }
        }

        /* Create data for the json */
        $data = [
            "date"      => Carbon::now()->toDateString(),
            "timestamp" => Carbon::now()->getTimestamp(),
            "value"     => $serviceWidgetUsers
        ];

        /* Return json */
        return Response::json($data);
    }

} /* MetricsController */
