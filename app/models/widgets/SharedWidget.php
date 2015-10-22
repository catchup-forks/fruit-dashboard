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
     * checkIntegrity
     * Checking if the widget exists.
     */
    public function checkIntegrity() {
        parent::checkIntegrity();
        $this->getRelatedWidget();
    }

    /**
     * getMinRows
     * Returning the minimum rows required for the widget.
     * --------------------------------------------------
     * @return int
     * --------------------------------------------------
    */
    public function getMinRows() {
        return $this->getRelatedWidget()->getDescriptor()->min_rows;
    }

    /**
     * getMinCols
     * Returning the minimum rows required for the widget.
     * --------------------------------------------------
     * @return int
     * --------------------------------------------------
    */
    public function getMinCols() {
        return $this->getRelatedWidget()->getDescriptor()->min_cols;
    }

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
        }

        return $widget;
    }

    /**
     * getSharingId
     * Returning the sharing id.
     * --------------------------------------------------
     * @return int
     * --------------------------------------------------
     */
    public function getSharingId() {
        return $this->getSettings()['sharing_object'];
    }

    /**
     * getTemplateMeta
     * Returning data for the gridster init template.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public function getTemplateMeta() {
        $related = $this->getRelatedWidget();
        $meta = parent::getTemplateMeta();
        $meta['general']['id'] = $related->id;
        $meta['general']['type'] = $related->getDescriptor()->type;
        $meta['general']['state'] = $related->state;
        $meta['selectors'] = array(
            'widget'  => '[data-id=' . $this->id . ']',
            'wrapper' => '#widget-wrapper-' . $related->id,
            'loading' => '#widget-loading-' . $related->id,
            'refresh' => '#widget-refresh-' . $related->id,
        );
        if ($related instanceof HistogramWidget) {
            $meta['urls']['statUrl'] = route('widget.singlestat', $related->id);
            $meta['selectors']['graph'] = '[id^=chart-container]';
            $meta['layout'] = $related->getSettings()['type'];
        }
        $meta['data']['init'] = 'widgetData' . $related->id;

        return $meta;
    }

}
?>
