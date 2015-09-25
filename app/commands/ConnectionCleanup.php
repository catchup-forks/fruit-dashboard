<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ConnectionCleanup extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'connections:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command deletes all connections that are broken for some reason (e.g. no Google refresh token is stored).';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        foreach (Connection::all() as $connection) {
            $user = $connection->user;
            if ($connection->service == 'google_analytics' && $connection->refresh_token == '') {
                $connector = new GoogleAnalyticsConnector($user);
                $connector->disconnect();
                Log::info('user #' . $user->id . ' has been disconnected from google analytics due to a broken connection');
            }
        }
    }
}
