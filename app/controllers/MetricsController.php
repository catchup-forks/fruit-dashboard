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
            $diff = Carbon::now()->diffInDays(Carbon::parse($user->settings->last_activity));
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
            foreach ($user->dataObjects as $data) {
                if ($data->getManager() instanceof HistogramDataManager) {
                    $numberOfDataPoints += count($data->decode());
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
                $key = $widget->getDescriptor()->category;
                if (array_key_exists($key, $services)) {
                    $data[$services[$key]] += 1;
                }
            }

        /* Get connections only for one service */
        } else {
            $widgetcount = 0;
            foreach (Widget::all() as $widget) {
                if ($widget->getDescriptor()->category == $service) {
                    $widgetcount += 1;
                }
            }
            $data[$service] = $widgetcount;
        }

        /* Return json */
        return Response::json($data);
    }

    /**
     * getHasActiveWidgetCount
     * --------------------------------------------------
     * @return Returns the number of users who have at least
     *          one active widget with the provided service
     * --------------------------------------------------
     */
    public function getHasActiveWidgetCount($service) {
        /* Build basic data */
        $data = $this->buildBasicData();

        /* Get all connections by services */
        if ($service == 'all') {
            /* Build initial services and initial data */
            $initialServices = array();
            foreach (SiteConstants::getAllGroupsMeta() as $serviceMeta) {
                $data[$serviceMeta['display_name']] = 0;
                $initialServices[$serviceMeta['name']] = array(
                    'display_name' => $serviceMeta['display_name'],
                    'value' => FALSE
                );
            }

            /* Iterate through all users */
            foreach (User::all() as $user) {
                /* Copy initialServices to currentServices */
                $currentServices = $initialServices;
                /* Iterate through the widgets of the user */
                foreach ($user->widgets as $widget) {
                    $key = $widget->getDescriptor()->category;
                    if (array_key_exists($key, $currentServices)) {
                        /* Check active connection */
                        if ($widget->state == 'active') {
                            $currentServices[$key]['value'] = TRUE;
                        }
                    }
                }

                /* Add current connections to data */
                foreach ($currentServices as $currentService) {
                    $data[$currentService['display_name']] += (int)$currentService['value'];
                }
            }

        /* Get connections only for one service */
        } else {
            $data[$service] = 0;
            /* Iterate through all users */
            foreach (User::all() as $user) {
                $serviceActive = FALSE;
                /* Iterate through the widgets of the user */
                foreach ($user->widgets as $widget) {
                    /* Check active connection */
                    if (($widget->getDescriptor()->category == $service) and
                        ($widget->state == 'active')) {
                        $serviceActive = TRUE;
                    }

                }
                /* Add active connection to data */
                $data[$service] += (int)$serviceActive;
            }
        }

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
