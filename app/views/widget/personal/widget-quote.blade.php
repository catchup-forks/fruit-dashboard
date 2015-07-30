<div class="text-white text-center quote">
  <span class="fa fa-refresh" id="refresh"></span>
  <p class="lead body" id="quote">
    {{ $widget->getData()['quote'] }}
  </p>
  <p class="source" id="author">
    {{ $widget->getData()['author'] }}
  </p>
</div>

@section('widgetScripts')

 <script type="text/javascript">
   function updateWidget() {
     $.ajax({
       type: "POST",
       data: {},
       url: "{{ route('widget.ajax-handler', $widget->id) }}"
      }).done(function( data ) {
        $("#quote").html(data['quote']);
        $("#author").html(data['author']);
      });
   }

   $(document).ready(function() {
     @if((Carbon::now()->timestamp - $widget->data->updated_at->timestamp) / 60 > $widget->getSettings()['update_frequency'])
        updateWidget();
     @endif

     $("#refresh").click(function () {
        updateWidget();
     });
   });
 </script>

@append