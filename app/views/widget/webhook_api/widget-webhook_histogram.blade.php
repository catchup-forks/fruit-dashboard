@if ( ! $widget['hasData'])
  <div id="widget-loading-{{ $widget['id'] }}" class="widget-inner fill">
    <p class="text-center">
        This widget has no data available yet, please check the URL in the settings, and try again.
    </p>
  </div> <!-- /.widget-inner -->
@else
    @include('widget.widget-general-histogram')
@endif
