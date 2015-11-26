/**
 * @class FDChart
 * --------------------------------------------------------------------------
 * Class function for the charts
 * --------------------------------------------------------------------------
 */
function FDChart(widgetOptions) {
  // Private variables
  var options      = widgetOptions;
  var chartCanvas  = new FDChartCanvas(options);
  var chartHandler = new FDChartHandler(options);

  // Debug
  var debug = true;

  // Public functions
  this.draw = draw;

  /**
   * @function draw
   * --------------------------------------------------------------------------
   * Draws the chart
   * @param {string} layout | the chart layout
   * @param {dictionary} data | the chart data
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function draw(layout, data) {
    // Automatically change the layout to the supplied
    updateActiveLayout(layout);

    // Validate data
    if (!validateData(data)) { 
      return this; 
    }

    // Clears the existing chart
    clear();

    // Get Canvas context and validate
    var canvasContext = chartCanvas.get2dContext();
    if (canvasContext) {
      if (debug) {console.log('[S] Canvas exists (' + canvasContext.canvas.width + 'x' + canvasContext.canvas.height + ')')};
    } else {
      if (debug) {console.log('[E] Canvas context doesn\'t exist.')};
      if (debug) {console.log('--- Canvas selector: ' + options.selectors.activeLayout)};
      return;
    }

    // Draw chart and validate
    try {
      new Chart(canvasContext, {
        type: chartHandler.getChartType(layout),
        data: chartHandler.transformChartData(data),
        options: chartHandler.getChartOptions(data)
      });
      if (debug) {console.log('[S] Drawing the chart succeeded.')};
    } catch(err) {
      if (debug) {
        console.log('[E] Drawing the chart failed: ');
        console.log(err.message);
        console.log('  - chartHandler.getChartType: ');
        console.log(chartHandler.getChartType(layout));
        console.log('  - chartHandler.transformChartData: ');
        console.log(chartHandler.transformChartData(data));
        console.log('  - chartHandler.getChartOptions(data): ');
        console.log(chartHandler.getChartOptions(data));
      }
    }

    // return
    return this;
  }

  /**
   * @function updateActiveLayout
   * --------------------------------------------------------------------------
   * Updates the active layout selector
   * @param {string} layout | The new layout
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function updateActiveLayout(layout) {
    // Update layout and activeLayout selectors
    options.layout = layout;
    options.selectors.activeLayout = '#widget-layout-' + options.layout + '-' + options.general.id;
    // Propagate update to the subclasses
    chartCanvas.updateActiveLayout(options.layout);
    chartHandler.updateActiveLayout(options.layout);
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
    chartCanvas.reinsert();
  }

  /**
   * @function validateData
   * --------------------------------------------------------------------------
   * Validates the chart data
   * @param {dictionary} data | the chart data
   * @return {boolean} isValid | validity of the data
   * --------------------------------------------------------------------------
   */
  function validateData(data) {
    // Validate data
    isValid = (data && data.datasets && data.datasets.length > 0);
    if (!isValid) {
      if (debug) {console.log('[E] The supplied data is not valid for the chart.')};
      if (debug) {console.log(data)};
    } else {
      if (debug) {console.log('[S] Data validation passed.')};
    }
    // Validate data
    return isValid;
  }

} // FDChart



/**
 * @class FDChartCanvas
 * --------------------------------------------------------------------------
 * Class function for the chart canvas
 * --------------------------------------------------------------------------
 */
