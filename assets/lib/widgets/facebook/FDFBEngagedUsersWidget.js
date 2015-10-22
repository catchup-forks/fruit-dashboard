/**
 * @class FDFacebookEngagedUsersWidget
 * --------------------------------------------------------------------------
 * Class function for the FacebookEngagedUsers Widget
 * --------------------------------------------------------------------------
 */
function FDFacebookEngagedUsersWidget(widgetOptions) {
  // Call parent constructor
  FDHistogramWidget.call(this, widgetOptions);
};

FDFacebookEngagedUsersWidget.prototype = Object.create(FDHistogramWidget.prototype);
FDFacebookEngagedUsersWidget.prototype.constructor = FDFacebookEngagedUsersWidget;

