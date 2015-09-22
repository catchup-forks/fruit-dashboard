@if ($widget->dataManager()->getData() == FALSE)
    <div class="text-white text-center drop-shadow" id="widget-loading-{{ $widget->id }}">
        <h3>This widget is waiting for data on this url: </h3><br>
        {{ $widget->dataManager()->getUrl() }}
        <a href="{{ route('api.test', [$widget->id]) }}">You can test it from here</a>
    </div>
@else
    @include('widget.widget-general-multiple-histogram', ['widget' => $widget])
@endif
