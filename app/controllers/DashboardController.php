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
        /*
        $connector = new BraintreeConnector(Auth::user());
        $connector->connect();
        foreach (Braintree_Plan::all() as $plan) {
            return var_dump($plan);
        }*/
        return View::make('dashboard.dashboard')
            ->with('dashboards', Auth::user()->dashboards);
    }

} /* DashboardController */
