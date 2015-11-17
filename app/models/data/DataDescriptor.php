<?php

class DataDescriptor extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'category',
        'type',
        'attributes'
    );
    public $timestamps = false;

    // -- Relations -- //
    public function dataObjects() {return $this->hasMany('Data', 'descriptor_id');}

    /**
     * getCollectorClassName
     * Return the specific collector class Name
     * --------------------------------------------------
     * @return string The collector class Name
     * --------------------------------------------------
    */
    public function getCollectorClassName()
    {
        $className = Utilities::underscoreToCamelCase($this->type) . 'DataCollector';
        if ( ! class_exists($className)) {
            $className = Utilities::underscoreToCamelCase($this->category . '_') . $className;
        }
        return $className;
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
    public static function find($id, $columns=array('*'))
    {
        return DataDescriptor::rememberForever()->where('id', $id)->first();
    }

    /**
     * getAttributes
     * Return the descriptor attributes.
     * --------------------------------------------------
     * @return array
     * --------------------------------------------------
    */
    public function getAttributes()
    {
        /* I <3 you Eloquent. */
        return json_decode($this->attributes['attributes'], true);
    }

}
?>
