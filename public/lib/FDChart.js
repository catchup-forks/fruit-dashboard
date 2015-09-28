/**
 * @class FDChart
 * --------------------------------------------------------------------------
 * Class function for the charts
 * --------------------------------------------------------------------------
 */
function FDChart(widgetOptions) {
  // Private variables
  var options = widgetOptions;
  var canvas  = new FDCanvas(widgetOptions);
  var chartOptions = new FDChartOptions(widgetOptions.page)
  var chartData = null;
  
  // Public functions
  this.draw       = draw;
  this.updateData = updateData;

  /**
   * @function updateData
   * --------------------------------------------------------------------------
   * Updates the chart data
   * @param {dictionary} data | the chart data
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
   * Transforms the data to ChartJS format
   * @param {dictionary} rawData | the chart data
   * @return {this} stores the transformed data
   * --------------------------------------------------------------------------
   */
  function transformData(rawData) {
    var processedData = {
      labels  : [],
      datasets: [
        {
          values: [],
          name:   widgetOptions.name,
          color: '105 ,153, 209'
        }
      ],
    };

    // Transform raw db data (rawData->processedData)
    if (!("datasets" in rawData)) {
      for (i = 0; i < rawData.length; ++i) {
        processedData.labels.push(rawData[i]['datetime']);
        processedData.datasets[0].values.push(rawData[i]['value']);
      }
    } else {
      processedData = rawData;
    }

    // Transform processedData (processedData->transformedData)
    var transformedData = {
      labels  : processedData.labels,
      datasets: [],
    };

    for (i = 0; i < processedData.datasets.length; ++i) {
      transformedData.datasets.push(createDataSet(processedData.datasets[i].values, processedData.datasets[i].name, processedData.datasets[i].color));
    }

    // Store new data
    chartData = transformedData;

    // Return
    return this;
  }

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
   * @param {string} type | the chart type
   * @return {true} 
   * --------------------------------------------------------------------------
   */
  function draw(type) {
    // Reinsert canvas
    canvas.reinsert();

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
    // Build data.
    console.log(data);

    // Draw chart.
    new Chart(canvas.get2dContext()).Line(data, options);
  }



} // FDChart
