<div data-id='{{ $widget['id'] }}'
     data-row="{{ $widget['position']->row }}"
     data-col="{{ $widget['position']->col }}"
     data-sizex="{{ $widget['position']->size_x }}"
     data-sizey="{{ $widget['position']->size_y }}"
     data-min-sizex="{{ $widget['min_cols'] }}"
     data-min-sizey="{{ $widget['min_rows'] }}"
     class="gridster-widget can-hover">

     @if (is_subclass_of($widget['className'], 'SharedWidget'))
      <div class="position-tr-sm-second">
        <span class="fa fa-share-alt text-white display-hovered drop-shadow" data-toggle="tooltip" title="This widget is shared with you" data-placement="left"></span>
      </div> <!-- /.position-tr-sm-second -->
     @endif

    <div class="dropdown position-tr-sm">
      <a id="{{ $widget['id'] }}" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="fa fa-bars drop-shadow text-white color-hovered display-hovered"></span>
      </a>
      <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="{{ $widget['id'] }}">

        @if ($widget['state'] != 'setup_required')
        {{-- EDIT --}}
        <li>
          <a href="{{ route('widget.edit', $widget['id']) }}">
            <span class="fa fa-cog"> </span>
            Edit Settings
          </a>
        </li>

        {{-- REFRESH --}}
         @if (is_subclass_of($widget['className'], 'iAjaxWidget'))
          <li>
            <a href="#" id="widget-refresh-{{$widget['id']}}" title="refresh widget content">
              <span class="fa fa-refresh"> </span>
              Refresh data
            </a>
          </li>
        @endif

        {{-- SHARE --}}
        @if ( ! is_subclass_of($widget['className'], 'SharedWidget') && ! is_subclass_of($widget['className'], 'PromoWidget'))
        <li>
          <a href="#" id="share-{{$widget['id']}}" onclick="showShareModal({{$widget['id']}})">
            <span class="fa fa-share-alt"> </span>
            Share widget
          </a>
        </li>
        @endif
        @endif

        {{-- DELETE --}}
        <li>
          <a href="#" class="widget-delete" data-id='{{ $widget['id'] }}' >
            <span class="fa fa-times"> </span>
            Delete widget
          </a>
        </li>

      </ul>
    </div>
  @if ($widget['state'] == 'setup_required')
      @include('widget.errors.widget-setup-required')
  @elseif ($widget['state'] == 'data_source_error')
      @include('widget.errors.widget-data-source-error')
  @elseif ($widget['state'] == 'rendering_error')
      @include('widget.errors.widget-rendering-error')
  @elseif (is_subclass_of($widget['className'], 'SharedWidget'))
      <div class="@if ($widget['relatedWidget']->state == 'loading') not-visible @endif fill" id="widget-wrapper-{{$widget['relatedWidget']->id}}">
        @include(
          $widget['relatedWidget']->getDescriptor()->getTemplateName(),
          ['widget' => $widget['relatedWidget']->getTemplateData()]
        )
      </div>
  @else
    @if (is_subclass_of($widget['className'], 'iAjaxWidget'))
      @include('widget.widget-loading')
      <div class="@if ($widget['state'] == 'loading') not-visible @endif fill" id="widget-wrapper-{{$widget['id']}}">
    @endif
    @if ($widget['state'] != 'loading')
      @include($widget['descriptor']['templateName'])
    @endif
    <!-- Adding loading on DataWidget -->
    @if (is_subclass_of($widget['className'], 'iAjaxWidget'))
      </div>
    @endif

  @endif
</div> <!-- /.gridster-widget -->
