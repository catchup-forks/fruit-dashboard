<?php

class BraintreeAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    const DAYS = 30;

    /* -- Class properties -- */
    /* LATE STATIC BINDING. */
    protected static $positioning = array(
        'braintree_mrr'  => '{"col":1,"row":1,"size_x":6,"size_y":6}',
        'braintree_arr'  => '{"col":7,"row":1,"size_x":6,"size_y":6}',
        'braintree_arpu' => '{"col":1,"row":7,"size_x":6,"size_y":6}',
    );
    protected static $service = 'braintree';
    /* /LATE STATIC BINDING. */

}