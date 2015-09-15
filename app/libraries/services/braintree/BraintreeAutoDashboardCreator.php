<?php

class BraintreeAutoDashboardCreator extends GeneralAutoDashboardCreator
{
    const DAYS = 30;

    /* -- Class properties -- */
    /* LATE STATIC BINDING. */
    protected static $positioning = array(
        'braintree_mrr'  => '{"col":4,"row":1,"size_x":6,"size_y":6}',
        'braintree_arr'  => '{"col":2,"row":7,"size_x":5,"size_y":5}',
        'braintree_arpu' => '{"col":7,"row":7,"size_x":5,"size_y":5}',
    );
    protected static $service = 'braintree';
    /* /LATE STATIC BINDING. */

}