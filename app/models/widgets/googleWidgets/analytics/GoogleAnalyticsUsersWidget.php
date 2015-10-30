<?php
class GoogleAnalyticsUsersChartWidget extends Widget implements iServiceWidget
{
    use ChartWidgetTrait;
    use HistogramWidgetTrait;
    use GoogleAnalyticsWidgetTrait;

    protected function getDataTypes()
    {
        return array('new_users', 'users');
    }

    public function getTemplateData()
    {
        $chart = $this->buildChart();

        return array(
            'data'          => array(
                'dataSets'   => $chart->getDataSets(),
                'labels'     => $chart->getLabels(),
                'isCombined' => $chart->getIsCombined(),
            ),
            'currentDiff'   => $this->getDiff(),
            'currentValue'  => $this->getLatestValues(),
            'hasCumulative' => true/false
        );
    }

    private function buildChart()
    {
        /* Building the histograms. */
        $newUsers = $this->buildHistogram($this->data['new_users']);
        $users    = $this->buildHistogram($this->data['users']);

        foreach ($newUsers as $entry) {
        }
    }
}
?>
