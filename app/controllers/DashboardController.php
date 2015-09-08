<?php


/**
 * --------------------------------------------------------------------------
 * DashboardController: Handles the authentication related sites
 * --------------------------------------------------------------------------
 */
class DashboardController extends BaseController
{
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
        /* Check the default dashboard and create if not exists */
        Auth::user()->checkOrCreateDefaultDashboard();

        /* Checking the user's widget data integrity */
        Widget::checkIntegrity(Auth::user());

        /* Render the page */
        return View::make('dashboard.dashboard');
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
        $dashboard = $this->getDashboard($dashboardId);
        if (is_null($dashboard)) {
            return Response::json(FALSE);
        }
        $dashboard->delete();

        /* Return. */
        return Response::json(TRUE);
    }

    /**
     * anyToggleDashboardLock
     * --------------------------------------------------
     * @return Toggles the locking for a dashboard.
     * --------------------------------------------------
     */
    public function anyToggleDashboardLock($dashboardId) {
        $dashboard = $this->getDashboard($dashboardId);
        if (is_null($dashboard)) {
            return Response::json(FALSE);
        }

        if($dashboard->is_locked) {
          $dashboard->is_locked = FALSE;
        } else {
          $dashboard->is_locked = TRUE;
        }
        $dashboard->save();

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
     * anyLockAllDashboards
     * --------------------------------------------------
     * @return Locks all dashboards.
     * --------------------------------------------------
     */
    public function anyLockAllDashboards($userId) {
        $user = User::find($userId);
        if (is_null($user)) {
            return Response::json(FALSE);
        }

        foreach ($user->dashboards as $dashboard) {
            $dashboard->is_locked = TRUE;
            $dashboard->save();
        }

        /* Return. */
        return Response::json(TRUE);
    }

    /**
     * anyUnlockAllDashboards
     * --------------------------------------------------
     * @return Unlocks all dashboards.
     * --------------------------------------------------
     */
    public function anyUnlockAllDashboards($userId) {
        $user = User::find($userId);
        if (is_null($user)) {
            return Response::json(FALSE);
        }

        foreach ($user->dashboards as $dashboard) {
            $dashboard->is_locked = FALSE;
            $dashboard->save();
        }

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
