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
        Log::info("Data collection started at " . Carbon::now()->toDateTimeString());
        $time = microtime(TRUE);
        $errors = 0;
        $i = 0;
        foreach (DataManager::all() as $manager) {
            if (Carbon::now()->diffInMinutes($manager->last_updated) >= $manager->update_period) {
                if ($manager->id != 1131) {
                    continue;
                }
                $i++;
                try {
                    $manager->getSpecific()->collectData();
                } catch (Exception $e) {
                    $errors++;
                    Log::error('Error found while collecting data on manager #' . $manager->id . '. message: ' . $e->getMessage());
                }
            }
        }
        Log::info('Data collection finished with ' . $errors . ' errors (of ' . $i . ') It took ' . ( microtime(TRUE) - $time) . ' seconds to run.');
    }
}
