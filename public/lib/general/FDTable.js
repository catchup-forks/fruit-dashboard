/**
 * @class FDTable
 * --------------------------------------------------------------------------
 * Class function for the tables
 * --------------------------------------------------------------------------
 */
function FDTable(widgetSelector) {
  // Private variables
  var selector = widgetSelector + ' table';
  
  // Public functions
  this.draw = draw;

  /**
   * @function draw
   * --------------------------------------------------------------------------
   * Draws the chart
   * @param {dictionary} data | the table data
   * @return {true} 
   * --------------------------------------------------------------------------
   */
  function draw(data, isHistogram) {
    if (data) {
      isHistogram = typeof isHistogram !== 'undefined' ? isHistogram : false;

      // Clear the existing table
      clear();

      if(isHistogram) {
        // Draw table
        $(selector).append('<thead></thead>');
        for(var i=0; i<data.header.length; i++) {
          $(selector + ' thead').append('<th>' + data.header[i] + '</th>');
        }
        $(selector).append('<tbody></tbody>');
        var item, row;
        for(var i=0; i<data.content.length; i++) {
          item = data.content[i];
          row = '';
          for(var j=0; j<item.length; j++) {
            row += '<td>' + item[j] + '</td>';
          }
          $(selector + ' tbody').append('<tr>' + row + '</tr>');
        }
      } else {
        $(selector).append(data.header);
        $(selector).append(data.content);
      }
    }

    // return
    return true;
  }

  /**
   * @function clear
   * --------------------------------------------------------------------------
   * Clears the previous table
   * @return {this}
   * --------------------------------------------------------------------------
   */
  function clear() {
    $(selector + " thead").remove();
    $(selector + " tbody").remove();
  }

} // FDTable
