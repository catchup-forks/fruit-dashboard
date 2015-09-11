<div class="text-white text-center drop-shadow quote">
  <div class="margin-top-sm has-margin-horizontal">
    <p class="lead body" id="quote-{{ $widget->id }}">
      {{ $widget->getData()['quote'] }}
    </p>
    <p class="source" id="author-{{ $widget->id }}">
      {{ $widget->getData()['author'] }}
    </p>
  </div> <!-- /.container -->
</div>

@section('widgetScripts')

<script type="text/javascript">
  function updateWidget(data) {
    $("#quote-{{ $widget->id }}").html(data['quote']);
    $("#author-{{ $widget->id }}").html(data['author']);
  }

  $(document).ready(function() {
    @if((Carbon::now()->timestamp - $widget->data->updated_at->timestamp) /3660 > $widget->dataManager()->update_frequency)
      refreshWidget({{ $widget->id }}, function (data) { updateWidget(data);});
    @endif

     $("#refresh-{{$widget->id}}").click(function (e) {
      e.preventDefault();
      refreshWidget({{ $widget->id }}, function (data) { updateWidget(data);});
     });
   });
 </script>

@append