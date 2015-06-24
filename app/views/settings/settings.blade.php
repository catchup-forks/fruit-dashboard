@extends('meta.base-user')

	@section('pageTitle')
		Settings
	@stop

	@section('pageContent')

		<div id="content-wrapper">
			@parent

			<!-- Account settings -->
			<div class="col-md-10 col-md-offset-1">
				<div class="row">
					<div class="col-sm-6 col-sm-offset-3 account-form-wrapper">
					<div class="panel-body account-form bordered getHeight">
						<h4><i class="fa fa-cog"></i>&nbsp;&nbsp;Account settings</h4>
						<!-- Name -->
						{{ Form::open(array(
							'action' => 'SettingsController@doSettingsName',
							'id' => 'form-settings-name',
							'role' => 'form',
							'class' => 'form-horizontal' )) }}

								<div class="form-group"  id="editNameForm">
									{{ Form::label('id_nameedit', 'Username', array(
									'class' => 'col-sm-4 control-label')) }}
									<div class="col-sm-8">
										<p class="form-control-static">
											@if(Auth::user()->name)
											<span>{{ Auth::user()->name }}</span>
											@else 
											N/A
											@endif
											<button id="editName" class="btn btn-flat btn-info btn-sm pull-right" type="button" onClick= '_gaq.push(["_trackEvent", "Edit", "Editing name"]);mixpanel.track("Editing name");'>Edit</button>
										</p>
									</div>
								</div> <!-- / .form-group -->

								<!-- hidden name change form -->

								<div id="changeNameForm" class="hidden-form">

									<div class="form-group @if ($errors->first('name')) has-error @endif">
										{{ Form::label('id_name', 'New username', array(
										'class' => 'col-sm-4 control-label')) }}
										<div class="col-sm-8">
											{{ Form::text('name', Auth::user()->name, array(
											'id' => 'id_name',
											'class' => 'form-control')) }}
										</div>
									</div> <!-- / .form-group -->

									<div class="col-sm-8 col-sm-offset-4 text-center padding-xs-vr">
										<button class="btn btn-warning btn-sm btn-flat" type="button" id="cancelName">Cancel</button>
										{{ Form::submit('Save', array(
										'id' => 'id_submit',
										'class' => 'btn btn-primary btn-sm btn-flat',
										'onClick'=> '_gaq.push(["_trackEvent", "Edit", "Name edited"]);mixpanel.track("Name edited");')) }}
									</div>

								</div>

								<!-- / hidden name change form -->


								{{ Form::close() }}

								<!-- Country -->
								{{ Form::open(array(
								'action' => 'SettingsController@doSettingsCountry',
								'id' => 'form-settings-country',
								'role' => 'form',
								'class' => 'form-horizontal' )) }}

								<div class="form-group" id="editCountryForm">
									{{ Form::label('id_countryedit', 'Country', array(
									'class' => 'col-sm-4 control-label')) }}
									<div class="col-sm-8">
										<p class="form-control-static">
											@if(Auth::user()->zoneinfo)
											<span>{{ Auth::user()->zoneinfo }}</span>
											@else 
											N/A
											@endif
											<button id="editCountry" class="btn btn-flat btn-info btn-sm pull-right" type="button" onClick= '_gaq.push(["_trackEvent", "Edit", "Editing country"]);mixpanel.track("Editing country");'>Edit</button>
										</p>
									</div>
								</div> <!-- / .form-group -->

								<!-- hidden country change form -->

								<div id="changeCountryForm" class="hidden-form">

									<div class="form-group @if ($errors->first('country')) has-error @endif">
										{{ Form::label('id_country', 'New country', array(
										'class' => 'col-sm-4 control-label')) }}
										<div class="col-sm-8">
											{{ Form::text('country', Auth::user()->zoneinfo, array(
											'id' => 'id_country',
											'class' => 'form-control')) }}
										</div>
									</div> <!-- / .form-group -->

									<div class="col-sm-8 col-sm-offset-4 text-center padding-xs-vr">
										<button class="btn btn-warning btn-sm btn-flat" type="button" id="cancelCountry">Cancel</button> 
										{{ Form::submit('Save', array(
										'id' => 'id_submit',
										'class' => 'btn btn-primary btn-sm btn-flat',
										'onClick'=> '_gaq.push(["_trackEvent", "Edit", "Country edited"]);mixpanel.track("Country edited");')) }} 
									</div>

								</div>

								<!-- / hidden name change form -->

								{{ Form::close() }}

								<!-- Email -->
								{{ Form::open(array(
								'action' => 'SettingsController@doSettingsEmail',
								'id' => 'form-settings-email',
								'role' => 'form',
								'class' => 'form-horizontal' )) }}

								<div class="form-group" id="editEmailForm">
									{{ Form::label('id_emailedit', 'Email', array(
									'class' => 'col-sm-4 control-label')) }}
									<div class="col-sm-8">
										<p class="form-control-static">
											<span>{{ Auth::user()->email }}</span>
											<button id="editEmail" class="btn btn-flat btn-info btn-sm pull-right" type="button" onClick= '_gaq.push(["_trackEvent", "Edit", "Editing email"]);mixpanel.track("Editing email");'>Edit</button>
										</p>
									</div>
								</div> <!-- / .form-group -->

								<!-- hidden email change form -->

								<div id="changeEmailForm" class="hidden-form">

									<div class="form-group @if ($errors->first('email')) has-error @endif">
										{{ Form::label('id_email', 'New email', array(
										'class' => 'col-sm-4 control-label')) }}
										<div class="col-sm-8">
											{{ Form::text('email', '', array(
											'id' => 'id_email',
											'class' => 'form-control')) }}
										</div>
									</div> <!-- / .form-group -->

									<div class="form-group @if ($errors->first('email_password')) has-error @endif">
										{{ Form::label('id_email_password', 'Your password', array(
										'class' => 'col-sm-4 control-label')) }}
										<div class="col-sm-8">
											{{ Form::password('email_password', array(
											'id' => 'id_email_password',
											'class' => 'form-control')) }}
										</div>
									</div> <!-- / .form-group -->

									<div class="col-sm-8 col-sm-offset-4 text-center padding-xs-vr">
										<button class="btn btn-warning btn-sm btn-flat" type="button" id="cancelEmail">Cancel</button>  
										{{ Form::submit('Save', array(
										'id' => 'id_submit',
										'class' => 'btn btn-primary btn-sm btn-flat',
										'onClick'=> '_gaq.push(["_trackEvent", "Edit", "Email edited"]);mixpanel.track("Email edited");')) }}
									</div>

								</div>

								{{ Form::close() }}

								<!-- / hidden email change form -->

								<!-- Password -->
								{{ Form::open(array(
								'action' => 'SettingsController@doSettingsPassword',
								'id' => 'form-settings-password',
								'role' => 'form',
								'class' => 'form-horizontal' )) }}

								<div id="editPasswordForm">
									<div class="form-group">
										{{ Form::label('id_passwordedit', 'Password', array(
										'class' => 'col-sm-4 control-label')) }}
										<div class="col-sm-8">
											<p class="form-control-static">
												<span>********</span>
												<button id="editPassword" class="btn btn-flat btn-info btn-sm pull-right" type="button" onClick= '_gaq.push(["_trackEvent", "Edit", "Editing password"]);mixpanel.track("Editing password");'>Edit</button>
											</p>
										</div>
									</div> <!-- / .form-group -->
								</div>

								<!-- hidden password change form -->

								<div id="changePasswordForm" class="hidden-form">
									<div class="form-group @if ($errors->first('old_password')) has-error @endif">
										{{ Form::label('id_old_password', 'Old password', array(
										'class' => 'col-sm-4 control-label')) }}
										<div class="col-sm-8">
											{{ Form::password('old_password', array(
											'id' => 'id_old_password',
											'class' => 'form-control')) }}
										</div>
									</div> <!-- / .form-group -->

									<div class="form-group @if ($errors->first('new_password')) has-error @endif">
										{{ Form::label('id_new_password', 'New password', array(
										'class' => 'col-sm-4 control-label')) }}
										<div class="col-sm-8">
											{{ Form::password('new_password', array(
											'id' => 'id_new_password',
											'class' => 'form-control')) }}
										</div>
									</div> <!-- / .form-group -->

									<div class="form-group @if ($errors->first('new_password_confirmation')) has-error @endif">
										{{ Form::label('id_new_password_confirmation', 'New password again', array(
										'class' => 'col-sm-4 control-label')) }}
										<div class="col-sm-8">
											{{ Form::password('new_password_confirmation', array(
											'id' => 'id_new_password_confirmation',
											'class' => 'form-control')) }}
										</div>
									</div> <!-- / .form-group -->

									<div class="col-sm-8 col-sm-offset-4 text-center padding-xs-vr">
										<button class="btn btn-warning btn-sm btn-flat" type="button" id="cancelPassword">Cancel</button>  
										{{ Form::submit('Save', array(
										'id' => 'id_submit',
										'class' => 'btn btn-primary btn-sm btn-flat',
										'onClick'=> '_gaq.push(["_trackEvent", "Edit", "Password edited"]);mixpanel.track("Password edited");')) }}
									</div>

								</div>
								{{ Form::close() }}
							</div>
						</div> <!-- / .panel-body -->
					</div> <!-- / .col-sm-6 -->
				</div> <!-- /. col-md-10 -->
				<!-- /Account settings -->


				<!-- Notification settings -->
				<div class="col-md-10 col-md-offset-1">
					<div class="row">
						<div class="col-sm-6 col-sm-offset-3 notification-form-wrapper">
							<div class="panel-body account-form bordered getHeight">
								<h4><i class="fa fa-cog"></i>&nbsp;&nbsp;Notification settings</h4>

								<!-- Summary Email Frequency -->
								<!-- choose from dropdown -->
								{{ Form::open(array(
								'action' => 'SettingsController@doSettingsFrequency',
								'id' => 'form-settings-frequency',
								'role' => 'form',
								'class' => 'form-horizontal' )) }}

								<div id="editFrequencyForm">
									<div class="form-group">
										{{ Form::label('id_frequencyedit', 'Notifications', array(
										'class' => 'col-sm-4 control-label')) }}
										<div class="col-sm-8">
											<p class="form-control-static">
												@if (Auth::user()->summaryEmailFrequency == 'none')
												<span>No email</span>
												@elseif (Auth::user()->summaryEmailFrequency == 'daily')
												<span>Daily email</span>
												@else
												<span>Weekly email</span>
												@endif
												<button id="editFrequency" class="btn btn-flat btn-info btn-sm pull-right" type="button" onClick= '_gaq.push(["_trackEvent", "Edit", "Editing frequency"]);mixpanel.track("Editing frequency");'>Edit</button>
											</p>
										</div>
									</div> <!-- / .form-group -->
								</div>

								<!-- hidden notification change form -->

								<div id="changeFrequencyForm" class="hidden-form">
									<div class="form-group @if ($errors->first('new_frequency')) has-error @endif">
										{{ Form::label('id_frequency', 'Notifications', array(
										'class' => 'col-sm-4 control-label')) }}
										<div class="col-sm-8">
											{{ Form::select('new_frequency',
											// dropdown options
											array(
											'none' => 'No email', 
											'daily' => 'Daily email',
											'weekly' => 'Weekly email'), 
											// highlighted option
											Auth::user()->summaryEmailFrequency,
											array(                                       
											'id' => 'id_frequency',
											'class' => 'form-control')) }}
										</div>
									</div> <!-- / .form-group -->

									<div class="col-sm-8 col-sm-offset-4 text-center padding-xs-vr">
										<button class="btn btn-warning btn-sm btn-flat" type="button" id="cancelFrequency">Cancel</button>  
										{{ Form::submit('Save', array(
										'id' => 'id_submit',
										'class' => 'btn btn-primary btn-sm btn-flat',
										'onClick'=> '_gaq.push(["_trackEvent", "Edit", "Frequency edited"]);
										mixpanel.track("Frequency edited");')) }}
									</div>

								</div>
								{{ Form::close() }}
							</div>
						</div> <!-- / .panel-body -->
					</div> <!-- / .col-sm-6 -->
				</div> <!-- /. col-md-10 -->
				<!-- /Notification settings -->


				<!-- Subscription settings -->
				<div class="col-md-10 col-md-offset-1">
					<div class="row">
						<div class="col-sm-6 col-sm-offset-3 subscription-form-wrapper">
							<div class="panel-body account-form bordered getHeight">
								<h4><i class="fa fa-cog"></i>&nbsp;&nbsp;Subscription settings</h4>

							 <!-- Subscription -->

							{{ Form::open(array(
								'action' => 'PaymentController@doCancelSubscription',
								'id' => 'form-settings-subscription',
								'role' => 'form',
								'class' => 'form-horizontal' )) }}

								<div id="editPlanForm">
									<div class="form-group">
										{{ Form::label('id_planedit', 'Subscription', array(
											'class' => 'col-sm-4 control-label')) }}
										<div class="col-sm-8">
											<p class="form-control-static">
												{{ $planName }} 
												<button id="editPlan" class="btn btn-flat btn-info btn-sm pull-right" type="button" onClick= '_gaq.push(["_trackEvent", "Edit", "Changing subscription"]);mixpanel.track("Changing subscription");'>Edit</button>
											</p>
										</div>
									</div> <!-- / .form-group -->
								</div>

								<!-- hidden subscription change / cancel -->

								<div id="changePlanForm" class="hidden-form">
									<div class="form-group @if ($errors->first('change_plan')) has-error @endif">
										{{ Form::label('id_plan', 'Subscription', array(
											'class' => 'col-sm-4 control-label')) }}
										<div class="col-sm-8">
											@if($planName == 'No subscription' 
													|| $planName == 'Trial period' 
													|| $planName == 'Trial period ended'
													|| $planName == 'Free pack')
												<a href='/plans'><button class='btn btn-success btn-flat pull-right' type='button' id='changePlan'>
													Subscribe
												</button></a>
											@else
												{{ Form::submit('Cancel subsctiption', array(
													'action' => 'PaymentController@doCancelSubscription',
													'class' => 'btn btn-danger btn-sm btn-flat pull-left'
													)) }}
												<a href='/plans'><button class='btn btn-info btn-sm btn-flat pull-right' type='button' id='changePlan'>
													Change subscription
												</button></a>                
											@endif
										</div>
									</div> <!-- / .form-group -->

									<div class="col-sm-8 col-sm-offset-4 text-center padding-xs-vr">
										<button class="btn btn-warning btn-sm btn-flat" type="button" id="cancelPlanEdit">Cancel</button>  
									</div>
								</div>

							{{ Form::close() }}
							
							<!-- / Subscription -->
							</div> <!-- / .panel-body -->
						</div> <!-- / .col-sm-6 -->
					</div> <!-- / .row -->
				</div> <!-- / .col-md-10 -->

				<!-- /Account settings -->

				<!-- Manage connections  -->
				<div class="col-md-10 col-md-offset-1">
				<div class="row">
				 <div class="col-sm-6 col-sm-offset-3 connect-form connection-form-wrapper">
					<div class="panel-body bordered sameHeight">
						<h4><i class="fa fa-link"></i>&nbsp;&nbsp;Manage connections</h4>
						<div class="list-group">
						{{--
						  <!-- stripe connect start -->
							<div class="list-group-item">
								<i class="icon pf pf-stripe pf-big pull-left right-space"></i>
								@if($user->isStripeConnected())
									<a href="{{ URL::route('auth.disconnect', 'stripe') }}">
										<button id="disconnectStripe" class="btn btn-flat btn-info btn-sm pull-right" type="button">Disconnect</button>
									</a>  
								@elseif ($user->canConnectMore())
									<a href="{{ $stripeButtonUrl }}">
										<button id="connectStripe" class="btn btn-flat btn-info btn-sm pull-right" type="button">Connect</button>
									</a>
								@else
									<a href="/plans">
										<button id="connectBraintree" class="btn btn-flat btn-info btn-sm pull-right" type="button">Connect</button>
									</a>  
								@endif
								<h4 class="list-group-item-heading">Stripe</h4>
								<p class="list-group-item-text">
									@if($user->isStripeConnected())
										<span class="up">Connected.</span>
									@else
										<span class="down">Not connected.</span>
									@endif
								</p>
							</div>
							<!-- stripe connect end -->

							<!-- braintree connect start -->
							<div class="list-group-item">
								<i class="icon pf pf-braintree pf-big pull-left right-space"></i>
								@if($user->isBraintreeConnected())
									<a href="{{ URL::route('auth.disconnect', 'braintree') }}">
										<button id="disconnectBraintree" class="btn btn-flat btn-info btn-sm pull-right" type="button">Disconnect</button>
									</a>  
								@elseif ($user->canConnectMore())
									<a href="/connect">
										<button id="connectBraintree" class="btn btn-flat btn-info btn-sm pull-right" type="button">Connect</button>
									</a>
								@else
									<a href="/plans">
										<button id="connectBraintree" class="btn btn-flat btn-info btn-sm pull-right" type="button">Connect</button>
									</a>  
								@endif
								<h4 class="list-group-item-heading">Braintree</h4>
								<p class="list-group-item-text">
									@if($user->isBraintreeConnected())
										<span class="up">Connected.</span>
									@else
										<span class="down">Not connected.</span>
									@endif
								</p>
							</div>
							<!-- braintree connect end -->
						--}}

							<!-- google spreadsheet connect start -->
							<div class="list-group-item">
								<i class="fa icon fa-google fa-4x pull-left"></i>
								@if($user->isGooglespreadsheetConnected())
									<a href="{{ URL::route('auth.disconnect', 'googlespreadsheet') }}">
										<button id="disconnectGoogleSpreadsheets" class="btn btn-flat btn-info btn-sm pull-right" type="button">Disconnect</button>
									</a>
								@endif
						            <a href="{{ URL::route('connect/new', ['provider' => 'googlespreadsheet', 'step' => 'init']) }}">
										<button id="connectGoogleSpreadsheets" class="btn btn-flat btn-info btn-sm pull-right" type="button">Connect</button>
									</a>  
								<h4 class="list-group-item-heading">Google Spreadsheet</h4>
								<p class="list-group-item-text">
									@if($user->isGooglespreadsheetConnected())
										<span class="up">Connected.</span>
									@else
										<span class="down">Not connected.</span>
									@endif
								</p>
							</div>
							<!-- google spreadsheet connect end -->
							</div> <!-- / .list-group -->
						</div> <!-- / .panel-body -->
					</div> <!-- / .col-sm-6 -->
				</div>
				</div> <!-- / .row -->
				<!-- /Manage connections  -->


				<!--  manage widgets  -->
				<div class="col-md-10 col-md-offset-1">
					<div class="row">
					<div class="col-sm-6 col-sm-offset-3 widget-form-wrapper">
						<div class="panel-body bordered sameHeight">
							<h4><i class="fa fa-link"></i>&nbsp;&nbsp;Manage widgets</h4>

							<!-- Background widget start -->
							<div class="list-group-item" style="border:none;">
								<i class="fa icon fa-picture-o fa-4x pull-left"></i>
								<h4 class="list-group-item-heading">Background image</h4>
								<div style="clear:both;"></div>
								<a href="{{ URL::route('connect.editwidget', 'background') }}" class="sm-pull-right">
									<button id="newWidget" class="btn btn-flat btn-info btn-sm pull-right" type="button">Edit background settings</button>
								</a>
							</div>
							<div style="clear:both"></div>
							<!-- Background widget end -->

							<div class="list-group-item" style="border:none;">
								<i class="fa icon fa-google fa-4x pull-left"></i>
								<h4 class="list-group-item-heading">Google Spreadsheet</h4>
								<p class="list-group-item-text">
								@if($user->isGooglespreadsheetConnected())
									<span class="up">Connected.</span>
								@else
									<span class="down">Not connected.</span>
								@endif
								</p>
								<div style="clear:both;"></div>
								<ul>
								@foreach ($google_spreadsheet_widgets as $widget)
									<li>
										{{ $widget->widget_name }}
										[<a href="{{ URL::route('connect.deletewidget', $widget->id) }}">remove</a>]
									</li>
								@endforeach
								</ul>
								@if($user->isGooglespreadsheetConnected())
								<a href="{{ URL::route('connect.addwidget', 'googlespreadsheet') }}" class="sm-pull-right">
									<button id="newWidget" class="btn btn-flat btn-info btn-sm pull-right" type="button">Add new widget</button>
								</a>
								@else
								<a href="{{ $googleSpreadsheetButtonUrl }}" class="sm-pull-right">
									<button id="newWidget" class="btn btn-flat btn-info btn-sm pull-right" type="button">Add new widget</button>
								</a>
								@endif
							</div>
							<div style="clear:both"></div>

							<div class="list-group-item" style="border:none;">
								<i class="fa icon fa-clock-o fa-4x pull-left"></i>
								<h4 class="list-group-item-heading">Clock widgets</h4>
								<div style="clear:both;"></div>  
								<ul>
								@foreach ($clock_widgets as $widget)
									<li>
										{{ $widget->widget_name }}
										[<a href="{{ URL::route('connect.deletewidget', $widget->id) }}">remove</a>]
									</li>
								@endforeach
								</ul>
								<a href="{{ URL::route('connect.addwidget', 'clock') }}" class="sm-pull-right">
									<button id="newWidget" class="btn btn-flat btn-info btn-sm pull-right" type="button">Add new widget</button>
								</a>
							</div>

							<div style="clear:both"></div>

							<div class="list-group-item" style="border:none;">
								<i class="fa icon fa-file-text-o fa-4x pull-left"></i>
								<h4 class="list-group-item-heading">iframe widgets</h4>
								<div style="clear:both;"></div>  
								<ul>
								@foreach ($iframe_widgets as $widget)
									<li>
										{{ $widget->widget_name }}
										[<a href="{{ URL::route('connect.deletewidget', $widget->id) }}">remove</a>]
									</li>
								@endforeach
								</ul>
								<a href="{{ URL::route('connect.addwidget', 'iframe') }}" class="sm-pull-right">
									<button id="newWidget" class="btn btn-flat btn-info btn-sm pull-right" type="button">Add new widget</button>
								</a>
							</div>

							<div style="clear:both"></div>

							<div class="list-group-item" style="border:none;">
								<i class="fa icon fa-quote-left fa-4x pull-left"></i>
								<h4 class="list-group-item-heading">Quote widgets</h4>
								<div style="clear:both;"></div>  
								<ul>
								@foreach ($quote_widgets as $widget)
									<li>
										{{ $widget->widget_name }}
										[<a href="{{ URL::route('connect.deletewidget', $widget->id) }}">remove</a>]
									</li>
								@endforeach
								</ul>
								<a href="{{ URL::route('connect.addwidget', 'quote') }}" class="sm-pull-right">
									<button id="newWidget" class="btn btn-flat btn-info btn-sm pull-right" type="button">Add new widget</button>
								</a>
							</div>

							<div style="clear:both"></div>

							<div class="list-group-item" style="border:none;">
								<i class="fa icon fa-pencil fa-4x pull-left"></i>
								<h4 class="list-group-item-heading">Note widgets</h4>
								<div style="clear:both;"></div>  
								<ul>
								@foreach ($note_widgets as $widget)
									<li>
										{{ $widget->widget_name }}
										[<a href="{{ URL::route('connect.deletewidget', $widget->id) }}">remove</a>]
									</li>
								@endforeach
								</ul>
								<a href="{{ URL::route('connect.addwidget', 'note') }}" class="sm-pull-right">
									<button id="newWidget" class="btn btn-flat btn-info btn-sm pull-right" type="button">Add new widget</button>
								</a>
							</div>

							<div style="clear:both"></div>

							<div class="list-group-item" style="border:none;">
								<i class="fa icon fa-comment-o fa-4x pull-left"></i>
								<h4 class="list-group-item-heading">Greetings widgets</h4>
								<div style="clear:both;"></div>  
								<ul>
								@foreach ($greeting_widgets as $widget)
									<li>
										{{ $widget->widget_name }}
										[<a href="{{ URL::route('connect.deletewidget', $widget->id) }}">remove</a>]
									</li>
								@endforeach
								</ul>
								<a href="{{ URL::route('connect.addwidget', 'greeting') }}" class="sm-pull-right">
									<button id="newWidget" class="btn btn-flat btn-info btn-sm pull-right" type="button">Add new widget</button>
								</a>
							</div>

						</div> <!-- / .panel-body -->
					</div> <!-- / .col-sm-6 -->
				</div>
				</div> <!-- / .row -->
				<!-- / manage widgets  -->

			</div> <!-- /. col-md-10 -->
		</div> <!-- / #content-wrapper -->
	@stop

	@section('pageScripts')

		@if (Session::get('errors') || Session::get('error'))
			<script type="text/javascript">
				init.push(function () {
					// if error slide down
					@if ($errors->first('name')|| $errors->first('name_password'))
					$('#editNameForm').slideUp('fast', function (){
						$('#changeNameForm').slideDown('fast');
					});
					@elseif ($errors->first('country'))
					$('#editCountryForm').slideUp('fast', function (){
						$('#changeCountryForm').slideDown('fast');
					});
					@elseif ($errors->first('email') || $errors->first('email_password'))
					$('#editEmailForm').slideUp('fast', function (){
						$('#changeEmailForm').slideDown('fast');
					});
					@elseif ($errors->first('old_password') || $errors->first('new_password'))
					$('#editPasswordForm').slideUp('fast', function (){
						$('#changePasswordForm').slideDown('fast');
					});
					
					@endif
				});
			</script>
		@endif 

		<script type="text/javascript">
			init.push(function () {
				// event listeners for hidden forms
				$('#editName').on('click', function (){
					$('#editNameForm').slideUp('fast', function (){
						$('#changeNameForm').slideDown('fast');
					});
				})
				$('#editCountry').on('click', function (){
					$('#editCountryForm').slideUp('fast', function (){
						$('#changeCountryForm').slideDown('fast');
					});
				})
				$('#editEmail').on('click', function (){
					$('#editEmailForm').slideUp('fast', function (){
						$('#changeEmailForm').slideDown('fast');
					});
				})
				$('#editPassword').on('click', function (){
					$('#editPasswordForm').slideUp('fast', function (){
						$('#changePasswordForm').slideDown('fast');
					});
				})
				$('#editFrequency').on('click', function (){
					$('#editFrequencyForm').slideUp('fast', function (){
						$('#changeFrequencyForm').slideDown('fast');
					});
				})
				$('#editPlan').on('click', function (){
					$('#editPlanForm').slideUp('fast', function (){
						$('#changePlanForm').slideDown('fast');
					});
				})

				// event listeners for cancel buttons
				$('#cancelName').on('click', function (){
					$('#changeNameForm').slideUp('fast', function (){
						$('#editNameForm').slideDown('fast');
					});
				})
				$('#cancelCountry').on('click', function (){
					$('#changeCountryForm').slideUp('fast', function (){
						$('#editCountryForm').slideDown('fast');
					});
				})
				$('#cancelEmail').on('click', function (){
					$('#changeEmailForm').slideUp('fast', function (){
						$('#editEmailForm').slideDown('fast');
					});
				})
				$('#cancelPassword').on('click', function (){
					$('#changePasswordForm').slideUp('fast', function (){
						$('#editPasswordForm').slideDown('fast');
					});
				})
				$('#cancelFrequency').on('click', function (){
					$('#changeFrequencyForm').slideUp('fast', function (){
						$('#editFrequencyForm').slideDown('fast');
					});
				})
				$('#cancelPlanEdit').on('click', function (){
					$('#changePlanForm').slideUp('fast', function (){
						$('#editPlanForm').slideDown('fast');
					});
				})

				// switchers
				$('#id_background').switcher();
				$('#switchers-colors-square > input').switcher({ theme: 'square' });
				$('#switchers-colors-modern > input').switcher({ theme: 'modern' });
				
			});

		</script>
	@stop
