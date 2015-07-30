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
     * The widgets on which data collection will be run.
     *
     * @var string
     */
    protected $widgets = array();

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Running data collection on all widget
    \'s implementing the CronWidget interface.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->getWidgets();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        /* Iterating through the widgets. */
        foreach ($this->widgets as $widget) {
            /* Running data collection. */
            $widget->collectData();
        }
    }

    /**
     * Getting the widgets.
     *
     * @return none
     */
    public function getWidgets()
    {

        /* Iterating through the widgets. */
        foreach (Widget::all() as $generalWidget) {

            /* Getting the specific instance. */
            $widget = $generalWidget->getSpecific();

            /* Filtering to cron widgets. */
            if ($widget instanceof iCronWidget) {
                array_push($this->widgets, $generalWidget->getSpecific());
            }
        }
    }
}
