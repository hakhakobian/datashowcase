=== Data Showcase ===
Requires at least: 4.6
Tested up to: 6.4.2
Requires PHP: 7.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Seamlessly integrate with external APIs to dynamically fetch and elegantly display real-time data on your website.

== Key Features ==

*   Connects to external APIs securely.
*   Fetches real-time data for dynamic content.
*   List or Grid view to display the data.

== Installation ==

Install Data Showcase plugin by uploading the ZIP file from the WordPress dashboard.
To install, go to your WordPress dashboard, click on 'Plugins', and choose 'Add New Plugin' then "Upload Plugin".
Choose the plugin ZIP file and click 'Install Now', and after installation, activate the plugin.

After activation the "Data Showcase Options" submenu will be added under the WordPress "Settings" menu,
with the default settings, which you may change any time.
There you can set API Key in case of the API requires authentication, set API URL which default value is the registered
endpoint to fetch mock JSON data from the file included in the plugin folder (sample.json), you can change the URL
with any API URL witch is returning JSON or text data
(e.g. https://filesamples.com/samples/code/json/sample1.json,
 https://filesamples.com/samples/code/json/sample2.json
 https://filesamples.com/samples/code/json/sample3.json
 https://filesamples.com/samples/code/json/sample4.json), and also you can select the data type from from two available
 options (JSON|TEXT).
The type can be easily added by modifying the one line of the code.

The fetched data can be shown in any page or post with inserting the following shortcode:
[TCF_DATA_SHOWCASE format="grid"]
where the format is the layout format: possible value are grid or list.

Also the data can be shown by add "Data Showcase" block in Gutenberg editor and chosen the layout format.
There is no need to register WordPress widget because with the new functional you can add
any Gutenberg block as a widget.

Implemented caching mechanism to reduce load time by storing API responses in DB by API URLs.


== Changelog ==

= 1.0.0 =
* Initial version.