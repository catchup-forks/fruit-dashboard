/**
 * @class FDChart
 * --------------------------------------------------------------------------
 * Class function for the charts
 * --------------------------------------------------------------------------
 */
function FDChart(widgetID) {
  // Private variables
  var canvas = $('#' + widgetID + '-chart')[0];

  // Public functions
  this.draw = draw;

  /**
   * @function createDataSet
   * --------------------------------------------------------------------------
   * Creates a dataset for the chart
   * @return {dictionary} the generated dataset
   * --------------------------------------------------------------------------
   */
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

  /**
   * @function draw
   * --------------------------------------------------------------------------
   * Draws the chart
   * @param {dictionary} chartData | The chart data
   * @param {dictionary} options   | the options for the chart
   * @return {true} 
   * --------------------------------------------------------------------------
   */
  function draw(chartData, options) {
    switch(options.type) {
      case 'line':
      default:
          drawLineChart(chartData, options.chartJSOptions);
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
    // Build data.
    var rawDatasets = data.datasets;
    var transformedData = {
      labels: data.labels,
      datasets: []
    };

    // Transform and push the datasets
    for (i = 0; i < rawDatasets.length; ++i) {
      transformedData.datasets.push(createDataSet(rawDatasets[i].values, rawDatasets[i].name, rawDatasets[i].color));
    }

    // Draw chart.
    new Chart(canvas.getContext("2d")).Line(transformedData, options);
  }

} // FDChart
