<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MigrateExternal extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'migrate:external';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates all needed external package tables';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
       $this->call('migrate', array('--package' => 'barryvdh/laravel-async-queue'));
    }
}