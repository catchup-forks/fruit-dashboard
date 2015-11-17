<?php

class Dashboard extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'name',
        'background',
        'number',
        'is_locked',
        'is_default'
    );
    public $timestamps = false;

    // -- Relations -- //
    public function widgets() {return $this->hasMany('Widget');}
    public function user() { return $this->belongsTo('User'); }

    /**
     * getNextAvailablePosition
     * Return the next available position to the dashboard.
     * --------------------------------------------------
     * @param $desiredX The desired cols.
     * @param $desiredY The desired rows.
     * @param $widget The editable widget.
     * @return array of the x,y position.
     * --------------------------------------------------
    */
    public function getNextAvailablePosition($desiredX, $desiredY, $widget=null) {
        /* Iterating through the grid to find a fit. */
        for ($i = 1; $i <= SiteConstants::getGridNumberOfRows(); ++$i) {
            for ($j = 1; $j <= SiteConstants::getGridNumberOfCols(); ++$j) {
                /* Defining rectangle. */
                $rectangle = array(
                    'startX' => $j,
                    'startY' => $i,
                    'endX'   => $j + $desiredX,
                    'endY'   => $i + $desiredY
                );
                /* Respecting the grid size */
                if ( ! $this->inGrid($rectangle)) {
                    continue;
                }
                if ($this->fits($rectangle, $widget)) {
                    return '{"size_x":' . $desiredX . ',"size_y":' . $desiredY. ',"col":' . $j . ', "row": '. $i .'}';
                }
            }
        }
        /* No match, default positioning. */
        return '{"size_x":' . $desiredX . ',"size_y":' . $desiredY. ',"col": 11,"row": 11}';
    }

    /**
     * fits
     * Determines, whether or not, the widget fits into the position.
     * --------------------------------------------------
     * @param $rectangle Array
     * @param $skipWidget The widget to avoid conflicts with.
     * @return bool
     * --------------------------------------------------
    */
    private function fits($rectangle, $skipWidget=null) {
        /* Looking for an overlap. */
        foreach ($this->widgets as $widget) {

            if ($widget->state == 'hidden') {
                continue;
            }
            if ( ! is_null($skipWidget) && ($widget->id == $skipWidget->id)) {
                continue;
            }

            $pos = $widget->getPosition();
            $xEnd = $pos->col + $pos->size_x;
            $yEnd = $pos->row + $pos->size_y;

            $x1Overlap = ($pos->col < $rectangle['endX']);
            $x2Overlap = (($xEnd) > $rectangle['startX']);
            $y1Overlap = ($pos->row < $rectangle['endY']);
            $y2Overlap = (($yEnd) > $rectangle['startY']);

            if ($x1Overlap && $x2Overlap && $y1Overlap && $y2Overlap) {
                return false;
            }
        }

        return true;
    }

    public function save(array $options=array()) {
        /* Notify user about the change */
        $this->user->updateDashboardCache();
        return parent::save($options);
    }

    /**
     * inGrid
     * Determines, if the rectangle is in the grid.
     * --------------------------------------------------
     * @param $rectangle Array
     * @return bool
     * --------------------------------------------------
    */
    private function inGrid($rectangle) {
        if ($rectangle['endX'] > SiteConstants::getGridNumberOfCols()) {
            return false;
        }
        if ($rectangle['endY'] > SiteConstants::getGridNumberOfRows()) {
            return false;
        }
        return true;
    }

    /**
     * Overriding delete to update the user's cache.
    */
    public function delete() {
        /* Notify user about the change */
        $this->user->updateDashboardCache();
        $this->widgets()->delete();
        parent::delete();
    }
}

?>
