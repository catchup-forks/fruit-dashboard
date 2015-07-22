<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SendDailyEmail extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'metrics:send';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Sends email to all the users with their previous days metrics.';

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
		// get all the users
		$users = User::all();
		
		$previousDayDate = Carbon::yesterday()->toDateString();
		// currently calculated metrics
		$currentMetrics = Calculator::currentMetrics();
		// for each user send email with their metrics

		$dailyEmailSent = 0;
		$weeklyEmailSent = 0;
		foreach ($users as $user) {
			// check if user finished the connect process
			if ($user->isConnected() && $user->ready == 'connected' && !$user->isTrialEnded())
			{
				switch ($user->summaryEmailFrequency) {

					case 'none': // no summary email
						break;

					case 'daily': // default behavior, send yesterday's data
						// get the user's metrics
						$metric = Metric::where('user', $user->id)	
										->where('date', $previousDayDate)
										->first();

						if ($metric)
						{
							$previousMetrics = Metric::where('user', $user->id)
					                        ->where('date','<=', Carbon::yesterday()->subDays(30)->toDateString())
					                        ->orderBy('date','desc')
					                        ->first();
					        $changes = array();
					        foreach ($currentMetrics as $metricID => $metricDetails) {
					            // get the correct color
					            $changes[$metricID]['positiveIsGood'] = $metricDetails['metricClass']::POSITIVE_IS_GOOD;
				                $date = $metric->date;

					            if ($previousMetrics) {
					                if($previousMetrics->$metricID != 0)
					                {
					                    $value = ($metric->$metricID / $previousMetrics->$metricID) * 100 - 100;
					                    $changes[$metricID][$date]['isBigger'] = $value > 0 ? true : false;
					                    $changes[$metricID][$date]['value'] = round($value).' %';
					                }
					                else
					                    $changes[$metricID][$date]['value'] = null;
					            } else {
					            	$changes[$metricID][$date]['value'] = null;
					            } 
					        }
							// format metrics to presentable data
							$metric->formatMetrics();
							// this line is for making the daily email the same format as the weekly
							// so we only need one email template
							$metrics = array($metric->date => $metric);
							$data = array(
								'metrics' => $metrics,
			                    'currentMetrics' => $currentMetrics,
			                    'changes' => $changes,
			                    'isDaily' => true,
			                    'index'	  => 0,		// for striping
								);

							
							// $email = Mailman::make('emails.notification.summary')
							// 	->with($data)
							// 	->to($user->email)
							// 	->subject('Daily summary')
							// 	//->show();
							// 	->send();

							//File::put(public_path().'/emails/summary_email.html',$email);

							$dailyEmailSent++;
						}
						break;

					case 'weekly': // send a weekly summary to the user with their numbers
						// check if today is monday (we send weekly emails on monday)
						/* improvment idea
							change this if to switch-case with days
							for user controlled daily send
						*/
						if(Carbon::now()->dayOfWeek == Carbon::WEDNESDAY)
						{
							// get the user's metrics
							$metrics = Metric::where('user', $user->id)	
											->where('date','<=', $previousDayDate)
											->orderBy('date','desc')
											->take(7)
											->get();
							$previousMetrics = Metric::where('user', $user->id)
					                        ->where('date','<=', Carbon::yesterday()->subDays(30)->toDateString())
					                        ->orderBy('date','desc')
					                        ->take(7)
					                        ->get();
					        $changes = array();
					        foreach ($currentMetrics as $metricID => $metricDetails) {
					            // get the correct color
					            $changes[$metricID]['positiveIsGood'] = $metricDetails['metricClass']::POSITIVE_IS_GOOD;

					            foreach ($previousMetrics as $id => $prevMetric) {
					                $date = $metrics[$id]->date;
					                if($prevMetric->$metricID != 0)
					                {
					                    $value = ($metrics[$id]->$metricID / $prevMetric->$metricID) * 100 - 100;
					                    $changes[$metricID][$date]['isBigger'] = $value > 0 ? true : false;
					                    $changes[$metricID][$date]['value'] = round($value).' %';
					                }
					                else
					                    $changes[$metricID][$date]['value'] = null;
					            }
					        }

							// format metrics to presentable data
							$weeklyMetrics = array();
							foreach ($metrics as $metric) {
								$metric->formatMetrics();
								$weeklyMetrics[$metric->date] = $metric;
							}
							$data = array(
								'metrics' => $weeklyMetrics,
								'currentMetrics' => $currentMetrics,
								'changes' => $changes,
								'isDaily' => false,
								'index'	  => 0,
								);
							// login the user (necessary to get the email address)	
							// Auth::login($user);


							// send the email to the user
							// $email = Mailman::make('emails.notification.summary')
							// 	->with($data)
							// 	->to($user->email)
							// 	->subject('Weekly summary')
							// 	//->show();
							// 	->send();

							//File::put(public_path().'/emails/summary_email.html',$email);
								
							$weeklyEmailSent++;
						}
						break;
					default:
						Log::error('notifications string has been changed, check the email sending code');
						break;
				} // /switch
			} // /isConnected
		} // /foreach
		Log::info($dailyEmailSent.' daily summary emails sent out of '.count($users).' users');
		Log::info($weeklyEmailSent.' weekly summary emails sent out of '.count($users).' users');
		Log::info('Total of '. $dailyEmailSent + $weeklyEmailSent .' emails sent');
	}
}
