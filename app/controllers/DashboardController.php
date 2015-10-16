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
        $dm = DataManager::find(6);
        $dm->initializeData();
        exit(94);
        /* Get the current user */
        $user = Auth::user();

        /* Check the default dashboard and create if not exists */
        $user->checkOrCreateDefaultDashboard();

        /* Handle the widget sharings. */
        $user->handleWidgetSharings();

        /* Check onboarding state */
        if ($user->settings->onboarding_state != 'finished') {
            return View::make('dashboard.dashboard-onboarding-not-finished', array(
                    'currentState' => $user->settings->onboarding_state
                ));
        }

        /* Get active dashboard, if the url contains it */
        $parameters = array();
        $activeDashboard = Request::query('active');
        if ($activeDashboard) {
            $parameters['activeDashboard'] = $activeDashboard;
        }

        /* Checking the user's data managers integrity. */
        if (self::OPTIMIZE) {
            var_dump(' -- DEBUG LOG --');
            $time = microtime(TRUE);
            $startTime = $time;
            $queries = count(DB::getQueryLog());
            $startTime = microtime(TRUE);
        }
        $user->checkDataManagersIntegrity();
        if (self::OPTIMIZE) {
            var_dump(
                "DM check integrity time: ". (microtime(TRUE) - $time) .
                " (" . (count(DB::getQueryLog()) - $queries ). ' db queries)'
            );
            $queries = count(DB::getQueryLog());
            $time = microtime(TRUE);
        }


        /* Checking the user's widgets integrity */
        $user->checkWidgetsIntegrity();
        if (self::OPTIMIZE) {
           var_dump(
               "Widget check integrity time: ". (microtime(TRUE) - $time) .
               " (" . (count(DB::getQueryLog()) - $queries ). ' db queries)'
           );
           $queries = count(DB::getQueryLog());
           $time = microtime(TRUE);
        }

        /* Creating view */
        $view = $user->createDashboardView();
        if (self::OPTIMIZE) {
            var_dump(
                "Dashboards/widgets data loading time: ". (microtime(TRUE) - $time) .
                " (" . (count(DB::getQueryLog()) - $queries ). ' db queries)'
            );
            $queries = count(DB::getQueryLog());
            $time = microtime(TRUE);
        }

        if (self::OPTIMIZE) {
            $view->render();
            var_dump(
                "Rendering time: ". (microtime(TRUE) - $time) .
                " (" . (count(DB::getQueryLog()) - $queries ). ' db queries)'
            );
            var_dump(
                 "Total loading time: ". (microtime(TRUE) - LARAVEL_START) .
                 " (" . count(DB::getQueryLog()) . ' db queries)'
            );
            exit(94);
        }

        try {
            /* Trying to render the view. */
            $renderedView = $view->render();
            Log::info("Rendering time:" . (microtime(TRUE) - LARAVEL_START));
            return $renderedView;
        } catch (Exception $e) {
            /* Error occured trying to find the widget. */
            $user->turnOffBrokenWidgets();
            /* Recreating view. */
            $view = $user->createDashboardView();
        }
        return $view;

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

} /* DashboardController */
