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
        /* Check for valid user and redirect if not signed in */
        if (!Auth::check()) {
            return Redirect::route('signup-wizard.authentication');
        } else {
            return View::make('dashboard.dashboard')
                ->with('dashboards', Auth::user()->dashboards);
        }
    }

} /* DashboardController */
