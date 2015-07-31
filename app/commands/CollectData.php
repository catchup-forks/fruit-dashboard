<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CollectData extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'widgets:collect_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Running data collection on all widget
    \'s implementing the CronWidget interface.';

    /**
     * Execute the console command.
     *
     * @return none
     */
    public function fire()
    {
        /* Iterating through the widgets. */
        foreach ($this->getWidgets() as $widget) {
            /* Running data collection. */
            $widget->collectData();
        }
    }

    /**
     * Getting the widgets.
     *
     * @return The filtered widgets.
     */
    public function getWidgets()
    {
        $widgets = array();

        /* Iterating through the widgets. */
        foreach (Widget::all() as $generalWidget) {

            /* Getting the specific instance. */
            $widget = $generalWidget->getSpecific();

            /* Filtering to cron widgets. */
            if ($widget instanceof iCronWidget) {
                array_push($widgets, $generalWidget->getSpecific());
            }
        }

        return $widgets;
    }
}
