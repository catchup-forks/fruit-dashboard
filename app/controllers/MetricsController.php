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

        /* Iterate through all users */
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

    /**
     * getAllServiceWidgetsCount
     * --------------------------------------------------
     * @return Returns the number of users who have at least
     *          one widget with the provided service
     * --------------------------------------------------
     */
    public function getAllServiceWidgetsCount() {
        /* Get active users */
        $data = array(
            'timestamp' => Carbon::now()->getTimestamp()
        );
        $services = array();

        foreach (array_merge(SiteConstants::getSocialServices(), SiteConstants::getFinancialServices()) as $serviceMeta) {
            $services[$serviceMeta['name']] = $serviceMeta['display_name'];
        }

        /* Iterate through all users */
        foreach (Widget::all() as $widget) {
            $key = $widget->descriptor->category;
            /* Service exists */
            if (array_key_exists($key, $services)) {
                /* Data key exists. */
                $display_name = $services[$key];
                if ( ! array_key_exists($display_name, $data)) {
                    $data[$display_name] = 0;
                }
                $data[$display_name]++;
            }
        }

        /* Return json */
        return Response::json($data);
    }

    /**
     * getNumberOfDashboards
     * --------------------------------------------------
     * @return Returns the number of the dashboards
     * --------------------------------------------------
     */
    public function getNumberOfDashboards() {
        /* Create data for the json */
        $data = [
            "date"      => Carbon::now()->toDateString(),
            "timestamp" => Carbon::now()->getTimestamp(),
            "value"     => Dashboard::all()->count()
        ];

        /* Return json */
        return Response::json($data);
    }

    /**
     * getNumberOfWidgets
     * --------------------------------------------------
     * @return Returns the number of the dashboards
     * --------------------------------------------------
     */
    public function getNumberOfWidgets() {
        /* Create data for the json */
        $data = [
            "date"      => Carbon::now()->toDateString(),
            "timestamp" => Carbon::now()->getTimestamp(),
            "value"     => Widget::all()->count()
        ];

        /* Return json */
        return Response::json($data);
    }

    /**
     * getNumberOfDataPoints
     * --------------------------------------------------
     * @return Returns the number of the dashboards
     * --------------------------------------------------
     */
    public function getNumberOfDataPoints() {
        $numberOfDataPoints = 0;

        /* Iterate through all users */
        foreach (User::all() as $user) {
            /* Iterate through all widgets */
            foreach ($user->widgets as $widget) {
                try {
                    $numberOfDataPoints += count($widget->getSpecific()->getData());
                } catch (Exception $e) {
                    continue;
                }
            }
        }

        /* Create data for the json */
        $data = [
            "date"      => Carbon::now()->toDateString(),
            "timestamp" => Carbon::now()->getTimestamp(),
            "value"     => $numberOfDataPoints
        ];

        /* Return json */
        return Response::json($data);
    }

} /* MetricsController */
