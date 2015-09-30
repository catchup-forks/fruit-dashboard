/**
 * @class FDTable
 * --------------------------------------------------------------------------
 * Class function for the tables
 * --------------------------------------------------------------------------
 */
function FDTable(widgetOptions) {
  // Private variables
  var options   = widgetOptions;
  var tableData = null;
  
  // Public functions
  this.draw       = draw;
  this.updateData = updateData;

  /**
   * @function updateData
   * --------------------------------------------------------------------------
   * Updates the table data
   * @param {dictionary} data | the table data
   * @return {this} 
   * --------------------------------------------------------------------------
   */
  function updateData(data) {
    // Transform and store new data
    transformData(data);
    // return
    return this;
  }

  /**
   * @function transformData
   * --------------------------------------------------------------------------
   * Transforms the data to HTML Table format
   * @param {dictionary} rawData | the table data
   * @return {this} stores the transformed data
   * --------------------------------------------------------------------------
   */
  function transformData(rawData) {
    if (rawData.length == undefined) {
      return this;
    }



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

    // Store new data
    tableData = transformedData;

    // Return
    return this;
  }

    //   clearTable(tableId);
  /**
   * @function clear
   * --------------------------------------------------------------------------
   * Creates a dataset for the chart
   * @return {dictionary} the generated dataset
   * --------------------------------------------------------------------------
   */
  function clear(values, name, color) {
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

  /**
   * @function draw
   * --------------------------------------------------------------------------
   * Draws the chart
   * @param {string} type | the chart type
   * @return {true} 
   * --------------------------------------------------------------------------
   */
  function draw(type) {
    // Clear the existing table
    clear();

    // Draw chart
    switch(type) {
      case 'line':
      default:
          drawLineChart(chartData, chartOptions.getLineChartOptions());
          break;
    }

    // return
    return true;
  }

  /**
   * @function drawLineChart
   * --------------------------------------------------------------------------
   * Draws a line chart
   * @param {dictionary} data | The chart data
   * @param {dictionary} options | The chart options
   * @return {true} 
   * --------------------------------------------------------------------------
   */
  function drawLineChart(data, options) {
    // Draw chart.
    new Chart(canvas.get2dContext()).Line(data, options);
  }



} // FDTable
