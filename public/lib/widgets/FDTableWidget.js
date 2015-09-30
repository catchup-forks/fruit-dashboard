/**
 * @class FDTableWidget
 * --------------------------------------------------------------------------
 * Class function for the Histogram Widgets
 * --------------------------------------------------------------------------
 */
var FDTableWidget = function(widgetOptions) {
  /* -------------------------------------------------------------------------- *
   *                                 ATTRIBUTES                                 *
   * -------------------------------------------------------------------------- */
  this.options = widgetOptions;
  this.table   = new FDTable(widgetOptions);

  // AutoLoad
  this.init();
}

/* -------------------------------------------------------------------------- *
 *                                 FUNCTIONS                                  *
 * -------------------------------------------------------------------------- */

/**
  * @function init
  * Automatically initializes the widget
  * --------------------------------------------------------------------------
  * @return {this} 
  * --------------------------------------------------------------------------
  */
FDTableWidget.prototype.init = function() {
   return this;
};

/**
 * @function refresh
 * Handles the specific refresh procedure to the widget
 * --------------------------------------------------------------------------
 * @return {this} 
 * --------------------------------------------------------------------------
 */
FDTableWidget.prototype.refresh = function(data) {
  //   $("#" + tableId + " tbody").remove();
  //   $("#" + tableId + " thead").remove();
  return this;
}

// function clearTable(tableId) {

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

/* -------------------------------------------------------------------------- *
 *                                   EVENTS                                   *
 * -------------------------------------------------------------------------- */
