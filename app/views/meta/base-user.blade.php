@extends('meta.meta')

@section('body')

	<body @if(isset($isBackgroundOn)) @if($isBackgroundOn) style="background: url({{$dailyBackgroundURL}}) no-repeat center center fixed" @endif @endif>
			@section('navbar')
				@include('meta.navbar')
			@show
			
			@section('pageContent')

			@show

			@section('footer')
				@include('meta.footer')
			@show

			@section('intercomScript')

				<!-- Intercom Script -->
				<script>
		    	window.intercomSettings = {
					@if (Auth::user())
				    	name: "{{ Auth::user()->email }}",
				        email: "{{ Auth::user()->email }}",
				        created_at: "{{ Auth::user()->created_at }}",
		    		@else
		    			name: 'Demo',
		    			email: 'Demo',
		    			created_at: {{ strtotime('2015-03-17'); }},
		    		@endif
			    	app_id: "nch9zmp2"
		    	};
				</script>
		
				{{ HTML::script('js/intercom_io.js'); }}
				<!-- / Intercom Script -->

			@show
		
			@section('mixpanelUserTracking')
				<script type="text/javascript">
					@if(Auth::user())
						mixpanel.identify( "{{ Auth::user()->id}}" );
						mixpanel.people.set({
							"$email": "{{ Auth::user()->email }}",    
						    "$created": "{{ Auth::user()->created_at }}",
						    "$last_login": "{{ Carbon::now() }}"        
						});
					@else
						mixpanel.identify( "Demo" );
					@endif
				</script>
			@show
	</body>

@stop