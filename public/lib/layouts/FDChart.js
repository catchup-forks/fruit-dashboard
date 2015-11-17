/**
 * @class FDChart
 * --------------------------------------------------------------------------
 * Class function for the charts
 * --------------------------------------------------------------------------
 */
function FDChart(widgetOptions) {
  // Private variables
  var options     = widgetOptions;
  var chartCanvas = new FDChartCanvas(options);
  var chartData   = new FDChartData(options)

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

    // if(this.options.layout=='chart' || this.options.layout == 'multiple') {
    //   if (this.data.isCombined) {
    //     this.chart.draw('combined', this.data);
    //   } else {
    //     this.chart.draw('line', this.data); 
    //   }
    // } else if(this.options.layout=='table') {
    //   this.table.draw(this.data, true);
    // }


    // In case of one point or flat datasets, render with different options.
    var singlePointOptions = {};
    // var start;
    // var min;
    // var max;
    // var steps;

    var isDatasetsExist = data && data.datasets && data.datasets.length>0;

    // If on dashboard and datasets exist.
    if (isDatasetsExist && widgetOptions.data.page == 'dashboard') {
      // In case of one point datasets, unshift an extra label.
      if (data.datasets[0].values.length == 1) {
        data.labels.unshift(data.labels[0]);

        // Build extra options for single point charts.
        singlePointOptions = {
          pointDotStrokeWidth : 1,
          pointDotRadius : 3,
        }
      };

      // For each one point dataset, unshift an extra value.
      for (var i = data.datasets.length - 1; i >= 0; i--) {

        if (data.datasets[i].values.length == 1) {
          data.datasets[i].values.unshift(data.datasets[i].values[0]);
        };

        // If Math operations are needed on datasets, do it here.
        // min = Math.min(data.datasets[i].values);
        // max = Math.max(data.datasets[i].values);
        // if (min == max) {
        //   singlePointOptions = {
        //     showScale : true,
        //     scaleOverride : true,
        //     scaleSteps: Math.ceil((max-start)/step),
        //     scaleStepWidth: step,
        //     scaleStartValue: start,
        //   }
        // };

      };

    };

    // Clear the existing chart
    clear();

    // If datasets exist.
    if(isDatasetsExist) {
      var canvasContext = chartCanvas.get2dContext();
      // Draw chart
      switch(type) {
        case 'line':
        if (canvasContext) {
          new Chart(canvasContext, {
            type: 'line',
            data: chartData.transformLineChartDatasets(data),
            options: chartData.getLineChartOptions(singlePointOptions)
          });
        };
          break;
        case 'combined':
          if (canvasContext) {
            new Chart(canvasContext, {
              type: 'bar',
              data: chartData.transformLineChartDatasets(data),
              options: chartData.getLineChartOptions(singlePointOptions)
            });
          };
          break;
        default:
          if (canvasContext) {
            new Chart(canvasContext, {
              type: 'line',
              data: chartData.transformLineChartDatasets(data),
              options: chartData.getLineChartOptions(singlePointOptions)
            });
          };
          break;
      }
    }

    // return
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
  var widgetSelector = options.selectors.widget;
  var graphSelector  = options.selectors.widget + ' ' + options.selectors.graph;

  // Public functions
  this.reinsert     = reinsert;
  this.get2dContext = get2dContext;

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
    return {'width': $(widgetSelector).first().width()-widthMargin,
            'height': $(widgetSelector).first().height()-heightMargin};
  }

  /**
   * @function get2dContext
   * --------------------------------------------------------------------------
   * Returns the canvas 2d Context
   * @return {dictionary} context | The canvas get2dContext
   * --------------------------------------------------------------------------
   */
  function get2dContext() {
    if ($(graphSelector).find('canvas').length) {
      return $(graphSelector).find('canvas')[0].getContext("2d");
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
    $(graphSelector).empty();
    // Add new canvas
    if (options.data.page == 'dashboard') {
      $(graphSelector).append('<canvas class="chart chart-line" height="' + canvasSize.height +'" width="' + canvasSize.width + '"></canvas>');
    } else if (options.data.page == 'singlestat') {
      $(graphSelector).append('<canvas class="canvas-auto" height="' + canvasSize.height +'" width="' + canvasSize.width + '"></canvas>');
    };

    // Return
    return this;
  }
} // FDChartCanvas


