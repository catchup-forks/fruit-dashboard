<?php

class Dashboard extends Eloquent
{
    // -- Fields -- //
    protected $fillable = array(
        'name',
        'background',
        'number'
    );
    public $timestamps = FALSE;

    // -- Relations -- //
    public function widgets() {return $this->hasMany('Widget');}
    public function user() { return $this->belongsTo('User'); }

    /**
     * getNextAvailablePosition
     * Returning the next available position to the dashboard.
     * --------------------------------------------------
     * @param $desiredX The desired cols.
     * @param $desiredY The desired rows.
     * @return array of the x,y position.
     * --------------------------------------------------
    */
    public function getNextAvailablePosition($desiredX, $desiredY) {
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
                if ($this->fits($rectangle)) {
                    return '{"size_x": ' . $desiredX . ', "size_y": ' . $desiredY. ', "col": ' . $j . ', "row": ' . $i . '}';
                }
            }
        }
        /* No match, default positioning. */
        return '{"size_x": ' . $desiredX . ', "size_y": ' . $desiredY. ', "col": 0 , "row": 0 }';
    }

    /**
     * fits
     * Determines, whether or not, the widget fits into the position.
     * --------------------------------------------------
     * @param $rectangle Array of the widget's desired position.
     * @return array of the x,y position.
     * --------------------------------------------------
    */
    private function fits($rectangle) {

        Log::info($rectangle);

        /* Looking for an overlap. */
        foreach ($this->widgets as $widget) {

            if ($widget->state == 'hidden') {
                continue;
            }

            $pos = $widget->getPosition();

            $x1Overlap = ($pos->col < $rectangle['endX']);
            $x2Overlap = (($pos->col + $pos->size_x) > $rectangle['startX']);
            $y1Overlap = ($pos->row < $rectangle['endY']);
            $y2Overlap = (($pos->row + $pos->size_y) > $rectangle['startY']);

            if ($x1Overlap && $x2Overlap && $y1Overlap && $y2Overlap) {
                return FALSE;
            }
        }

        return TRUE;
    }

}

?>
