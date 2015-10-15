<div class="widget-inner fill" id="widget-loading-{{ $widget['id'] }}">
  <div class="widget-heading larger-text">
    {{ Utilities::underscoreToCamelCase($widget['descriptor']->type, TRUE) }}
  </div> <!-- /.widget-heading -->
  <p class="lead text-center">
    This widget has no data, you can try to refresh it, or check the data source, if it's available.
  </p>
</div> <!-- /.widget-inner -->