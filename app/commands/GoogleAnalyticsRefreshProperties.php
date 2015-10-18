<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GoogleAnalyticsRefreshProperties extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'google_analytics:refresh_properties';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshing all user\'s google analytics properties.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        foreach (User::all() as $user) {
            if ( ! $user->isServiceConnected('google_analytics')) {
                /* GA not connected. */
                continue;
            }
            try {
                $collector = new GoogleAnalyticsDataCollector($user);
                $collector->saveProperties();
                Log::info("Successfully updated GA properties of user #" . $user->id);
            } catch (Exception $e) {
                Log::error('Error found while running ' . get_class($this) . ' on user #' . $user->id . '. message: ' . $e->getMessage());
            }
        }
    }

}
