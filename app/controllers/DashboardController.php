<?php

/**
 * --------------------------------------------------------------------------
 * DashboardController: Handles the authentication related sites
 * --------------------------------------------------------------------------
 */
class DashboardController extends BaseController
{
    const OPTIMIZE = false;

    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * anyDashboard
     * --------------------------------------------------
     * Returns the user dashboard, or redirects to signup wizard
     * @return Renders the dashboard page
     * --------------------------------------------------
     */
    public function anyDashboard($dashboardId=null)
    {

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

        $dashboard = $user->dashboards()->find($dashboardId);
        if (is_null($dashboard)) {
            /* Using default dashboard, if the dashboard is not set. */
            $dashboard = $user->dashboards()
                ->where('is_default', true)
                ->first();

            $dashboardId = $dashboard->id;
        } 

        /* For debug purposes. */
        if (self::OPTIMIZE) {
            return $this->showOptimizeLog($dashboard);
            exit(94);
        }

        /* No caching in local development */
        if ( ! App::environment('local')) {
            /* Trying to load from cache. */
            $cachedDashboard = $this->getCache($dashboardId);
            if ( ! is_null($cachedDashboard)) {
                /* Some logging */
                if ( ! App::environment('production')) {
                    Log::info("Loading dashboard from cache.");
                    Log::info("Rendering time:" . (microtime(true) - LARAVEL_START));
                }

                /* Return the cached dashboard. */
                return $cachedDashboard;
            }
        }

        /* Checking the user's widgets integrity */
        $dashboard->checkWidgetsIntegrity();
        
        /* Creating view */
        $view = $dashboard->createView();

        try {
            /* Trying to render the view. */
            $renderedView = $view->render();

            if ( ! App::environment('producion')) {
                Log::info("Rendering time:" . (microtime(true) - LARAVEL_START));
            }
        } catch (Exception $e) {
            /* Error occured, trying to find the widget. */
            $dashboard->turnOffBrokenWidgets();
            /* Recreating view. */
            $renderedView = $dashboard->createView()->render();
        }

        /* Saving the cache, and returning the view. */
        $sessionKeys = array_keys(Session::all());
        if ( ! (in_array('error', $sessionKeys) || in_array('success', $sessionKeys))) {
            $this->saveToCache($renderedView, $dashboardId);
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
            return Response::json(false);
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
        return Response::json(true);
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
            return Response::json(false);
        }

        $dashboard->is_locked = true;
        $dashboard->save();

        /* Return. */
        return Response::json(true);
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
            return Response::json(false);
        }

        $dashboard->is_locked = false;
        $dashboard->save();

