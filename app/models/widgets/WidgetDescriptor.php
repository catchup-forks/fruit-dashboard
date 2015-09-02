<?php

class WidgetDescriptor extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'name',
        'description',
        'type',
        'is_premium',
        'category',
        'min_cols', 'min_rows',
        'default_cols', 'default_rows'
    );
    public $timestamps = FALSE;

    // -- Relations -- //
    public function widgets() {return $this->hasMany('Widget', 'descriptor_id');}
    public function dataManagers() {return $this->hasMany('DataManager', 'descriptor_id');}

    /* Returning the specific widgetClass Name
     *
     * @return string The widget class Name
    */
    public function getClassName() {
        return str_replace(
            ' ', '',
            ucwords(str_replace('_',' ', $this->type))
        ) . "Widget";
    }

    /**
     * getTemplateName
     * --------------------------------------------------
     * Returning the location of the blade template.
     * @return string
     * --------------------------------------------------
    */
    public function getTemplateName() {
        return 'widget.' . $this->category . '.widget-' . $this->type;
    }

    /**
     * getDataManager
     * Returning the corresponding DataManager object.
     * --------------------------------------------------
     * @param Widget $widget
     * @return DataManager
     * --------------------------------------------------
    */
    public function getDataManager($widget) {
        if (class_exists(str_replace('Widget', 'DataManager', $this->getClassName()))) {
            /* Manager class exists. */
            $managers = $widget->user()->dataManagers()->where('descriptor_id', $this->id)->get();

            if (count($managers) > 1) {
                /* More managers, checking criteria. */
                foreach ($managers as $manager) {
                    if ($manger->getCriteria() == $widget->getCriteria()) {
                        return $manager;
                    }
                }
            } else if (count($managers) == 1) {
                /* Only one manager using it automatically. */
                return $managers[0];
            }
        }
        return null;
    }
}
?>