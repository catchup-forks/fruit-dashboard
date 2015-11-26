<div class="flex-container">
  <div class="text-center">
    <h4 id="promo-{{ $widget['id'] }}" class="no-margin-top has-margin-vertical-sm">
      <a href="{{ $widget['connectionMeta']['url'] }}?createDashboard=1" id="promo-url-{{ $widget['id'] }}">
        {{ HTML::image(
          $widget['settings']['photo_location'],
          $widget['relatedDescriptor']->name, array(
            'class' => 'promo-opaque img-responsive img-rounded'
          )
        )}}
      </a>
    </h4>
  </div> <!-- /.text-center -->
</div> <!-- /.flex-container -->

@section('widgetScripts')
<script type="text/javascript">
  var widgetData{{ $widget['id'] }} = { }
</script>
@append