/**
 * @class FDChartData
 * --------------------------------------------------------------------------
 * Class function to get and transform chart data
 * --------------------------------------------------------------------------
 */
function FDChartData(widgetOptions) {
  // Private variables
  options = widgetOptions;
  
  // Public functions
  this.getChartOptions    = getChartOptions
  this.transformChartData = transformChartData

  /**
   * @function getChartOptions
   * --------------------------------------------------------------------------
   * Returns the options for a chart based on the page and the layout
   * @param {string} page | the actual page
   * @param {string} layout | the chart layout
   * @return {dictionary} chartOptions | the chart options
   * --------------------------------------------------------------------------
   */
  function getChartOptions() {
    // Set options for all cases
    var options = { 
      tooltips: {
        enabled: false,
        custom: customTooltip
      }
    });

    // Set options based on page and layout
    switch(options.data.page) {
      case 'singlestat':
        switch (options.layout) {
          // FIXME: REMOVE CHART LAYOUT
          case 'chart':
            $.extend(options, getChartOptionsSingleStat());
            break;
          case 'single-line':
          case 'multi-line':
          case 'combined-bar-line':
          default:
            console.log('[E] No getChartOptions function is defined to this layout: ' + layout)
            break;
        }
        break;
      case 'dashboard':
      default:
        switch (options.layout) {
          // FIXME: REMOVE CHART LAYOUT
          case 'chart':
            $.extend(options, getChartOptionsDashboard());
            break;
          case 'single-line':
          case 'multi-line':
          case 'combined-bar-line':
          default:
            console.log('[E] No getChartOptions function is defined to this layout: ' + layout)
            break;
        }
        break;
    }
  }

  /**
   * @function transformLineChartDatasets
   * --------------------------------------------------------------------------
   * Transforms the chart data based on the page and layout
   * @return {dictionary} chartOptions | the chart options
   * --------------------------------------------------------------------------
   */
   function transformChartData(data) {
      // Transform data based on page and layout
      switch(options.data.page) {
        case 'singlestat':
          switch (options.layout) {
            // FIXME: REMOVE CHART LAYOUT
            case 'chart':
              $.extend(options, transformChartDatasetsSingleStat(data));
              break;
            case 'single-line':
            case 'multi-line':
            case 'combined-bar-line':
            default:
              console.log('[E] No getChartOptions function is defined to this layout: ' + layout)
              break;
          }
          break;
        case 'dashboard':
        default:
          switch (options.layout) {
            // FIXME: REMOVE CHART LAYOUT
            case 'chart':
              $.extend(options, transformChartDatasetsDashboard(data));
              break;
            case 'single-line':
            case 'multi-line':
            case 'combined-bar-line':
            default:
              console.log('[E] No getChartOptions function is defined to this layout: ' + layout)
              break;
          }
          break;
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
   * @function getChartOptionsDashboard
   * --------------------------------------------------------------------------
   * Returns the line chart options for the dashboard page
   * @param {object} type | extra options for single point or flat line charts
   * @return {dictionary} chartOptions | Dictionary with the options
   * --------------------------------------------------------------------------
   */
  function getChartOptionsDashboard(singlePointOptions) {
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


    for (var i = data.datasets.length - 1; i >= 0; i--) {
      transformedData.datasets.push(
          transform(
            data.datasets[i].type,
            data.datasets[i].values, 
            data.datasets[i].name, 
            data.datasets[i].color,
            data.isCombined
          )
      );
    }

    // Return
    return transformedData;

    function transform(type, values, name, color, isCombined) {

      var alpha = 0.2;
      if (type == 'bar') {
        alpha = 0.8;
      };

      var yAxisID = "y-axis-1";

      if (isCombined) {

        yAxisID = "y-axis-2";

        if (type == 'bar') {
          yAxisID = "y-axis-1";
        };
        
      };

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

    $(tooltip._chart.canvas).css('cursor', 'pointer');

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

} // FDChartData
