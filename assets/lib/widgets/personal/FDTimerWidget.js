/**
 * @class FDTimerWidget
 * --------------------------------------------------------------------------
 * Class function for the Timer Widget
 * --------------------------------------------------------------------------
 */
var FDTimerWidget = function(widgetOptions) {
    // Call parent constructor
    FDGeneralWidget.call(this, widgetOptions)

    this.timerSelector = '#timer-time-' + this.options.general.id;
    this.startSelector = '#start-' + this.options.general.id;
    this.resetSelector = '#reset-' + this.options.general.id;

    this.running = false;

    // Automatically initialize
    this.init();
};

FDTimerWidget.prototype = Object.create(FDGeneralWidget.prototype);
FDTimerWidget.prototype.constructor = FDTimerWidget;

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
FDTimerWidget.prototype.init = function() {
    this.updateData(window['widgetData' + this.options.general.id]);
    this.draw();

    var that = this;

    $(this.startSelector).click(function() {
        that.running = true;
        $(that.startSelector).hide();
        $(that.resetSelector).show();
        that.startTimer();
    });

    $(this.resetSelector).click(function() {
        that.reset();
    });
}

/**
 * @function reinit
 * --------------------------------------------------------------------------
 * Override parent reinit
 * @return {this}
 * --------------------------------------------------------------------------
 */
FDTimerWidget.prototype.reinit = function() {
    this.draw();  
}

/**
 * @function draw
 * Draws the widget
 * --------------------------------------------------------------------------
 * @return {this}
 * --------------------------------------------------------------------------
 */
FDTimerWidget.prototype.draw = function() {
    this.reset();
    return this;
}

/**
 * @function reset
 * Reset the timer
 * --------------------------------------------------------------------------
 * @return {null}
 * --------------------------------------------------------------------------
 */
FDTimerWidget.prototype.reset = function() {
    this.running = false;
    $(this.timerSelector).html(this.widgetData.countdown);
    $(this.startSelector).show();
    $(this.resetSelector).hide();
}

/**
 * @function startTimer
 * Start the timer
 * --------------------------------------------------------------------------
 * @return {null}
 * --------------------------------------------------------------------------
 */
FDTimerWidget.prototype.startTimer = function() {
    var that = this;
    
    function countdown() {
        if (!that.running) {
            return;
        }
        seconds = $(that.timerSelector).html();
        seconds = parseInt(seconds, 10);
        if (seconds == 1) {
            return;
        }
        seconds --;
        $(that.timerSelector).html(seconds);

        setTimeout(countdown, 1000);
    }

    countdown();
}

