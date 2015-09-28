// When browser action icon is clicked, open a new tab to Fruit Dashboard.
chrome.browserAction.onClicked.addListener(function() {
  var url = "https://dashboard.tryfruit.com";
  chrome.tabs.create({ url: url });
});