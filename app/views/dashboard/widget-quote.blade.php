<li data-id='{{ $widget_data["widget_id"] }}' class="dashboard-widget grey-hover" data-row="{{ $widget_data['position']['row'] }}" data-col="{{ $widget_data['position']['col'] }}" data-sizex="{{ $widget_data['position']['x'] }}" data-sizey="{{ $widget_data['position']['y'] }}">
	<!--a class='link-button' href='' data-toggle="modal" data-target='#widget-settings-{{ $id }}'><span class="gs-option-widgets"></span></a-->
	<a href="{{ URL::route('connect.deletewidget', $id) }}">
    <span class="gs-close-widgets"></span>
  </a>

	<p id="quote" class="textShadow">{{ $quote }}</p>
	{{-- <p id="author" class="textShadow pull-right">{{ $author }}</p> --}}
</li>

@section('pageModals')
	<!-- quote settings -->
	
	@include('settings.widget-settings')

	<!-- /quote settings -->
@append
