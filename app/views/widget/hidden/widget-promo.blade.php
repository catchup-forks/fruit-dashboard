<h3 id="promo-{{ $widget['id'] }}" class="text-white text-center drop-shadow no-margin-top has-margin-vertical-sm">
  {{ HTML::image(
    $widget['relatedDescriptor']->getPhotoLocation(),
    $widget['relatedDescriptor']->name, array(
      'class' => 'img-responsive img-rounded center-block'
    )
  )}}

</h3>

@section('widgetScripts')
@append