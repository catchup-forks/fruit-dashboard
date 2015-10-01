<div class="panel-transparent">
  Events:<br>
  <div class="panel-body" id="events-holder-{{ $widget->id }}">
  </div>
</div>

@section('widgetScripts')

 <script type="text/javascript">
  // $(document).ready(function() {
  //   function updateWidget() {
  //     $.ajax({
  //      type: "POST",
  //      data: {'type': 'all', 'collect': false},
  //      url: "{{ route('widget.ajax-handler', $widget->id) }}"
  //     }).done(function( events ) {
  //       for (i = 0; i < events.length; i++) {
  //         labelClass = '';
  //         labelName = '';
  //         htmlData = '';

  //         if (events[i]['type'].indexOf('charge') > -1) {
  //            // charge.
  //            charge = events[i]['data']['object'];
  //            labelName = 'charge';
  //            htmlData = charge['amount'] + ' ' + charge['currency'];
  //         } else if (events[i]['type'].indexOf('customer.subscription') > -1) {

  //            // subscription.
  //            charge = events[i]['data']['object'];
  //            labelName = 'charge';
  //            htmlData = charge['amount'] + ' ' + charge['currency'];
  //         }

  //         // Setting class.
  //         switch (events[i]['type']) {
  //           case 'charge.succeeded': labelClass='success'; break;
  //           case 'charge.failed': labelClass='danger'; break;
  //         }

  //         $('#events-holder-{{ $widget->id }}').append(
  //           '<div><span class="label label-' + labelClass + ' label-as-badge">' + labelName + '</span> ' + htmlData + '</div>'
  //           );
  //         console.log(events[i]);
  //       }

  //     });
  //   }

  //   $("#refresh-{{$widget->id}}").click(function () {
  //     updateWidget();
  //   });
  // });
 </script>

@append