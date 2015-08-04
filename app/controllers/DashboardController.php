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
        $user = Auth::user();
        $user->created_at = strtotime('22-04-2015');
        $user->save();
        /* Detect if the user has no dashboard, and redirect */
        if (!Auth::user()->dashboards()->count()) {
            return Redirect::route('signup-wizard.personal-widgets');
        }

        /* Render the page */
        return View::make('dashboard.dashboard')
            ->with('dashboards', Auth::user()->dashboards);
    }

} /* DashboardController */
