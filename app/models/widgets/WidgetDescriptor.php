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

    /**
     * getClassName
     * Returning the specific widgetClass Name
     * --------------------------------------------------
     * @return string The widget class Name
     * --------------------------------------------------
    */
    public function getClassName() {
        return Utilities::underscoreToCamelCase($this->type) . 'Widget';
    }

    /**
     * find
     * Returning the model based on id.
     * --------------------------------------------------
     * @param int $id
     * @param array $columns
     * @return WidgetDescriptor
     * --------------------------------------------------
    */
    public static function find($id, $columns=array('*')) {
        /* Trying to load from cache. */
        $cacheKey = 'descriptor_' . $id;
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        /* Not found in cache, storing and returning the object from DB. */
        $descriptor = parent::find($id, $columns);
        Cache::put($cacheKey, $descriptor);
        return $descriptor;
    }

    /**
     * getDataManager
     * Returning the specific DataManager class Name
     * --------------------------------------------------
     * @return string The widget class Name
     * --------------------------------------------------
    */
    public function getDMClassName() {
        return str_replace('Widget', 'DataManager', $this->getClassName());
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
     * @param bool firstRun
     * @return DataManager
     * --------------------------------------------------
    */
    public function getDataManager($widget, $firstRun=TRUE) {
        $className = $this->getDMClassName();
        if (class_exists($className) && $widget->hasValidCriteria()) {
            /* Manager class exists, and widget has been set up */
            $managers = $widget->user()->dataManagers()->where('descriptor_id', $this->id)->get();

            foreach ($managers as $manager) {
                if ($manager->getCriteria() == $widget->getCriteria()) {
                    return $manager;
                }
            }

            /* No manager found */
            if ($widget instanceof iServiceWidget && $firstRun) {
                /* It's a service widget. */
                /* Creating all related managers. */
                $connectorClass = $widget->getConnectorClass();
                $connector = new $connectorClass($widget->user());
                $connector->createDataManagers($widget->getCriteria());

                /* Calling getManager again, there should be a match now. */
                return $this->getDataManager($widget, FALSE);
            } else {
                /* Creating a manager. */
                return DataManager::createManagerFromWidget($widget);
            }

       }
        return null;
    }

}
?>