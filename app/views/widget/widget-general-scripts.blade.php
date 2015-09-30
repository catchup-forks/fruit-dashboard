<script type="text/javascript">

  // // HAMBURGER MENU
  // // Call the Hamburger Menu.
  // $('.dropdown-toggle').dropdown();

  // // If the mouse leaves the contextual menu, close it.
  // $(".dropdown-menu").mouseleave(function(){
  //   $(".dropdown").removeClass("open");
  // });

  // DELETE WIDGET
  // Look for the delete menu click
  // $(".deleteWidget").click(function(e) {

  //   e.preventDefault();

  //   // initialize url
  //   var url = "{{ route('widget.delete', 'widgetID') }}".replace('widgetID', $(this).attr("data-id"))

  //   // Look for the actual gridster dashboard instance.
  //   var gridsterID = $(this).closest('.gridster').attr('id');
  //   var regridster

  //   // Reinitialize gridster and remove widget.
  //   regridster = $('#' + gridsterID + ' div.gridster-container').gridster().data('gridster');
  //   regridster.remove_widget($(this).closest('div.gridster-player'));


  //   // Call ajax function
  //   $.ajax({
  //     type: "POST",
  //     dataType: 'json',
  //     url: url,
  //          data: null,
  //          success: function(data) {
  //             easyGrowl('success', "You successfully deleted the widget", 3000);
  //          },
  //          error: function(){
  //             easyGrowl('error', "Something went wrong, we couldn't delete your widget. Please try again.", 3000);
  //          }
  //   });
  // });

  // function sendAjax(postData, widgetId, callback) {
  //   $.ajax({
  //   type: "POST",
  //   data: postData,
  //   url: "{{ route('widget.ajax-handler', 'widgetID') }}".replace("widgetID", widgetId)
  //   }).done(function( data ) {
  //     callback(data);
  //   });
  // }

  // function loadWidget(widgetId, callback) {
  //   var done = false;
  //   function pollState() {
  //     sendAjax({'state_query': true}, widgetId, function (data) {
  //       if (data['ready']) {
  //         $("#widget-loading-" + widgetId).hide();
  //         $("#widget-wrapper-" + widgetId).show();
  //         done = true;
  //         callback(data['data']);
  //       }
  //       if ( ! done ) {
  //         setTimeout(pollState, 1000);
  //       }
  //     });
  //   }
  //   pollState();
  // };

  // function refreshWidget(widgetId, callback) {
  //   $("#widget-wrapper-" + widgetId).hide();
  //   $("#widget-loading-" + widgetId).show();
  //   sendAjax({'refresh_data': true}, widgetId, callback);
  //   loadWidget(widgetId, callback);
  // };

  // Function reinsertCanvas empties the container and reinserts a canvas. If measure is true then it updates the sizing variables.
  // function reinsertCanvas(canvas) {
  //   var canvasHeight = canvas.closest('div.gridster-player').height()-2*10;
  //   var canvasWidth = canvas.closest('div.gridster-player').width()-5-30;

  //   canvasId = canvas[0].id;
  //   container = $("#" + canvasId + "-container");

  //   container.empty();
  //   container.append('<canvas id=\"' + canvasId + '\" class=\"chart chart-line\" height=\"' + canvasHeight +'\" width=\"' + canvasWidth + '\"></canvas>');

  //   return $("#" + canvasId);
  // }

  // function updateHistogramWidget(data, canvas, name, valueSpan) {

  //   // Updating chart values.
  //   var labels = [];
  //   var values = [];
  //   for (i = 0; i < data.length; ++i) {
  //     labels.push(data[i]['datetime']);
  //     values.push(data[i]['value']);
  //   }
  //   if (data.length > 0 && valueSpan) {
  //     valueSpan.html(data[data.length-1]['value']);
  //   }

  //   return {'values': values, 'labels': labels};
  // }

  // function updateMultipleHistogramWidget(data, canvas, name) {
  //   if (data && data['datetimes'] == null) {
  //     return;
  //   }
  // }

  // function updateMentionsWidget(data, containerId) {
  //   if (data.length === undefined) {
  //     return;
  //   }
  //   console.log("hello");

  //   function clearContainer() {
  //     $(containerId).html('');
  //   }

  //   for (word in data['text']) {
  //     console.log(word);
  //   }

  //   clearContainer();

  // }

  // function clearTable(tableId) {
  //   $("#" + tableId + " tbody").remove();
  //   $("#" + tableId + " thead").remove();
  // }

  // function updateTableWidget(data, tableId) {
  //   if ( data.length == undefined) {
  //     return;
  //   }

  //   clearTable(tableId);

  //   // Adding header
  //   var header = '<thead>';
  //   for (var name in data['header']) {
  //     header += '<th>' + name + '</th>';
  //   }
  //   header += '</thead>';
  //   $("#" + tableId).append(header);

  //   // Adding content
  //   var content = '<tbody>';
  //   for (var row=0; row < data['content'].length; row++) {
  //     content += '<tr>';
  //     for (var key in data['content'][row]) {
  //       content += '<td>' + data['content'][row][key] + '</td>';
  //     }
  //     content += '</tr>';
  //   }
  //     content += '</tbody>';
  //   $("#" + tableId).append(content);

  // }

</script>