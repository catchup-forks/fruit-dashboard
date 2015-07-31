<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<title>Braintree testing</title>

	<!-- Braintree JS library -->
	{{ HTML::script('https://js.braintreegateway.com/v2/braintree.js') }}
</head>

<body>

		<h1>Hello World</h1>

	<div id='paymentStuff'>
		<form id="checkout" method="post" action="">
	  		<div id="dropin"></div>
	  		<input type="submit" value="Subscribe">
		</form>
	</div>

	<div id='results'>
		@if(isset($result))
			{{ $result->getMessage() }}
		@endif
	</div>

	@if(!isset($result))
		<script type="text/javascript">
			braintree.setup(
			  '{{ $clientToken }}',
			  'dropin', {
			    container: 'dropin'
			});
		</script>
	@endif
</body>
