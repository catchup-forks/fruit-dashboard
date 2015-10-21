/**
 * @class FDChartOptions
 * --------------------------------------------------------------------------
 * Class function to set the global chart options
 * --------------------------------------------------------------------------
 */
function FDChartOptions(widgetOptions) {
  // Private variables
  var page = widgetOptions.data.page;
  
  // Public functions
  this.init = init;
  this.getLineChartOptions = getLineChartOptions;
  this.transformLineChartDatasets = transformLineChartDatasets;

  /**
   * @function init
   * --------------------------------------------------------------------------
   * Initializes the FDChartOptions object
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function init() {
    if (page == 'dashboard') {
      setDefaultOptionsDashboard();
    } else if (page == 'singlestat') {
      setDefaultOptionsSingleStat();
    };

    return this;
  }

  /**
   * @function getLineChartOptions
   * --------------------------------------------------------------------------
   * Returns the options for a line chart based on the page
   * @param {object} type | extra options for single point or flat line charts
   * @return {dictionary} chartOptions | the chart options
   * --------------------------------------------------------------------------
   */
  function getLineChartOptions(singlePointOptions) {
    if (page == 'dashboard') {
      return getLineChartOptionsDashboard(singlePointOptions);
    } else if (page == 'singlestat') {
      return getLineChartOptionsSingleStat();
    }
  }

  /**
   * @function transformLineChartDatasets
   * --------------------------------------------------------------------------
   * Returns the options for a line chart based on the page
   * @return {dictionary} chartOptions | the chart options
   * --------------------------------------------------------------------------
   */
  function transformLineChartDatasets(data) {
    if (page == 'dashboard') {
      return transformLineChartDatasetsDashboard(data);
    } else if (page == 'singlestat') {
      // FIXME
      return transformLineChartDatasetsDashboard(data);
    }
  }

  /**
   * @function setDefaultOptionsDashboard
   * --------------------------------------------------------------------------
   * Sets the chart default options for the dashboard page
   * @return {true} 
   * --------------------------------------------------------------------------
   */
  function setDefaultOptionsDashboard() {
    Chart.defaults.global.responsive      = false;
    
    // Return
    return true;
  }

  /**
   * @function getLineChartOptionsDashboard
   * --------------------------------------------------------------------------
   * Returns the line chart options for the dashboard page
   * @param {object} type | extra options for single point or flat line charts
   * @return {dictionary} chartOptions | Dictionary with the options
   * --------------------------------------------------------------------------
   */
  function getLineChartOptionsDashboard(singlePointOptions) {
    var defaultOptions = {
      scales: {
        xAxes: [{
          display: false
        }],
        yAxes: [
          {
            display: false,
            type: 'linear',
            id: "y-axis-1",
          },
          {
            display: false,
            type: 'linear',
            position: 'right',
            id: "y-axis-2",
          }
          ]
      }
    }

    if (singlePointOptions) {
      jQuery.extend(defaultOptions, singlePointOptions);
    };

    return defaultOptions;
  }

  /**
   * @function transformLineChartDatasetsDashboard
   * --------------------------------------------------------------------------
   * Creates a dataset for the chart
   * @param {dictionary} data | The chart data
   * @return {dictionary} the generated dataset
   * --------------------------------------------------------------------------
   */
  function transformLineChartDatasetsDashboard(data) {
    var transformedData = {
      labels  : data.labels,
      datasets: [],
    };

    for (i = 0; i < data.datasets.length; ++i) {
      transformedData.datasets.push(
          transform(
            data.datasets[i].type,
            data.datasets[i].values, 
            data.datasets[i].name, 
            data.datasets[i].color
          )
      );
    }
    // Return
    return transformedData;

    function transform(type, values, name, color) {
      var yAxisID = "y-axis-2";
      var alpha = 0.2;
      if (type == 'bar') {
        yAxisID = "y-axis-1";
        alpha = 0.8;
      };
      return {
        type: type,
        label: name,
        
        fill: false,
        yAxisID: yAxisID,
        backgroundColor: "rgba(" + color + ", " + alpha + ")",
        borderColor: "rgba(" + color + ", 1)",
        
        pointBorderColor: "rgba(" + color + ", 1)",
        pointBackgroundColor: "#fff",
        pointBorderWidth: 1,
        pointHoverRadius: 3,
        pointHoverBackgroundColor: "rgba(" + color + ", 1)",
        pointHoverBorderColor: "rgba(" + color + ", 1)",
        pointHoverBorderWidth: 2,
        
        borderWidth: 2,
        
        hoverBackgroundColor: "rgba(" + color + ", 1)",
        hoverBorderColor: "rgba(" + color + ", 1)",

        data: values
      }
    }
  }

  /**
   * @function setDefaultOptionsSingleStat
   * --------------------------------------------------------------------------
   * Sets the chart default options for the single stat page
   * @return {true} 
   * --------------------------------------------------------------------------
   */
  function setDefaultOptionsSingleStat() {
    Chart.defaults.global.responsive      = false;
    
    // Return
    return true;
  }

  /**
   * @function getLineChartOptionsSingleStat
   * --------------------------------------------------------------------------
   * Returns the line chart options for the single stat page
   * @return {dictionary} chartOptions | Dictionary with the options
   * --------------------------------------------------------------------------
   */
  function getLineChartOptionsSingleStat() {
    return {
      scales: {
        xAxes: [{
          display: true
        }],
        yAxes: [
        {
          display: true,
          type: 'linear',
          id: "y-axis-1",
        },
        {
          display: true,
          type: 'linear',
          position: 'right',
          id: "y-axis-2",
        }
        ]
      },
      tooltipTemplate: function (d) {
        if (d.label) {
          return d.label + ': ' + d.value;
        } else {
          return d.value;
        };
      },
      multiTooltipTemplate: function (d) {
        if (d.datasetLabel) {
          return d.datasetLabel + ': ' + d.value;
        } else {
          return d.value;
        };
      }
    };
  }

} // FDChartOptions
