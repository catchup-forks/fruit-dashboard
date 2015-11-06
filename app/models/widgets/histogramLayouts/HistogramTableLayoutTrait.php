<?php

trait HistogramTableLayoutTrait
{
    /* Dummies */
    protected function getTableTemplateMeta() {return array();}
    protected function getTableTemplateData() {return array();}

    /**
     * getTableData
     * Returns the data in table format.
     * --------------------------------------------------
     * @param array $options
     * @return array
     * --------------------------------------------------
     */
    protected function getTableData(array $options)
    {
        $settings = $this->getSettings();
        $dateHeader = rtrim(ucwords($this->getResolution()), 's');

        /* Initializing table. */
        $tableData = array(
            'header' => array(
                 $dateHeader,
                 $this->getDescriptor()->name,
                 'Trend'
            ),
            'content' => array(
            )
        );

        /* Populating table data. */
        for ($i = $this->getLength(); $i > 0; --$i) {
            $diff = $i - 1;
            $now = Carbon::now();
            switch ($settings['resolution']) {
                case 'days':   $date = $now->subDays($diff)->format('M-d'); break;
                case 'weeks':  $date = $now->subWeeks($diff)->format('W'); break;
                case 'months': $date = $now->subMonths($diff)->format('M'); break;
                case 'years':  $date = $now->subYears($diff)->format('Y'); break;
                default:$date = '';
            }

            /* Calculating data. */
            $history = $this->getHistory($i);
            $value = $history['value'];

            if (isset($previousValue) && $previousValue != 0) {
                $percent = ($value / $previousValue - 1) * 100;
            } else {
                $percent = 0;
            }

            /* Create format for percent. */
            $success = static::isSuccess($percent);
            $trendFormat = '<div class="';
            if ($success) { $trendFormat .= 'text-success';
            } else { $trendFormat .= 'text-danger'; }
            $trendFormat .= '"> <span class="fa fa-arrow-';
            if ($percent >= 0) { $trendFormat .= 'up';
            } else { $trendFormat .= 'down'; }
            $trendFormat .= '"> %.2f%%</div>';

            array_push($tableData['content'], array(
                $date,
                Utilities::formatNumber($history['value'], $this->getFormat()),
                Utilities::formatNumber($percent, $trendFormat)
            ));

            /* Saving previous value. */
            $previousValue = $value;
        }
        $tableData['content'] = array_reverse($tableData['content']);
        return $tableData;
    }
}