function FDChartCanvas(widgetOptions) {
 /* -------------------------------------------------------------------------- *
  *                                 ATTRIBUTES                                 *
  * -------------------------------------------------------------------------- */
  // Private variables
  var options        = widgetOptions;

  // Public functions
  this.reinsert     = reinsert;
  this.get2dContext = get2dContext;
  this.updateActiveLayout = updateActiveLayout;

  /* -------------------------------------------------------------------------- *
   *                                 FUNCTIONS                                  *
   * -------------------------------------------------------------------------- */

  /**
   * @function size
   * --------------------------------------------------------------------------
   * Returns the widget actual size in pixels
   * @return {dictionary} size | The widget size in pixels
   * --------------------------------------------------------------------------
   */
  function size() {
    // Set margins
    if (options.data.page == 'dashboard') {
      widthMargin = 35;
      heightMargin = 20;
    } else if (options.data.page == 'singlestat') {
      widthMargin = 0;
      heightMargin = 20;
    };

    // Return
    return {'width': $(options.selectors.widget).first().width()-widthMargin,
            'height': $(options.selectors.widget).first().height()-heightMargin};
  }

  /**
   * @function get2dContext
   * --------------------------------------------------------------------------
   * Returns the canvas 2d Context
   * @return {dictionary} context | The canvas get2dContext
   * --------------------------------------------------------------------------
   */
  function get2dContext() {
    if ($(options.selectors.activeLayout).find('canvas').length) {
      return $(options.selectors.activeLayout).find('canvas')[0].getContext("2d");
    } else {
      return false;
    };
  }

  /**
   * @function reinsert
   * --------------------------------------------------------------------------
   * Reinserts the canvas with the provided size
   * @param {dictionary} size | The width and height of the new canvas
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function reinsert() {
    // Get the canvas size
    canvasSize = size();
    // Delete current canvas
    $(options.selectors.chartWrapper).empty();
    // Add new canvas
    if (options.data.page == 'dashboard') {
      $(options.selectors.chartWrapper).append('<canvas class="chart" height="' + canvasSize.height +'" width="' + canvasSize.width + '"></canvas>');
    } else if (options.data.page == 'singlestat') {
      $(options.selectors.chartWrapper).append('<canvas class="canvas-auto" height="' + canvasSize.height +'" width="' + canvasSize.width + '"></canvas>');
    };

    // Return
    return this;
  }

  /**
   * @function updateActiveLayout
   * --------------------------------------------------------------------------
   * Updates the active layout selector
   * @param {string} layout | The new layout
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function updateActiveLayout(layout) {
    // Update layout and activeLayout selectors
    options.layout = layout;
    options.selectors.activeLayout = '#widget-layout-' + options.layout + '-' + options.general.id;
    // Return
    return this;
  }

} // FDChartCanvas


/**
 * @class FDChartHandler
 * --------------------------------------------------------------------------
 * Class function to get and transform chart data
 * --------------------------------------------------------------------------
 */
function FDChartHandler(widgetOptions) {
  /* -------------------------------------------------------------------------- *
   *                                 ATTRIBUTES                                 *
   * -------------------------------------------------------------------------- */
  options = widgetOptions;
  // Debug
  debug = true;

  /* -------------------------------------------------------------------------- *
   *                                 FUNCTIONS                                  *
   * -------------------------------------------------------------------------- */
  this.updateActiveLayout = updateActiveLayout;
  this.getChartType       = getChartType;
  this.getChartOptions    = getChartOptions;
  this.transformChartData = transformChartData;

  /**
   * @function updateActiveLayout
   * --------------------------------------------------------------------------
   * Updates the active layout selector
   * @param {string} layout | The new layout
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function updateActiveLayout(layout) {
    // Update layout and activeLayout selectors
    options.layout = layout;
    options.selectors.activeLayout = '#widget-layout-' + options.layout + '-' + options.general.id;
    // Return
    return this;
  }

  /**
   * @function getChartType
   * --------------------------------------------------------------------------
   * Returns the ChartJS chart type string based on the layout
   * @return {string} chartType | the chart type
   * --------------------------------------------------------------------------
   */
  function getChartType() {
    // Initialize chartType
    var chartType = '';
    // Set type based on the layout
    switch (options.layout) {
      // FIXME: REMOVE CHART LAYOUT
      case 'chart':
        chartType = 'line';
        break;
      case 'combined_chart':
        chartType = 'bar';
        break;
      // ENDFIXME
      case 'single-line':
        chartType = 'line';
        break;
      case 'multi-line':
        chartType = 'line';
        break;
      case 'combined-bar-line':
        chartType = 'bar';
        break;
      default:
        if (debug) {console.log('[E] No getChartType behaviour is defined to this layout: ' + options.layout)};
        break;
    }
    // Return
    return chartType;
  }

  /**
   * @function getChartOptions
   * --------------------------------------------------------------------------
   * Returns the options for a chart based on the page and the layout
   * @param {dictionary} data | the raw data (sometimes options are based on data)
   * @return {dictionary} chartOptions | the chart options
   * --------------------------------------------------------------------------
   */
  function getChartOptions(data) {
    // Set options for all cases
    var chartOptions = { 
      tooltips: {
        enabled: false,
        custom: customTooltip
      }
    };

    // Set options based on page and layout
    switch(options.data.page) {
      case 'singlestat':
        switch (options.layout) {
          // FIXME: REMOVE CHART LAYOUT
          case 'chart':
            $.extend(chartOptions, getChartOptionsSingleStat(data));
            break;
          case 'combined_chart':
            $.extend(chartOptions, getCombinedBarLineOptionsSingleStat(data));
            break;
          //ENDFIXME
          case 'single-line':
            $.extend(chartOptions, getSingleLineOptionsSingleStat(data));
            break;
          case 'multi-line':
            $.extend(chartOptions, getMultiLineOptionsSingleStat(data));
            break;
          case 'combined-bar-line':
            $.extend(chartOptions, getCombinedBarLineOptionsSingleStat(data));
            break;
          default:
            if (debug) {console.log('[E] No getChartOptions behaviour is defined to this layout: ' + options.layout)};
            break;
        }
        break;
      case 'dashboard':
      default:
        switch (options.layout) {
          // FIXME: REMOVE CHART LAYOUT
          case 'chart':
            $.extend(chartOptions, getChartOptionsDashboard(data));
            break;
          case 'combined_chart':
            $.extend(chartOptions, getCombinedBarLineOptionsDashboard(data));
            break;
          // ENDFIXME
          case 'single-line':
            $.extend(chartOptions, getSingleLineOptionsDashboard(data));
            break;
          case 'multi-line':
            $.extend(chartOptions, getMultiLineOptionsDashboard(data));
            break;
          case 'combined-bar-line':
            $.extend(chartOptions, getCombinedBarLineOptionsDashboard(data));
            break;
          default:
            if (debug) {console.log('[E] No getChartOptions behaviour is defined to this layout: ' + options.layout)};
            break;
        }
        break;
    }
    // Return
    return chartOptions;
  }

  /**
   * @function transformChartData
   * --------------------------------------------------------------------------
   * Transforms the chart data based on the page and layout
   * @param {dictionary} data | the raw data to be transformed
   * @return {dictionary} transformedData | the transformed data
   * --------------------------------------------------------------------------
   */
  function transformChartData(data) {
    // Initialize transformedData
    var transformedData = []

    // Transform data based on page and layout
    switch(options.data.page) {
      case 'singlestat':
        switch (options.layout) {
          // FIXME: REMOVE CHART LAYOUT
          case 'chart':
            transformedData = transformChartDataSingleStat(data);
            break;
          case 'combined_chart':
            transformedData = transformCombinedBarLineDataSingleStat(data);
            break;
          // ENDFIXME
          case 'single-line':
            transformedData = transformSingleLineDataSingleStat(data);
            break;
          case 'multi-line':
            transformedData = transformMultiLineDataSingleStat(data);
            break;
          case 'combined-bar-line':
            transformedData = transformCombinedBarLineDataSingleStat(data);
            break;
          default:
            if (debug) {console.log('[E] No transformChartData behaviour is defined to this layout: ' + options.layout)};
            break;
        }
        break;
      case 'dashboard':
      default:
        switch (options.layout) {
          // FIXME: REMOVE CHART LAYOUT
          case 'chart':
            transformedData = transformChartDataDashboard(data);
            break;
          case 'combined_chart':
            transformedData = transformCombinedBarLineDataDashboard(data);
            break;
          //ENDFIXME
          case 'single-line':
            transformedData = transformSingleLineDataDashboard(data);
            break;
          case 'multi-line':
            transformedData = transformMultiLineDataDashboard(data);
            break;
          case 'combined-bar-line':
            transformedData = transformCombinedBarLineDataDashboard(data);
            break;
          default:
            if (debug) {console.log('[E] No transformChartData behaviour is defined to this layout: ' + options.layout)};
            break;
        }
        break;
    }
    // Return
    return transformedData;
  }

  /* -------------------------------------------------------------------------- *
   *                            OPTIONS | DASHBOARD                             *
   * -------------------------------------------------------------------------- */

  /**
   * @function getChartOptionsDashboard
   * @FIXME: RENAME THIS FUNCTION TO SINGLELINE
   * --------------------------------------------------------------------------
   * Returns the chart options for the dashboard page
   * @param {dictionary} data | the raw data
   * @return {dictionary} chartOptions | Dictionary with the options
   * --------------------------------------------------------------------------
   */
  function getChartOptionsDashboard(data) {
    // Create default options
    var chartOptions = {
      scales: {
        xAxes: [{display: false }],
        yAxes: [{display: false, type: 'linear', id: "y-axis-1"}]
      }
    }
    // In case of one point datasets, unshift an extra label and change options
    if (data.datasets[0].values.length == 1) {
      data.labels.unshift(data.labels[0]);
      jQuery.extend(chartOptions, {pointDotStrokeWidth : 1, pointDotRadius : 3,})
    }
    //Return
    return chartOptions;
  }

  /**
   * @function getSingleLineOptionsDashboard
   * --------------------------------------------------------------------------
   * Returns the single-line chart options for the dashboard page
   * @param {dictionary} data | the raw data
   * @return {dictionary} chartOptions | Dictionary with the options
   * --------------------------------------------------------------------------
   */
  function getSingleLineOptionsDashboard(data) {
    return getChartOptionsDashboard(data);
  }

  /**
   * @function getMultiLineOptionsDashboard
   * --------------------------------------------------------------------------
   * Returns the multi-line chart options for the dashboard page
   * @param {dictionary} data | the raw data
   * @return {dictionary} chartOptions | Dictionary with the options
   * --------------------------------------------------------------------------
   */
  function getMultiLineOptionsDashboard(data) {
    return getChartOptionsDashboard(data);
  }

  /**
   * @function getCombinedBarLineOptionsDashboard
   * --------------------------------------------------------------------------
   * Returns the combined-bar-line chart options for the dashboard page
   * @param {dictionary} data | the raw data
   * @return {dictionary} chartOptions | Dictionary with the options
   * --------------------------------------------------------------------------
   */
  function getCombinedBarLineOptionsDashboard(data) {
    // Create default options
    var chartOptions = {
      scales: {
        xAxes: [{ display: false }],
        yAxes: [
          {display: false, type: 'linear', id: "y-axis-1"},
          {display: false, type: 'linear', id: "y-axis-2", position: 'right'}
        ]
      }
    }
    // In case of one point datasets, unshift an extra label and change options
    if (data.datasets[0].values.length == 1) {
      data.labels.unshift(data.labels[0]);
      jQuery.extend(chartOptions, {pointDotStrokeWidth : 1, pointDotRadius : 3,})
    }
    //Return
    return chartOptions;
  }

  /* -------------------------------------------------------------------------- *
   *                           TRANSFORM | DASHBOARD                            *
   * -------------------------------------------------------------------------- */

  /**
   * @function transformChartDataDashboard
   * @FIXME: RENAME THIS FUNCTION TO SINGLELINE
   * --------------------------------------------------------------------------
   * Transforms the raw data to chart layout for the dashboard page
   * @param {dictionary} data | the raw data
   * @return {dictionary} transformedData | the transformed data
   * --------------------------------------------------------------------------
   */
  function transformChartDataDashboard(data) {
    // Initialize transformedData
    var transformedData = {
      labels  : data.labels,
      datasets: [],
    };

    // On one point dataset, unshift an extra value.
    for (var i = data.datasets.length - 1; i >= 0; i--) {
      if (data.datasets[i].values.length == 1) {
        data.datasets[i].values.unshift(data.datasets[i].values[0]);
      };
    };

    // Iterate through the datasets and transform
    for (var i = data.datasets.length - 1; i >= 0; i--) {
      transformedData.datasets.push(
          transform(
            data.datasets[i].values, 
            data.datasets[i].name, 
            data.datasets[i].color
          )
      );
    }

    // Return
    return transformedData;

    // Helper function to transform
    function transform(values, name, color) {
      return {
        type: "line",
        label: name,

        yAxisID: "y-axis-1",
        
        fill: false,
        backgroundColor: "rgba(" + color + ", " + "0.2" + ")",
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
      };

      return transformedObject;
    }
  }

  /**
   * @function transformSingleLineDataDashboard
   * --------------------------------------------------------------------------
   * Transforms the raw data to single-line layout for the dashboard page
   * @param {dictionary} data | the raw data
   * @return {dictionary} transformedData | the transformed data
   * --------------------------------------------------------------------------
   */
  function transformSingleLineDataDashboard(data) {
    return transformChartDataDashboard(data);
  }

  /**
   * @function transformMultiLineDataDashboard
   * --------------------------------------------------------------------------
   * Transforms the raw data to multi-line layout for the dashboard page
   * @param {dictionary} data | the raw data
   * @return {dictionary} transformedData | the transformed data
   * --------------------------------------------------------------------------
   */
  function transformMultiLineDataDashboard(data) {
    return transformChartDataDashboard(data);
  }

  /**
   * @function transformCombinedBarLineDataDashboard
   * --------------------------------------------------------------------------
   * Transforms the raw data to combined-bar-line layout for the dashboard page
   * @param {dictionary} data | the raw data
   * @return {dictionary} transformedData | the transformed data
   * --------------------------------------------------------------------------
   */
  function transformCombinedBarLineDataDashboard(data) {
    // Initialize transformedData
    var transformedData = {
      labels  : data.labels,
      datasets: [],
    };

    // On one point dataset, unshift an extra value.
    for (var i = data.datasets.length - 1; i >= 0; i--) {
      if (data.datasets[i].values.length == 1) {
        data.datasets[i].values.unshift(data.datasets[i].values[0]);
      };
    };

    // Iterate through the datasets and transform
    for (var i = data.datasets.length - 1; i >= 0; i--) {
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
      var alpha   = (type == 'bar') ? 0.8 : 0.2;
      var yAxisID = (type == 'bar') ? 'y-axis-1' : 'y-axis-2';

      var transformedObject = {
        type: type,
        label: name,

        yAxisID: yAxisID,
        
        fill: false,
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
      };

      return transformedObject;
    }
  }

  /* -------------------------------------------------------------------------- *
   *                            OPTIONS | SINGLESTAT                            *
   * -------------------------------------------------------------------------- */

   /**
    * @function getChartOptionsSingleStat
    * @FIXME: RENAME THIS FUNCTION TO SINGLELINE
    * --------------------------------------------------------------------------
    * Returns the chart options for the single stat page
    * @param {dictionary} data | the raw data
    * @return {dictionary} chartOptions | Dictionary with the options
    * --------------------------------------------------------------------------
    */
  function getChartOptionsSingleStat(data) {
    // Create default options
    var chartOptions = {
      scales: {
        xAxes: [{display: true }],
        yAxes: [
          {display: true, type: 'linear', id: "y-axis-1"},
        ]
      },
      tooltipTemplate: function (d) {
        if (d.label) { return d.label + ': ' + d.value; }
        else { return d.value; };
      },
      multiTooltipTemplate: function (d) {
        if (d.datasetLabel) { return d.datasetLabel + ': ' + d.value;} 
        else { return d.value; };
      }
    };
    //Return
    return chartOptions;
  }

  /**
   * @function getSingleLineOptionsSingleStat
   * --------------------------------------------------------------------------
   * Returns the single-line chart options for the dashboard page
   * @param {dictionary} data | the raw data
   * @return {dictionary} chartOptions | Dictionary with the options
   * --------------------------------------------------------------------------
   */
  function getSingleLineOptionsSingleStat(data) {
    return getChartOptionsSingleStat(data);
  }

  /**
   * @function getMultiLineOptionsSingleStat
   * --------------------------------------------------------------------------
   * Returns the multi-line chart options for the dashboard page
   * @param {dictionary} data | the raw data
   * @return {dictionary} chartOptions | Dictionary with the options
   * --------------------------------------------------------------------------
   */
  function getMultiLineOptionsSingleStat(data) {
    return getChartOptionsSingleStat(data);
  }

  /**
   * @function getCombinedBarLineOptionsSingleStat
   * --------------------------------------------------------------------------
   * Returns the chart options for the single stat page
   * @param {dictionary} data | the raw data
   * @return {dictionary} chartOptions | Dictionary with the options
   * --------------------------------------------------------------------------
   */
  function getCombinedBarLineOptionsSingleStat(data) {
    // Create default options
    var chartOptions = {
      scales: {
        xAxes: [{display: true }],
        yAxes: [
          {display: true, type: 'linear', id: "y-axis-1"},
          {display: true, type: 'linear', id: "y-axis-2", position: 'right'}
        ]
      },
      tooltipTemplate: function (d) {
        if (d.label) { return d.label + ': ' + d.value; }
        else { return d.value; };
      },
      multiTooltipTemplate: function (d) {
        if (d.datasetLabel) { return d.datasetLabel + ': ' + d.value;} 
        else { return d.value; };
      }
    };
    //Return
    return chartOptions;
  }

  /* -------------------------------------------------------------------------- *
   *                           TRANSFORM | SINGLESTAT                           *
   * -------------------------------------------------------------------------- */

  /* -------------------------------------------------------------------------- *
   *                              OTHER FUNCTIONS                               *
   * -------------------------------------------------------------------------- */
  function customTooltip(tooltip) {
    // Tooltip Element
    var tooltipEl = $('#chartjs-tooltip');

    if (!tooltipEl[0]) {
      $('body').append('<div id="chartjs-tooltip"></div>');
      tooltipEl = $('#chartjs-tooltip');
    }

    // Hide if no tooltip
    if (!tooltip._view.opacity) {
      tooltipEl.css({
        opacity: 0
      });
      $('.chartjs-wrap canvas').each(function(index, el) {
        $(el).css('cursor', 'default');
      });
      return;
    }

    // Set caret Position
    tooltipEl.removeClass('above below no-transform');
    if (tooltip._view.yAlign) {
      tooltipEl.addClass(tooltip._view.yAlign);
    } else {
      tooltipEl.addClass('no-transform');
    }

    // Set Text
    if (tooltip._view.text) {
      tooltipEl.html(tooltip._view.text);
    } else if (tooltip._view.labels) {
      var innerHtml = '<div class="title">' + tooltip._view.title + '</div>';

      // Sort
      var colors = [];
      var labels = [];
      var numbers = [];
      if(tooltip._view.labels.length>0) {
        colors.push(tooltip._view.legendColors[0].fill);
        labels.push(tooltip._view.labels[0]);
        numbers.push(parseFloat(labels[0].split(' ')[1]));
        for (var i=1; i<tooltip._view.labels.length; i++) {
          var number = parseFloat(tooltip._view.labels[i].split(' ')[1]);
          var sorted = false;
          for (var j=0; j<i; j++) {
            if(!sorted && number>numbers[j]) {
              colors.splice(j, 0, tooltip._view.legendColors[i].fill);
              labels.splice(j, 0, tooltip._view.labels[i]);
              numbers.splice(j, 0, number);
              sorted = true;
            }
          }
          if(!sorted) {
            colors.push(tooltip._view.legendColors[i].fill);
            labels.push(tooltip._view.labels[i]);
            numbers.push(number);
          }
        }
      }      

      for (var i=0; i<tooltip._view.labels.length; i++) {
        innerHtml += [
          '<div class="section">',
          '   <span class="chartjs-tooltip-key" style="background-color:' + colors[i] + '"></span>',
          '   <span class="chartjs-tooltip-value">' + labels[i] + '</span>',
          '</div>'
        ].join('');
      }
      tooltipEl.html(innerHtml);
    }

    // Find Y Location on page
    var top = 0;
    if (tooltip._view.yAlign) {
      if (tooltip._view.yAlign == 'above') {
        top = tooltip._view.y - tooltip._view.caretHeight - tooltip._view.caretPadding;
      } else {
        top = tooltip._view.y + tooltip._view.caretHeight + tooltip._view.caretPadding;
      }
    }

    var offset = $(tooltip._chart.canvas).offset();

    // Display, position, and set styles for font
    tooltipEl.css({
      opacity: 1,
      width: tooltip._view.width ? (tooltip._view.width + 'px') : 'auto',
      left: offset.left + tooltip._view.x + 'px',
      top: offset.top + top + 'px',
      fontFamily: tooltip._view._fontFamily,
      fontSize: tooltip._view.fontSize,
      fontStyle: tooltip._view._fontStyle,
      padding: tooltip._view.yPadding + 'px ' + tooltip._view.xPadding + 'px',
    });
  }

} // FDChartHandler
