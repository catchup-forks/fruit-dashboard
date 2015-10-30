<?php

/**
 * --------------------------------------------------------------------------
 * DashboardController: Handles the authentication related sites
 * --------------------------------------------------------------------------
 */
class DashboardController extends BaseController
{
    const OPTIMIZE = FALSE;

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * anyDashboard
     * --------------------------------------------------
     * returns the user dashboard, or redirects to signup wizard
     * @return Renders the dashboard page
     * --------------------------------------------------
     */
    public function anyDashboard() {
        Data::create(array(
            'user_id'  => 1,
            'criteria'     => json_encode(array('profile' => 85953960)),
            'descriptor_id' => 65
        ));
        exit(94);
        /* No caching in local development */
        if ( ! App::environment('local')) {
            /* Trying to load from cache. */
            $cachedDashboard = $this->getFromCache();
            if ( ! is_null($cachedDashboard)) {
                /* Some logging */
                if ( ! App::environment('production')) {
                    Log::info("Loading dashboard from cache.");
                    Log::info("Rendering time:" . (microtime(TRUE) - LARAVEL_START));
                }

                /* Return the cached dashboard. */
                return $cachedDashboard;
            }
        }
        if (self::OPTIMIZE) {
            return $this->showOptimizeLog(Auth::user());
            exit(94);
        }

        /* Get the current user */
        $user = Auth::user();

        /* Check the default dashboard and create if not exists */
        $user->checkOrCreateDefaultDashboard();

        /* Check onboarding state */
        if ($user->settings->onboarding_state != 'finished') {
            return View::make('dashboard.dashboard-onboarding-not-finished', array(
                    'currentState' => $user->settings->onboarding_state
                ));
        }

        /* Get active dashboard, if the url contains it */
        $parameters = array();
        $activeDashboard = Input::get('active');
        if ($activeDashboard) {
            $parameters['activeDashboard'] = $activeDashboard;
        }

        /* Checking the user's widgets integrity */
        $user->checkWidgetsIntegrity();

        /* Creating view */
        $view = $user->createDashboardView($parameters);

        try {
            /* Trying to render the view. */
            $renderedView = $view->render();

            if ( ! App::environment('producion')) {
                Log::info("Rendering time:" . (microtime(TRUE) - LARAVEL_START));
            }
        } catch (Exception $e) {
            /* Error occured, trying to find the widget. */
            $user->turnOffBrokenWidgets();
            /* Recreating view. */
            $renderedView= $user->createDashboardView($parameters)->render();
        }

        /* Saving the cache, and returning the view. */
        $sessionKeys = array_keys(Session::all());
        if ( ! (in_array('error', $sessionKeys) || in_array('success', $sessionKeys))) {
            $this->saveToCache($renderedView);
        }
        return $renderedView;

    }

    /**
     * getManageDashboards
     * --------------------------------------------------
     * @return Renders the mange dashboards page
     * --------------------------------------------------
     */
    public function getManageDashboards() {
        /* Check the default dashboard and create if not exists */
        Auth::user()->checkOrCreateDefaultDashboard();

        /* Render the page */
        return View::make('dashboard.manage-dashboards');
    }

    /**
     * anyDeleteDashboard
     * --------------------------------------------------
     * @return Deletes a dashboard.
     * --------------------------------------------------
     */
    public function anyDeleteDashboard($dashboardId) {
        /* Get the dashboard */
        $dashboard = $this->getDashboard($dashboardId);
        if (is_null($dashboard)) {
            return Response::json(FALSE);
        }

        /* Track event | DELETE DASHBOARD */
        $tracker = new GlobalTracker();
        $tracker->trackAll('lazy', array(
            'en' => 'Dashboard deleted',
            'el' => $dashboard->name)
        );

        /* Delete the dashboard*/
        $dashboard->delete();

        /* Return. */
        return Response::json(TRUE);
    }

    /**
     * anyLockDashboard
     * --------------------------------------------------
     * @return Locks a dashboard.
     * --------------------------------------------------
     */
    public function anyLockDashboard($dashboardId) {
        $dashboard = $this->getDashboard($dashboardId);
        if (is_null($dashboard)) {
            return Response::json(FALSE);
        }

        $dashboard->is_locked = TRUE;
        $dashboard->save();

        /* Return. */
        return Response::json(TRUE);
    }

    /**
     * anyUnlockDashboard
     * --------------------------------------------------
     * @return Unlocks a dashboard.
     * --------------------------------------------------
     */
    public function anyUnlockDashboard($dashboardId) {
        $dashboard = $this->getDashboard($dashboardId);
        if (is_null($dashboard)) {
            return Response::json(FALSE);
        }

        $dashboard->is_locked = FALSE;
        $dashboard->save();

        /* Return. */
        return Response::json(TRUE);
    }

    /**
     * anyMakeDefault
     * --------------------------------------------------
     * @return Makes a dashboard the default one.
     * --------------------------------------------------
     */
    public function anyMakeDefault($dashboardId) {
        // Make is_default false for all dashboards
        foreach (Auth::user()->dashboards()->where('is_default', TRUE)->get() as $oldDashboard) {
            $oldDashboard->is_default = FALSE;
            $oldDashboard->save();
        }

        $dashboard = $this->getDashboard($dashboardId);
        if (is_null($dashboard)) {
            return Response::json(FALSE);
        }

        $dashboard->is_default = TRUE;
        $dashboard->save();

        /* Return. */
        return Response::json(TRUE);
    }

