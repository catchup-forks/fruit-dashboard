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
     * getUserCount
     * --------------------------------------------------
     * @return Returns the number of users
     *          by different dimensions in json
     * --------------------------------------------------
     */
    public function getUserCount($dimension) {
        /* Build basic data */
        $data = $this->buildBasicData();

        /* Get registered users */
        $registeredUsers = User::all()->count();

        /* Get active users */
        $activeUsers = 0;
        foreach (User::all() as $user) {
            $diff = Carbon::now()->diffInDays(Carbon::parse($user->last_activity));
            if ($diff <= 30) {
                $activeUsers += 1;
            }
        }

        /* Build data by dimensions */
        switch ($dimension) {
            case 'registered':
                $data['Registered users'] = $registeredUsers;
                break;

            case 'active':
                $data['Active users'] = $activeUsers;
                break;

            case 'all':
            default:
                $data['Registered users'] = $registeredUsers;
                $data['Active users']     = $activeUsers;
                break;
        }

        /* Return json */
        return Response::json($data);
    }


    /**
     * getVanityCount
     * --------------------------------------------------
     * @return Returns the vanity number(s)
     *          by different dimensions in json
     * --------------------------------------------------
     */
    public function getVanityCount($dimension) {
        /* Build basic data */
        $data = $this->buildBasicData();

        /* Get number of dashboards */
        $numberOfDashboards = Dashboard::all()->count();

        /* Get number of widgets */
        $numberOfWidgets = Widget::all()->count();

        /* Get number of datapoints */
        $numberOfDataPoints = 0;
        foreach (User::all() as $user) {
            foreach ($user->dataManagers as $dataManager) {
                if ($dataManager instanceof HistogramDataManager) {
                    $numberOfDataPoints += count($dataManager->getData());
                }
            }
        }

        /* Build data by dimensions */
        switch ($dimension) {
            case 'dashboards':
                $data['Dashboards'] = $numberOfDashboards;
                break;

            case 'widgets':
                $data['Widgets'] = $numberOfWidgets;
                break;

            case 'datapoints':
                $data['Datapoints'] = $numberOfDataPoints;
                break;

            case 'all':
            default:
                $data['Dashboards'] = $numberOfDashboards;
                $data['Widgets']    = $numberOfWidgets;
                $data['Datapoints'] = $numberOfDataPoints;
                break;
        }

        /* Return json */
        return Response::json($data);
    }

    /**
     * getConnectionsCount
     * --------------------------------------------------
     * @return Returns the number of connections
     *          by different services in json
     * --------------------------------------------------
     */
    public function getConnectionsCount($service) {
        /* Build basic data */
        $data = $this->buildBasicData();

        /* Get all connections by services */
        if ($service == 'all') {
           foreach (SiteConstants::getAllServicesMeta() as $serviceMeta) {
               $data[$serviceMeta['display_name']] = Connection::where('service', $serviceMeta['name'])->count();
           }

        /* Get connections only for one service */
        } else {
            $data[$service] = Connection::where('service', $service)->count();
        }

        /* Return json */
        return Response::json($data);
    }

    /**
     * getWidgetsCount
     * --------------------------------------------------
     * @return Returns the number of widgets
     *          by different services in json
     * --------------------------------------------------
     */
    public function getWidgetsCount($service) {
        /* Build basic data */
        $data = $this->buildBasicData();

        /* Get all connections by services */
        if ($service == 'all') {
            $services = array();
            foreach (SiteConstants::getAllGroupsMeta() as $serviceMeta) {
                $data[$serviceMeta['display_name']] = 0;
                $services[$serviceMeta['name']] = $serviceMeta['display_name'];
            }

            /* Iterate through all widgets */
            foreach (Widget::all() as $widget) {
                $key = $widget->descriptor->category;
                if (array_key_exists($key, $services)) {
                    $data[$services[$key]] += 1;
                }
            }

        /* Get connections only for one service */
        } else {
            $widgetcount = 0;
            foreach (Widget::all() as $widget) {
                if ($widget->descriptor->category == $service) {
                    $widgetcount += 1;
                }
            }
            $data[$service] = $widgetcount;
        }

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
     * ================================================== *
     *                   PRIVATE SECTION                  *
     * ================================================== *
     */
    /**
     * buildBasicData
     * --------------------------------------------------
     * @return Builds the basic data for the JSON object
     * --------------------------------------------------
     */
    public function buildBasicData() {
        return array(
            "date"      => Carbon::now()->toDateString(),
            "timestamp" => Carbon::now()->getTimestamp(),
        );
    }

} /* MetricsController */
