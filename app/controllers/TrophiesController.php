<?php


/**
 * --------------------------------------------------------------------------
 * TrophiesController: Handles the trophies related sites
 * --------------------------------------------------------------------------
 */
class TrophiesController extends BaseController
{
    /**
     * ================================================== *
     *                   PUBLIC SECTION                   *
     * ================================================== *
     */

    /**
     * anySettings
     * --------------------------------------------------
     * @return Renders the Trophies page
     * --------------------------------------------------
     */
    public function anyTrophies() {
        /* Render view */
        return View::make('trophies.trophies');
    }

}

