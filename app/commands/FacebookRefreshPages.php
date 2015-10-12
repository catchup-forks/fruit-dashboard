<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FacebookRefreshPages extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'facebook:refresh_pages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshing all user\'s facebook pages.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        foreach (User::all() as $user) {
            if ( ! $user->isServiceConnected('facebook')) {
                /* Facebook not connected. */
                continue;
            }
            try {
                $collector = new FacebookDataCollector($user);
                $collector->savePages();
                Log::info("Successfully updated FB pages for user #" . $user->id);
            } catch (Exception $e) {
                Log::error('Error found while running ' . get_class($this) . ' on user #' . $user->id . '. message: ' . $e->getMessage());
            }
        }
    }

}
