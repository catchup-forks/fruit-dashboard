<li data-id='{{ $widget_data["widget_id"] }}' class="dashboard-widget grey-hover" data-row="{{ $widget_data['position']['row'] }}" data-col="{{ $widget_data['position']['col'] }}" data-sizex="{{ $widget_data['position']['x'] }}" data-sizey="{{ $widget_data['position']['y'] }}">
    
    <a href="{{ URL::route('connect.deletewidget', $id) }}">
        <span class="gs-close-widgets"></span>
    </a>
    <a href="#">
        <div id="broken-widget" class="white-text center textShadow">
            <i class="fa fa-3x fa-cogs"></i>
            <p>Something went wrong, please reconfigure widget.</p>
        </div>
    </a>
    </div>

</li>