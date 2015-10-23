/**
 * @class FDNoteWidget
 * --------------------------------------------------------------------------
 * Class function for the NoteWidget
 * --------------------------------------------------------------------------
 */
var FDNoteWidget = function(widgetOptions) {
 // Call parent constructor
 FDGeneralWidget.call(this, widgetOptions)

 this.noteSelector = 'note-data-' + this.options.general.id;

 // Automatically initialize
 this.init();
};

FDNoteWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDNoteWidget.prototype.constructor = FDNoteWidget;

/* -------------------------------------------------------------------------- *
 *                                 FUNCTIONS                                  *
 * -------------------------------------------------------------------------- */
/**
 * @function init
 * --------------------------------------------------------------------------
 * Override parent init
 * @return {this}
 * --------------------------------------------------------------------------
 */
FDNoteWidget.prototype.init = function() {
    this.updateData(window['widgetData' + this.options.general.id]);
    this.draw();

    var that = this;

    $(this.noteSelector).on('change keyup', function () {
        $.ajax({
            type: "POST",
            data: {'text': $(that.noteSelector).val()},
            url: that.widgetData.url
        });
    });
}

/**
 * @function reinit
 * --------------------------------------------------------------------------
 * Override parent reinit
 * @return {this}
 * --------------------------------------------------------------------------
 */
FDNoteWidget.prototype.reinit = function() {
    this.draw(this.widgetData);  
}

/**
 * @function draw
 * Draws the widget
 * --------------------------------------------------------------------------
 * @return {this}
 * --------------------------------------------------------------------------
 */
FDNoteWidget.prototype.draw = function() {
    return this;
}