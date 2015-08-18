<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class BackgroundChange extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'background:change';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Changes the background for all users to the next available.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        foreach (User::all() as $user) {
            if ($user->background != null) {
                /* Change the background url */
                $user->background->changeUrl();
                
            } else {
                /* Create default background for the user */
                $background = new Background;
                $background->user()->associate($user);
                $background->changeUrl();

                /* Save background */
                $background->save();
            }
        }
    }
}
