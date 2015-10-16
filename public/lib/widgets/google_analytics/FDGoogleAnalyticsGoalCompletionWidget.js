/**
 * @class FDGoogleAnalyticsGoalCompletionWidget
 * --------------------------------------------------------------------------
 * Class function for the GoogleAnalyticsGoalCompletion Widget
 * --------------------------------------------------------------------------
 */
function FDGoogleAnalyticsGoalCompletionWidget(widgetOptions) {
  // Call parent constructor
  FDHistogramWidget.call(this, widgetOptions);
};

FDGoogleAnalyticsGoalCompletionWidget.prototype = Object.create(FDHistogramWidget.prototype);
FDGoogleAnalyticsGoalCompletionWidget.prototype.constructor = FDGoogleAnalyticsGoalCompletionWidget;
