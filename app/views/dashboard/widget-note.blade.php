<li data-id='{{ $id }}' class="dashboard-widget grey-hover" data-row="{{ $position['row'] }}" data-col="{{ $position['col'] }}" data-sizex="{{ $position['x'] }}" data-sizey="{{ $position['y'] }}">
	<!--a class='link-button' href='' data-toggle="modal" data-target='#widget-settings-{{ $id }}'><span class="gs-option-widgets"></span></a-->
	<a href="{{ URL::route('connect.deletewidget', $id) }}"><span class="gs-close-widgets"></span></a>
	
	{{Form::textarea('note', $currentValue, array(
		'id' => $id,
		'class' => 'noteClass text-fill-note note'
		)
	)}}
</li>

@section('pageModals')
	<!-- note settings -->
	
  	@include('settings.widget-settings')

	<!-- /note settings -->
@append
