<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateEmailConnect extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'connect:make';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate "connect" email for development purposes';

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
// 		$email = Mailman::make('emails.notification.connected')
//         	->to('rashan86@gmail.com')
//         	->subject('Your metrics are ready!')
//         	->show();
// //        	->send();

//         File::put(public_path().'/emails/connected_email.html',$email);
	}

}
