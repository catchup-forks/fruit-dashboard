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
        /* Detect if the user has no dashboard, and redirect */
        if (!Auth::user()->dashboards()->count()) {
            return Redirect::route('signup-wizard.personal-widgets');
        }

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
        /* Detect if the user has no dashboard, and redirect */
        if (!Auth::user()->dashboards()->count()) {
            return Redirect::route('signup-wizard.personal-widgets');
        }

        /* Render the page */
        return View::make('dashboard.manage');
    }

    /**
     * anyDeleteDashboard
     * --------------------------------------------------
     * @return Deletes a dashboard.
     * --------------------------------------------------
     */
    public function anyDeleteDashboard($dashboardId) {
        $dashboard = Auth::user()->dashboards()->where('id', $dashboardId)->get();
        if (is_null($dashboard)) {
            return Response::json(false);
        }
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
        $dashboard = Auth::user()->dashboards()->where('id', $dashboardId)->get();
        if (is_null($dashboard)) {
            return Response::json(false);
        }
        $dashboard->locked = TRUE;
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
        $dashboard = Auth::user()->dashboards()->where('id', $dashboardId)->get();
        if (is_null($dashboard)) {
            return Response::json(false);
        }
        $dashboard->locked = FALSE;
        $dashboard->save();

        /* Return. */
        return Response::json(true);
    }

} /* DashboardController */
