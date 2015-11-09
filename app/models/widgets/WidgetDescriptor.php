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
    public $timestamps = false;

    // -- Relations -- //
    public function widgets() {return $this->hasMany('Widget', 'descriptor_id');}
    public function dataObjects() {return $this->hasMany('Data', 'descriptor_id');}

    /**
     * getClassName
     * Return the specific widgetClass Name
     * --------------------------------------------------
     * @return string The widget class Name
     * --------------------------------------------------
    */
    public function getClassName() {
        return Utilities::underscoreToCamelCase($this->type) . 'Widget';
    }

    /**
     * find
     * Return the model based on id.
     * --------------------------------------------------
     * @param int $id
     * @param array $columns
     * @return WidgetDescriptor
     * --------------------------------------------------
    */
    public static function find($id, $columns=array('*')) {
        /* Trying to load from cache. */
        return WidgetDescriptor::rememberForever()->where('id', $id)->first();
    }

    /**
     * getDMClassName
     * Return the specific DataManager class Name
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
     * Return the location of the blade template.
     * @return string
     * --------------------------------------------------
    */
    public function getTemplateName() {
        return 'widget.' . $this->category . '.widget-' . $this->type;
    }

    /**
     * getPhotoLocation
     * Return the url of the demonstration photo.
     * --------------------------------------------------
     * @return string
     * --------------------------------------------------
     */
    public function getPhotoLocation() {
        return 'img/demonstration/widget-' . $this->type. '.png';
    }

}
?>