        /* Return. */
        return Response::json(true);
    }

    /**
     * getSetVelocity
     * --------------------------------------------------
     * @return Sets the active velocity for the dashboard.
     * --------------------------------------------------
     */
    public function getSetVelocity($dashboardId, $velocity) {
        /* Getting dashboard. */
        $dashboard = $this->getDashboard($dashboardId);
        if (is_null($dashboard)) {
            return Redirect::back()->with('error', 'Dashboard not found');
        }

        try {
            $dashboard->changeVelocity($velocity);
        } catch (WidgetException $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }

        return Redirect::back();
    }

    /**
     * anyMakeDefault
     * --------------------------------------------------
     * @return Makes a dashboard the default one.
     * --------------------------------------------------
     */
    public function anyMakeDefault($dashboardId) {
        // Make is_default false for all dashboards
        foreach (Auth::user()->dashboards()->where('is_default', true)->get() as $oldDashboard) {
            $oldDashboard->is_default = false;
            $oldDashboard->save();
        }

        $dashboard = $this->getDashboard($dashboardId);
        if (is_null($dashboard)) {
            return Response::json(false);
        }

        $dashboard->is_default = true;
        $dashboard->save();

        /* Return. */
        return Response::json(true);
    }

    /**
     * postRenameDashboard
     *
     */
    public function postRenameDashboard($dashboardId) {
        $dashboard = $this->getDashboard($dashboardId);
        if (is_null($dashboard)) {
            return Response::json(false);
        }

        $newName = Input::get('dashboard_name');
        if (is_null($newName)) {
            return Response::json(false);
        }

        $dashboard->name = $newName;
        $dashboard->save();

        /* Return. */
        return Response::json(true);
    }

    /**
     * postCreateDashboard
     *
     */
    public function postCreateDashboard() {
        $name = Input::get('dashboard_name');
        if (empty($name)) {
            return Response::json(false);
        }

        /* Creating dashboard. */
        $dashboard = new Dashboard(array(
            'name'       => $name,
            'background' => true,
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
        return Response::json(true);
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
            return null;
        }

        if ($dashboard->user->id != Auth::user()->id) {
            return NULL;
        }
        
        return $dashboard;
    }
    /**
     * showOptimizeLog
     * Renders the status log.
     * --------------------------------------------------
     * @param Dashboard $dashboard
     * --------------------------------------------------
     */
    private function showOptimizeLog($dashboard) {
        var_dump(' -- DEBUG LOG --');
        $time = microtime(true);
        $startTime = $time;
        $queries = count(DB::getQueryLog());
        $startTime = microtime(true);
        $memUsage = memory_get_usage();
        var_dump("Initial memory usage: " . number_format($memUsage));
        /* Checking the user's widgets integrity */
        $dashboard->checkWidgetsIntegrity();
        var_dump(
            "Widget check integrity time: ". (microtime(true) - $time) .
            " (" . (count(DB::getQueryLog()) - $queries ). ' db queries ' .
            number_format(memory_get_usage() - $memUsage) . ' bytes of memory)'
        );
        $memUsage = memory_get_usage();
        $queries = count(DB::getQueryLog());
        $time = microtime(true);

        /* Creating view */
        $view = $dashboard->createView();
        var_dump(
            "Dashboards/widgets data loading time: ". (microtime(true) - $time) .
            " (" . (count(DB::getQueryLog()) - $queries ). ' db queries ' .
            number_format(memory_get_usage() - $memUsage) . ' bytes of memory)'
        );
        $memUsage = memory_get_usage();
        $queries = count(DB::getQueryLog());
        $time = microtime(true);

        $view->render();
        var_dump(
            "Rendering time: ". (microtime(true) - $time) .
            " (" . (count(DB::getQueryLog()) - $queries ). ' db queries)'
        );
        var_dump("Total memory usage: " . number_format(memory_get_usage()) . " bytes");
        var_dump(
             "Total loading time: ". (microtime(true) - LARAVEL_START) .
             " (" . count(DB::getQueryLog()) . ' db queries)'
        );
        var_dump(DB::getQueryLog());
        return;
    }

    /**
     * getCache
     * Returns the dashboard if it's cached.
     * --------------------------------------------------
     * @param int dashboardId
     * @return View/null
     * --------------------------------------------------
     */
    private function getCache($dashboardId) {
        $user = Auth::user();
        if ( ! $user->update_cache) {
            return Cache::get($this->getDashboardCacheKey($dashboardId));
        }
        return;
    }

    /**
     * saveToCache
     * Saving the dashboard to cache.
     * --------------------------------------------------
     * @param int $dashboardId
     * @param string $renderedView
     * --------------------------------------------------
     */
    private function saveToCache($renderedView, $dashboardId) {
        $user = Auth::user();
        Cache::put(
            $this->getDashboardCacheKey($dashboardId),
            $renderedView,
            SiteConstants::getDashboardCacheMinutes()
        );
        $user->update_cache = false;
        $user->save();
    }

    /**
     * getDashboardCacheKey
     * Returns the cache key for the user's dashboard.
     * --------------------------------------------------
     * @param int $dashboardId
     * @return string
     * --------------------------------------------------
     */
    private function getDashboardCacheKey($dashboardId) {
        return Auth::user()->id . '_dashboard_' . $dashboardId;
    }

} /* DashboardController */
