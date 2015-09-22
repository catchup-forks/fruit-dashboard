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
        $widgetId = parent::getSettings()['related_widget'];
        $widget = Widget::find($widgetId);
        if (is_null($widget)) {
            /* Related widget does not exist. */
            /* Deleting widget setting sharing object state to deleted. */
            return null;
        }

        return $widget->getSpecific();
    }

    /**
     * getSettings
     * Merging the setting of this and the related widget.
     * --------------------------------------------------
     * @return Widget
     * --------------------------------------------------
     */
    public function getSettings() {
        if (is_null($this->getRelatedWidget())) {
            return parent::getSettings();
        }
        return array_merge(parent::getSettings(), $this->getRelatedWidget()->getSettings());
    }

    public function __call($method, $args) {
        return $this->getRelatedWidget()->{$method}($args);
    }
}
?>
