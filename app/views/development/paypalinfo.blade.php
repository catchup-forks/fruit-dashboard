@extends('meta.base-user-signout')

@section('pageContent')
<!-- accesstoken from paypal -->
<div class="container">
	@include('meta.pageAlerts')
	<div class="row">
		<div class="col-sm-4 col-sm-offset-4">
			<h1 class="text-center text-white padding-sm-vr">Start Up Dashboard</h1>
			<p class="text-center">{{ $accessToken }}</p>
			<div class="text-center padding-xs-vr footer-copyright">
				<a href="#">&copy; 2014 Start Up Dashboard</a>
			</div>

		</div>
	</div>
</div>

@stop

