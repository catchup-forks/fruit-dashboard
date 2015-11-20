@if ( ! $widget['hasData'])
  <div id="widget-loading-{{ $widget['id'] }}" class="widget-inner fill">
    <p class="text-center">
      <h4 class="text-center">{{ $widget['settings']['name'] }} (id: {{ $widget['id'] }})</h4>
      This widget is waiting for data on this url:
      <pre>
        {{ $widget['settings']['url'] }}
      </pre>
    </p> <!-- /.lead -->
    <p class="text-center">
      <button onclick="copyToClipboard('{{ $widget['settings']['url'] }}');" class="btn btn-sm btn-primary">Copy to clipboard</button>
    </p>
    <p class="text-center">
      Read more about the API <a href="https://github.com/tryfruit/fruit-dashboard-api" target="_blank">here</a>
    </p> <!-- /.lead -->
  </div> <!-- /.widget-inner -->
@else
    @include('widget.widget-general-histogram')
@endif

@section('widgetScripts')
<script type="text/javascript">
  @if ( ! $widget['hasData'])
    var widgetData{{ $widget['id'] }} = {};
    function copyToClipboard(url) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(url).select();
        document.execCommand("copy");
        $temp.remove();
    }
  @endif
</script>
@append
