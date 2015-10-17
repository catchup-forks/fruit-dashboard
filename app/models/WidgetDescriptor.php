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
    public function dataObjects() {return $this->hasMany('Data', 'descriptor_id');}

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
        return  WidgetDescriptor::rememberForever()->where('id', $id)->first();
    }

    /**
     * getDMClassName
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
     * getDataObject
     * Returning the corresponding Data object.
     * --------------------------------------------------
     * @param Widget $widget
     * @param bool firstRun
     * @return Data
     * --------------------------------------------------
    */
    public function getDataObject($widget, $firstRun=TRUE) {
        /* Manager class exists, and widget has been set up */
        $data = $widget->user()->dataObjects()
            ->where('descriptor_id', $this->id)->get();

        foreach ($data as $iData) {
            if ($iData->getCriteria() == $widget->getCriteria()) {
                return $iData;
            }
        }

        /* No data found */
        if ($widget instanceof iServiceWidget && $firstRun) {
            /* It's a service widget. */
            /* Creating all related data. */
            $connectorClass = $widget->getConnectorClass();
            $connector = new $connectorClass($widget->user());
            $connector->createDataObjects($widget->getCriteria());

            /* Calling getDataObject again, there should be a match now. */
            return $this->getDataObject($widget, FALSE);
        } else {
            /* Creating a manager. */
            return Data::createFromWidget($widget);
        }
    }

    /**
     * getPhotoLocation
     * Returning the url of the demonstration photo.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
     */
    public function getPhotoLocation() {
        return 'img/demonstration/widget-' . $this->type. '.png';
    }

}
?>