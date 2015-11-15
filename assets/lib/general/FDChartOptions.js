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
    var options;

    if (page == 'dashboard') {
      options = getLineChartOptionsDashboard(singlePointOptions);
    } else if (page == 'singlestat') {
      options = getLineChartOptionsSingleStat();
    }

    $.extend(options, { tooltips: {
      enabled: false,
      custom: customTooltip
    }});

    return options;
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
    Chart.defaults.global.responsive              = false;
    Chart.defaults.global.animation.duration      = 0;

    
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

} // FDChartOptions