    /**
     * postRenameDashboard
     *
     */
    public function postRenameDashboard($dashboardId) {
        $dashboard = $this->getDashboard($dashboardId);
        if (is_null($dashboard)) {
            return Response::json(FALSE);
        }

        $newName = Input::get('dashboard_name');
        if (is_null($newName)) {
            return Response::json(FALSE);
        }

        $dashboard->name = $newName;
        $dashboard->save();

        /* Return. */
        return Response::json(TRUE);
    }

    /**
     * postCreateDashboard
     *
     */
    public function postCreateDashboard() {
        $name = Input::get('dashboard_name');
        if (empty($name)) {
            return Response::json(FALSE);
        }

        /* Creating dashboard. */
        $dashboard = new Dashboard(array(
            'name'       => $name,
            'background' => TRUE,
            'number'     => Auth::user()->dashboards->max('number') + 1
        ));
        $dashboard->user()->associate(Auth::user());
        $dashboard->save();

        /* Track event | ADD DASHBOARD */
        $tracker = new GlobalTracker();
        $tracker->trackAll('lazy', array(
            'en' => 'Dashboard added',
            'el' => $dashboard->name)
        );

        /* Return. */
        return Response::json(TRUE);
    }

    /**
     * anyGetDashboards
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
    public function anyGetDashboards() {
        $dashboards = array();
        foreach (Auth::user()->dashboards as $dashboard) {
            array_push($dashboards, array(
                'id'        => $dashboard->id,
                'name'      => $dashboard->name,
                'is_locked' => $dashboard->is_locked,
            ));
        }

        /* Return. */
        return Response::json($dashboards);
    }

    /**
     * ================================================== *
     *                   PRIVATE SECTION                  *
     * ================================================== *
     */

    /**
     * getDashboard
     * --------------------------------------------------
     * @return Dashboard
     * --------------------------------------------------
     */
    private function getDashboard($dashboardId) {
        $dashboard = Dashboard::find($dashboardId);
        if (is_null($dashboard)) {
            return NULL;
        }
        if ($dashboard->user != Auth::user()) {
            return NULL;
        }
        return $dashboard;
    }
    /**
     * showOptimizeLog
     * Renders the status log.
     * --------------------------------------------------
     * @param User $user
     * --------------------------------------------------
     */
    private function showOptimizeLog($user) {
        exit(94);
        var_dump(' -- DEBUG LOG --');
        $time = microtime(TRUE);
        $startTime = $time;
        $queries = count(DB::getQueryLog());
        $startTime = microtime(TRUE);
        $memUsage = memory_get_usage();
        var_dump("Initial memory usage: " . number_format($memUsage));
        /* Checking the user's widgets integrity */
        $user->checkWidgetsIntegrity();
        var_dump(
            "Widget check integrity time: ". (microtime(TRUE) - $time) .
            " (" . (count(DB::getQueryLog()) - $queries ). ' db queries ' .
            number_format(memory_get_usage() - $memUsage) . ' bytes of memory)'
        );
        $memUsage = memory_get_usage();
        $queries = count(DB::getQueryLog());
        $time = microtime(TRUE);

        /* Creating view */
        $view = $user->createDashboardView();
        var_dump(
            "Dashboards/widgets data loading time: ". (microtime(TRUE) - $time) .
            " (" . (count(DB::getQueryLog()) - $queries ). ' db queries ' .
            number_format(memory_get_usage() - $memUsage) . ' bytes of memory)'
        );
        $memUsage = memory_get_usage();
        $queries = count(DB::getQueryLog());
        $time = microtime(TRUE);

        $view->render();
        var_dump(
            "Rendering time: ". (microtime(TRUE) - $time) .
            " (" . (count(DB::getQueryLog()) - $queries ). ' db queries)'
        );
        var_dump("Total memory usage: " . number_format(memory_get_usage()) . " bytes");
        var_dump(
             "Total loading time: ". (microtime(TRUE) - LARAVEL_START) .
             " (" . count(DB::getQueryLog()) . ' db queries)'
        );
        var_dump(DB::getQueryLog());
        return;
    }

    /**
     * getFromCache
     * Returns the dashboard if it's cached.
     * --------------------------------------------------
     * @return View/null
     * --------------------------------------------------
     */
    private function getFromCache() {
        $user = Auth::user();
        if ( ! $user->update_cache) {
            return Cache::get($this->getDashboardCacheKey());
        }
        return;
    }

    /**
     * saveToCache
     * Saving the dashboard to cache.
     * --------------------------------------------------
     * @param string $renderedView
     * --------------------------------------------------
     */
    private function saveToCache($renderedView) {
        $user = Auth::user();
        Cache::put(
            $this->getDashboardCacheKey(),
            $renderedView,
            SiteConstants::getDashboardCacheMinutes()
        );
        $user->update_cache = FALSE;
        $user->save();
    }

    /**
     * getDashboardCacheKey
     * Returns the cache key for the user's dashboard.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
     */
    private function getDashboardCacheKey() {
        return 'dashboard_' . Auth::user()->id;
    }

} /* DashboardController */
