<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SaveWidgets extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'widgets:save';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calling save on all widgets to keep integrity.';

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
        foreach (Widget::all() as $widget) {
            try {
                $widget->save();
            } catch (DescriptorDoesNotExist $e) {
                /* Deleting widget if the descriptor does not exist. */
                $widget->delete();
            }
        }
    }
}
