<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateEmails extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'email:make';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Helper command to generate the HTML emails for development purposes. You should rewrite the command for every new email.';

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
		// send email
		$user = User::find(1);
		$data = array('user' => $user);

		// $email = Mailman::make('emails.trial.trialWillEnd')
		// 		->with($data)
		// 		->to('rashan86@gmail.com')
		// 		->subject('[Fruit Analytics] Your free trial is ending.')
		// 		->show();
		// 		//->send();

		File::put(public_path().'/emails/development_email.html',$email);
	}
}