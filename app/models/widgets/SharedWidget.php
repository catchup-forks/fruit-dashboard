<?php

class SharedWidget extends Widget
{
    /* -- Settings -- */
    public static $sharedSettings = array(
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
            $this->delete();
            /* Related widget does not exist. */
            /* Deleting widget setting sharing object state to deleted. */
            return null;
        }

        return $widget->getSpecific();
    }

}
?>
