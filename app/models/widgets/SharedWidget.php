<?php

class SharedWidget extends Widget
{
    /* -- Settings -- */
    public static $settingsFields = array(
        'related_widget' => array(
            'name'       => 'Related widget',
            'type'       => 'INT',
            'validation' => 'required',
        ),
        'sharing_object' => array(
            'name'       => 'Sharing object',
            'type'       => 'INT',
            'validation' => 'required',
        ),
    );

    /* The settings to setup in the setup-wizard.*/
    public static $setupSettings = array();

    /**
     * getRelatedWidget
     * --------------------------------------------------
     * Returning the corresponding widget.
     * @return Widget
     * --------------------------------------------------
     */
    public function getRelatedWidget() {
        $widgetId = $this->getSettings()['related_widget'];
        $widget = Widget::find($widgetId);

        if (is_null($widget)) {
            /* Related widget does not exist. */
            /* Deleting widget setting sharing object state to deleted. */
            return null;
        }

        return $widget->getSpecific();
    }

    public function __call($method, $args) {
        Log::info($method, $args);
    }
}
?>
