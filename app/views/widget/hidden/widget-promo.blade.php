<h4 id="promo-{{ $widget['id'] }}" class="text-white text-center drop-shadow no-margin-top has-margin-vertical-sm">
  {{ HTML::image(
    $widget['settings']['photo_location'],
    $widget['relatedDescriptor']->name, array(
      'class' => 'img-responsive img-rounded center-block'
    )
  )}}
  @if (TRUE)
  @endif
  {{ $widget['connectionMeta']['text'] }}
  <a href="{{ $widget['connectionMeta']['url'] }}?createDashboard=1"><button class="btn btn-primary btn-xs">here</button></a>.

</h4>

@section('widgetScripts')
@append