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
        /* Iterating through the managers. */
        foreach (DataManager::all() as $manager) {
            if (Carbon::now()->diffInMinutes($manager->last_updated) >= $manager->update_period) {
                try {
                    $manager->getSpecific()->collectData();
                } catch (Exception $e) {
                    Log::error($e);
                }
            }
        }
    }
}
