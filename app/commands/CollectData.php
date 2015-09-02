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
        foreach (DataCollector::all() as $collector) {
            $collector->getSpecficic()->collectData();
        }
    }
}
