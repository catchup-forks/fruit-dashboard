<h4 id="promo-{{ $widget['id'] }}" class="no-margin-top has-margin-vertical-sm">
  <a href="{{ $widget['connectionMeta']['url'] }}?createDashboard=1">
    {{ HTML::image(
      $widget['settings']['photo_location'],
      $widget['relatedDescriptor']->name, array(
        'class' => 'promo-opaque img-responsive img-rounded center-block'
      )
    )}}
  </a>
</h4>

@section('widgetScripts')
@append