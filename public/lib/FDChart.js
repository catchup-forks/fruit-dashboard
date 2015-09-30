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
  var widgetData = null;

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
    if (rawData == undefined) {
      processedData = rawData;
    } else if ("datasets" in rawData) {
      processedData = rawData;
    } else {
      for (i = 0; i < rawData.length; ++i) {
        processedData.labels.push(rawData[i]['datetime']);
        processedData.datasets[0].values.push(rawData[i]['value']);
      }
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
    widgetData = transformedData;

    // Return
    return this;
  }

  /**
   * @function clear
   * --------------------------------------------------------------------------
   * Clears the previous chart
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function clear() {
    // Reinsert canvas
    canvas.reinsert();
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
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function draw(type) {
    // Clear the existing chart
    clear();

    // Draw chart
    switch(type) {
      case 'line':
      default:
          new Chart(canvas.get2dContext()).Line(widgetData, chartOptions.getLineChartOptions())
          break;
    }

    // return
    return this;
  }

} // FDChart
