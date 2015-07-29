<div class="text-white text-center">
    <span class="fa fa-refresh" id="refresh"></span>
	<p class="lead" id="quote">
		{{ $widget->getData()['quote'] }}
	</p>
	<span class="display-hovered" id="author">
		{{ $widget->getData()['author'] }}
	</span>
</div>

@section('widgetScripts')

 <script type="text/javascript">
   $(document).ready(function() {
    $("#refresh").click(function () {
        $.ajax({
            type: "POST",
            data: {},
            url: "{{ route('widget.ajax-handler', $widget->id) }}"
        }).done(function( data ) {
            $("#quote").html(data['quote']);
            $("#author").html(data['author']);
      });;
    });
   });
 </script>

