<?php


/**
 * --------------------------------------------------------------------------
 * DashboardController: Handles the authentication related sites
 * --------------------------------------------------------------------------
 */
class DashboardController extends BaseController
{
   /**
     * Controller: getDashboard
    /**
     * getDashboard
     * --------------------------------------------------
     * @return Renders the dashboard page
     * --------------------------------------------------
     */
    public function getDashboard() {
        Auth::loginUsingId(1);

        // Getting the different types of widgets.

        // Return.
        return View::make('dashboard.dashboard')
            ->with('dashboards', Auth::user()->dashboards);
    }

} /* DashboardController */
