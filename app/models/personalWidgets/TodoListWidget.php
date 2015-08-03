<?php

class TodoListWidget extends Widget implements iDataWidget
{
    /* -- Settings -- */
    public static $settingsFields = array();
    /* The settings to setup in the setup-wizard. */
    public static $setupSettings = array();

    /**
     * createDataScheme
     * --------------------------------------------------
     * Returning a deafult scheme for the data.
     * @return string, the note text.
     * --------------------------------------------------
    */
    public function createDataScheme() {
        return json_encode(array());
    }
}

?>
