review-service
================================

Fetch reviews from Google Reviews API and Itunes App Store and post them to Slack (or both!). 

For iOS apps you'll need your application ID. You can normally find this in the App Store url of your app.

For your Android apps the process is a little more involved as requests are authenticated.
1. You'll need permission to access the Google Play Developer Console of your app.
2. See the following documentation on getting access to the Play Store API : 
   https://developers.google.com/android-publisher/getting_started#setting_up_api_access_clients
   
To post to Slack you'll need to set up a personal integration webhook.
See: https://my.slack.com/services/new/incoming-webhook/
   
The icons are optional, but adds spice to the Slack post. 
You can use something like http://www.kristinebarias.com/images/ios_icon.png for example.


