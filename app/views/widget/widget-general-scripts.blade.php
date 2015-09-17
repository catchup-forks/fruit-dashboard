<script type="text/javascript">

  // Overriding chartjs defaults.
  Chart.defaults.global.animationSteps = 60;
  Chart.defaults.global.animationEasing = "easeOutQuart";
  Chart.defaults.global.scaleLineColor = "rgba(179,179,179,1)";
  Chart.defaults.global.scaleFontSize = 9;
  Chart.defaults.global.scaleFontFamily = "'Open Sans', sans-serif";
  Chart.defaults.global.scaleFontColor = "rgba(230,230,230,1)";
  Chart.defaults.global.responsive = false;
  Chart.defaults.global.tooltipCornerRadius = 4;
  Chart.defaults.global.tooltipXPadding = 5;
  Chart.defaults.global.tooltipYPadding = 5;
  Chart.defaults.global.tooltipCaretSize = 5;
  Chart.defaults.global.tooltipFillColor = "rgba(0,0,0,0.6)";
  Chart.defaults.global.tooltipFontFamily = "'Open Sans', sans-serif";
  Chart.defaults.global.tooltipFontSize = 11;
  Chart.defaults.global.tooltipFontStyle = "lighter";


  var chartOptions = {
     pointHitDetectionRadius : 5,
     pointDotRadius : 2,
     bezierCurve: true,
     bezierCurveTension : 0.35,
     scaleGridLineColor : "rgba(179,179,179,0.4)",
     scaleGridLineWidth : 0.35,
     tooltipTemplate: "<%if (label){%><%=label %>: <%}%><%= value %>",
     animation: true
  };

  $(".deleteWidget").click(function(e) {

    e.preventDefault();

    // initialize url
    var url = "{{ route('widget.delete', 'widgetID') }}".replace('widgetID', $(this).attr("data-id"))

    // Look for the actual gridster dashboard instance.
    var gridsterID = $(this).closest('.gridster').attr('id');
    var regridster

    // Reinitialize gridster and remove widget.
    regridster = $('#' + gridsterID + ' ul').gridster().data('gridster');
    regridster.remove_widget($(this).closest('li'));


    // Call ajax function
    $.ajax({
      type: "POST",
      dataType: 'json',
      url: url,
           data: null,
           success: function(data) {
              easyGrowl('success', "You successfully deleted the widget", 3000);
           },
           error: function(){
              easyGrowl('error', "Something went wrong, we couldn't delete your widget. Please try again.", 3000);
           }
    });
  });

  function sendAjax(postData, widgetId, callback) {
    $.ajax({
    type: "POST",
    data: postData,
    url: "{{ route('widget.ajax-handler', 'widgetID') }}".replace("widgetID", widgetId)
    }).done(function( data ) {
      callback(data);
    });
  }

  function loadWidget(widgetId, callback) {
    var done = false;
    function pollState() {
      sendAjax({'state_query': true}, widgetId, function (data) {
        if (data['ready']) {
          $("#widget-loading-" + widgetId).hide();
          $("#widget-wrapper-" + widgetId).show();
          done = true;
          callback(data['data']);
        }
        if ( ! done ) {
          setTimeout(pollState, 1000);
        }
      });
    }
    pollState();
  };

  function refreshWidget(widgetId, callback) {
    $("#widget-wrapper-" + widgetId).hide();
    $("#widget-loading-" + widgetId).show();
    sendAjax({'refresh_data': true}, widgetId, callback);
    loadWidget(widgetId, callback);
  };

  function createDataSet(values, name, color) {
    return {
      label: name,
      fillColor : "rgba(" + color + ",0.2)",
      strokeColor : "rgba(" + color + ",1)",
      pointColor : "rgba(" + color + ",1)",
      pointStrokeColor : "#fff",
      pointHighlightFill : "#fff",
      pointHighlightStroke : "rgba(" + color + ",1)",
      data: values
    }
  }

  // Function reinsertCanvas empties the container and reinserts a canvas. If measure is true then it updates the sizing variables.
  function reinsertCanvas(canvas) {
    canvasHeight = canvas.closest('li').height()-2*35;
    canvasWidth = canvas.closest('li').width()-2*30;

    canvasId = canvas[0].id;
    container = $("#" + canvasId + "-container");

    container.empty();
    container.append('<canvas id=\"' + canvasId + '\" class=\"chart chart-line\" height=\"' + canvasHeight +'\" width=\"' + canvasWidth + '\"></canvas>');

    return $("#" + canvasId);
  }

  function drawLineGraph(canvas, datasets, labels, name) {
    // Building data.
    var chartData = {
      labels: labels,
      datasets: []
    };
    for (i = 0; i < datasets.length; ++i) {
      if (datasets[i]['color']) {
        color = datasets[i]['color'];
      } else {
        color = '{{ SiteConstants::getChartJsColors()[0] }}';
      }
      chartData.datasets.push(createDataSet(datasets[i]['values'], datasets[i]['name'], color));
    }

    // Getting context.
    var ctx = canvas[0].getContext("2d");

    // Drawing chart.
    var chart = new Chart(ctx).Line(chartData, chartOptions);
  }

  function updateHistogramWidget(data, canvas, name, valueSpan) {
    // Updating chart values.
    var labels = [];
    var values = [];
    for (i = 0; i < data.length; ++i) {
      labels.push(data[i]['datetime']);
      values.push(data[i]['value']);
    }
    if (data.length > 0 && valueSpan) {
      valueSpan.html(data[data.length-1]['value']);
      canvas = reinsertCanvas(canvas);
    }

    drawLineGraph(canvas, [{'values': values, 'name': 'All'}], labels, name);
    return {'values': values, 'labels': labels};
  }

  function updateMultipleHistogramWidget(data, canvas, name) {
    if (data && data['datetimes'] == null) {
      return;
    }
    canvas = reinsertCanvas(canvas);
    drawLineGraph(canvas, data['datasets'], data['datetimes'], name);
  }

  function updateTableWidget(data, tableId) {
    if ( ! data['content']) {
      return;
    }

    clearTable(tableId);

    // Adding header
    var header = '<thead>';
    for (var name in data['header']) {
      header += '<th>' + name + '</th>';
    }
    header += '</thead>';
    $("#" + tableId).append(header);

    // Adding content
    var content = '<tbody>';
    for (var row=0; row < data['content'].length; row++) {
      content += '<tr>';
      for (var key in data['content'][row]) {
        content += '<td>' + data['content'][row][key] + '</td>';
      }
      content += '</tr>';
    }
      content += '</tbody>';
    $("#" + tableId).append(content);

  }

  function clearTable(tableId) {
    $("#" + tableId + " tbody").remove();
    $("#" + tableId + " thead").remove();
  }
</script>