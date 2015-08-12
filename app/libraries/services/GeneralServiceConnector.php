<?php

/**
* --------------------------------------------------------------------------
* GeneralServiceConnector:
*     Abstract class used mainly as an interface for service connectors.
* --------------------------------------------------------------------------
*/

abstract class GeneralServiceConnector
{
    /* -- Class properties -- */
    protected $user;

    /* -- Constructor -- */
    function __construct($user) {
        $this->user = $user;
    }

    abstract public function connect();
    abstract public function disconnect();

} /* GeneralServiceConnector */
