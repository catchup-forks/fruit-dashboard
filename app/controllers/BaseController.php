<?php

/**
 * --------------------------------------------------------------------------
 * BaseController: Parent class for all controller
 * --------------------------------------------------------------------------
 */
class BaseController extends Controller {

	/**
	 * setupLayout
	 * --------------------------------------------------
	 * Setup the layout used by the controller.
     * @return void
     * --------------------------------------------------
     */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

} /* BaseController */
