<?php


/**
 * --------------------------------------------------------------------------
 * SignupWizardController: Handles the signup process
 * --------------------------------------------------------------------------
 */
class SignupWizardController extends BaseController
{

    /**
     * getAuthentication
     * @return Renders the authentication step
     */
    public function getAuthentication() {
        /* Render the page */
        return View::make('signup-wizard.authentication');
    }

    /**
     * postAuthentication
     * @return Saves the user authentication data
     */
    public function postAuthentication() {
        /* Render the page */
        return View::make('signup-wizard.authentication');
    }

    /**
     * getPersonalWidgets
     * @return Renders the personal widget setup step
     */
    public function getPersonalWidgets() {
        /* Render the page */
        return View::make('signup-wizard.personal-widgets');
    }

    /**
     * postPersonalWidgets
     * @return Saves the user personal widget settings
     */
    public function postPersonalWidgets() {
        /* Render the page */
        return View::make('signup-wizard.personal-widgets');
    }

    /**
     * getFinancialConnect
     * @return Renders the financial connections step
     */
    public function getFinancialConnect() {
        /* Render the page */
        return View::make('signup-wizard.financial-connections');
    }

    /**
     * postFinancialConnect
     * @return Saves the financial connection setting
     */
    public function postFinancialConnect() {
        /* Render the page */
        return View::make('signup-wizard.financial-connections');
    }

} /* SignupWizardController */