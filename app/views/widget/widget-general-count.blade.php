  <div class="text-white drop-shadow has-margin-horizontal text-center" data-toggle="tooltip" data-placement="bottom" title="
  @if ($widget->descriptor->category == 'facebook')
    facebook page name FIXME
  @else if ($widget->descriptor->category == 'google_analytics')
    google analytics property name FIXME
  @endif
  ">
    <h3 id="{{$widget->id}}-value" class="truncate">
      {{ $widget->getCurrentValue()['value'] }}
    </h3>
    <p>
      <small>{{ $widget->descriptor->category }}</small>
    </p>
  </div>

@section('widgetScripts')
<script type="text/javascript">
  $(function() {
    $('#refresh-{{$widget->id}}').click(function () {
      refreshWidget({{ $widget->id }}, function (data) {
        $('#{{$widget->id}}-value').html(data.value);
      })
    })
  });
</script>
@append