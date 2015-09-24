<?php

class SharedWidget extends Widget
{
    /* -- Settings -- */
    public static $sharedSettings = array(
        'related_widget' => array(
            'name'       => 'Related widget',
            'type'       => 'INT',
            'validation' => 'required',
            'hidden'     => TRUE
        ),
        'sharing_object' => array(
            'name'       => 'Sharing object',
            'type'       => 'INT',
            'validation' => 'required',
            'hidden'     => TRUE
        ),
    );

    /**
     * getSettingsFields
     * Returns the SettingsFields
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
     */
     public static function getSettingsFields() {
        return array_merge(parent::getSettingsFields(), self::$sharedSettings);
     }

    /**
     * getRelatedWidget
     * Returning the corresponding widget.
     * --------------------------------------------------
     * @return Widget
     * --------------------------------------------------
     */
    public function getRelatedWidget() {
        $widgetId = $this->getSettings()['related_widget'];
        $widget = Widget::find($widgetId);
        if (is_null($widget)) {
            /* Related widget does not exist. */
            $this->delete();
            return null;
        }

        return $widget->getSpecific();
    }

}
?>