@if ($widget->dataManager()->getData() == FALSE)
  <div id="widget-loading-{{ $widget->id }}" class="widget-inner fill">
    <p class="text-center">
      This widget is waiting for data on this url:
      <pre>
        {{ $widget->getSettings()['url'] }}
      </pre>
    </p> <!-- /.lead -->
    <p class="text-center">
      <button onclick="copyToClipboard('{{ $widget->getSettings()['url'] }}');" class="btn btn-sm btn-primary">Copy to clipboard</button>
      <a href="{{ route('api.test', [$widget->id]) }}" class="btn btn-link">Test widget</a>
    </p>
  </div> <!-- /.widget-inner -->
@else
    @include('widget.widget-general-multiple-histogram', ['widget' => $widget])
@endif

@section('widgetScripts')
<script type="text/javascript">

  function copyToClipboard(url) {
      var $temp = $("<input>");
      $("body").append($temp);
      $temp.val(url).select();
      document.execCommand("copy");
      $temp.remove();
  }

$(document).ready(function(){

});

</script>
@append