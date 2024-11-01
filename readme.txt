=== VO Store Locator - WP Store Locator Plugin ===
Contributors: Jurski
Donate link: http://www.vitalorganizer.com/vo-locator-documentation/#donate
Tags: locator, store finder, store locator, store locator map, stores, wordpress locator, zip code, zip code locator, zip code search, zipcode, zipcode locator, zipcode search, business locations, google maps, shop finder, shop locator, shops, shortcode
Requires at least: 3.3
Tested up to: 5.2
Stable tag: 3.3.0

VO Store Locator 3.0 is the most customizable FREE plugin being offered. Our WYSIWYG, drag and drop interface will get your store locator page just the way you like it.

== Description ==
VO Store Locator allows you to add a customizable store locator directory and map to any page of your WordPress Site.

= No other plugin offers these many customizations for FREE! =

= VO Store Locator Features =
* NEW - Integrated with Visual Composer.
* NEW - Editor button allows you to add listings quickly to any page or post.
* Add Unlimited locations to showcase store listings, class locations, job locations, events listing and other locations with the use of Google maps.
* Fully Responsive layout out of the box.
* Show Distance to each Store from Current Location.
* Set default Center for Google Maps. 
* Customize your map with easy WYSIWYG interface.
* Customize the font type, and color of pins and backgrounds.
* Improved Performance with Marker Clusters.
* Find Direction in KM and Miles.
* Ability to tag the listings.
* Filter the listings by tags.
* Set a pin color per tag to differentiate locations.
* Locate address by Zip Code, Town, City or State.
* Customize store listing box appearance according to the website theme.
* Auto location look-up based on where a user is currently located.
* Auto locate co-ordinates, only need to enter an address of the desired place/store.
* Embed listings/maps on page and posts easily using the shortcode [VO-LOCATOR].
* Ability to add turn-by-turn driving directions to the location.
* Add thumbnail to the specific location/store.
* Map zoom and scroll with satellite view.
* Easily turn Map display On/Off.
* Ability to hide address from pubic/users for your special cases where you need to only show closest contact phone and other details.
* Location Management with colored markers.
* and much more

= Available Translations =
* Spanish, Mandarin Chinese, German, French, Italian, Romanian & Portuguese Language Translations available.

= Visit our website for more information =
[WP Store Locator](http://www.vitalorganizer.com/vo-locator-wordpress-store-locator-plugin/) | [Demo](http://www.vitalorganizer.com/vo-locator-demo/) | [Documentation](http://www.vitalorganizer.com/vo-locator-documentation/)

= Getting Started =

https://youtu.be/_6Zm_9hONKo

= PRO ADDON =

= Need more features and customizations? Check-out our PRO Add-On =
* NEW - Add Custom Fields to the Map pop-up.
* NEW - Radius Search
* NEW - Updates from admin plugin panel.
* NEW - Add custom marker icons for each location or per tag.
* New - Visitor Stats Tracking
* Import/Export Listings.
* Update Several listing Co-ordinates in a single click.
* Instances - Setting multiple store locators on different pages with full UI Customization.
* Filtering each store locator by Multiple Tags.
* Add multiple tags to single store listing.
* Bulk Update for Custom marker icons.

[GET THE PRO ADD-ON](http://www.vitalorganizer.com/product/vo-store-locator-pro-add-on/) | [Documentation](http://www.vitalorganizer.com/vo-locator-documentation/)

= Using shortcode in theme template files =

In any case, if you need to add listing within theme template files, add this line of code to your theme template:

`if(function_exists("volocator_func"))
{
    echo volocator_func();
} )`

Or One can even use the line of code mentioned below instead of the above function

`<?php echo do_shortcode( '[VO-LOCATOR]' ); ?>`

== Installation ==
= Plugin =
1. Upload the `vo-locator-the-wp-store-locator` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add your locations through the 'Listings' page in the VO Locator admin area
4. Place the shortcode `[VO-LOCATOR]` in the body of a Page or a Post to display your store locator
5. customize the listing display page and colors through "settings" page in the VO Locator admin area

== Screenshots ==
1. Demo1: Showcase of VO Store Locator listings in Desktop.
2. Demo1: Showcase of VO Store Locator listings in Tablet.
3. Demo1: Showcase of VO Store Locator listings in Mobile.
4. Manage Locations: Easily Manage a Few or Many Locations, Sortable by Name, Street, City, zip, etc. 
5. Add Locations: Once You Add a Location, it is Automatically Given Coordinates
6. Customizer: Choose the Important Options For the Look & Feel of Your store locator


== Frequently Asked Questions ==
[Check documentation](http://www.vitalorganizer.com/vo-locator-documentation/) for the most updated information

= Is VO Store Locator displayed across various devices with the perfect layout? =
* Yes, VO Store Locator is fully responsive and work across various devices

= Can I disable the map view for my store locator in any case? =
* Yes, you can enable or disable the map view display through 'settings' page in VO Locator admin area

= Can I display the VO Store Locator in a page template instead of using a shortcode in a Page or a Post? =
* Yes, in your page template, add the code:
`if(function_exists("volocator_func"))
{
    echo volocator_func();
} )`
*Or One can even use the line of code mentioned below instead of the above function `<?php echo do_shortcode( '[VO-LOCATOR]' ); ?>`

== changelog ==

= 3.3.0 =
* Added Custom CSS Option

= 3.2.15 =
* Security Fix

= 3.2.14 =
* Compatible with wordpress version 5.0
* Bug Fixes: Admin Notices Warning when debug set true.

= 3.2.13 =
* Bug Fixes: Admin Warnings Fixed.

= 3.2.12 =
* Search by fields for listings in admin.
* Filter admin listings by Tags.
* Set Default Tag Filter for front-end
.
* Listings Loaded Per Request.

= 3.2.11 =
* Bug Fixes: Visual Builder Shortcode issues
.
* Enhancements: Added setting for zipcode search text.

= 3.2.10 =
* Bug Fixes: Geo Location Enhancement.

= 3.2.9 =
* Bug Fixes: Map Marker HTTPS Issue Fixed.

= 3.2.8 =
* Bug Fixes: jQuery live function fix.

= 3.2.7 =
* Bug Fixes: Javascript encoding issue fixed.

= 3.2.6 =
* Bug Fixes: Search issue fixed.

= 3.2.5 =
* New - Country field added to add/edit listing form.
* Bug Fixes: Geo-location error fixed.
* Bug Fixes: Custom marker icon issue fixed.

= 3.2.4 =
* Addition: Added new button Add Listing & Close on add listing form on admin side.
* Bug Fixes: Find Address issue fixed on admin side on add/edit forms.
* Bug Fixes: CSS Conflict for Address Search Go button fixed(front-end).

= 3.2.3 =
* Bug Fixes: Admin settings region fix.

= 3.2.2 =
* Media Uploader image adding Issue fixed

.
* API key issue for Address box on add/edit listings forms is fixed.

= 3.2.1 =
* Bug Fixes: Javascript issues fixed.

= 3.2.0 =
* WP Editor button and Visual Composer Elements allow you to quick add maps to your posts or pages.
* Re-designed admin settings makes finding things and making changes easier than ever.
* Updated Google API libraries fix a number of issue with the older Google API keys. We recommend making a new Google API key if yours is older than November 2017.
* Easy "Get a Google Map API Key" link will set all the appropriate libraries needed. Just a few clicks to get a new key generated.
* We tooks steps to deal with themes that also use Google Maps and were loading duplicate libraries.
* Added some feedback on activation/deactivation to help us improve future releases.

= 3.1.2 =
* Bug Fixes: Page load performance fixes.

= 3.1.1 =
* Bug Fixes. Listing Image won't update for some users issue solved.

= 3.1 =
* Set different color pins for each location tags to differentiate your locations on the map.
* Infinite colors to choose for Map Marker.
* The new Pro-Add-On lets you set custom pin images for all your locations.

= 3.0.1 =
* Bug Fixes. Safari browser bug which showed some users Browser geolocation error is fixed in this version.

= 3.0 =
* Full Map Customization with WYSIWYG interface.
* Improved Performance with Marker Clusters.
* Resize Listing & Search box with WYSIWYG interface.
* ALL New UI for Manage Listings.
* Custom fonts to match with your website.
* Customize Marker Info Window.
* Infinte colors to choose for Map Marker.

= 2.3.15 =
* Romanian Language Translation Added

= 2.3.13 =
* Compatibility Update

= 2.3.12 =
* Bug Fixes

= 2.3.9 =
* Bug Fixes & Portuguese Language Translation Added.

= 2.3.8 =
* Bug Fixes: PHP version issue solved.

= 2.3.7 =
* Bug Fixes for location popup in mobile view.

= 2.3.6 =
* Bug Fixes: PHP short tags issue solved

= 2.3.5 =
* French Translation added
* Option added for Tag Filter Label within admin setting panel
* Bug Fixes

= 2.3.4 =
* UI bug fixes and improvements

= 2.3.3 =
* Bug Fixes: Admin warnings which use to appear while adding listings are fixed now.
* KM/Miles setting option which remains to miles even after updating to KM within admin area is now fixed.

= 2.3.2 =
* German & Italian Translations added
* Bug Fixes

= 2.3.1 =
* Insert Google Maps API Key option added to the setting page of the plugin. Some users had issue with Map display, error showed "oops something went wrong", these users need to add Google Maps API key within the settings area of the plugin.
* Show some love, option to show our branding is added within settings panel which is displayed at front-end below the map. One can always visit the setting page and disbale the branding if doesn't wish to show it.

= 2.3 =
* Ability to tag the listings.
* Filter the listings by tags.

= 2.2.5 =
* Map not displaying issue fixed
* Map Clusters issues fixed

= 2.2.4 =
* css bugs addressed

= 2.2.3 =
* Bug fixes
* UI Improvements
* Marker Clusters added for improved performance of Map.

= 2.2.2 =
* Plugin Display name changed in Wordpress Directory.

= 2.2.1 =
* Bug Fixes

= 2.2.0 =
* Regions for Map added in admin(plugin settings page). Please note to select an appropriate region and save settings.

= 2.1.7 =
* Spanish Translation Correction.
* Bug Fixes

= 2.1.6 =
* Added anchor tag to the URL field so the link is clickable.

= 2.1.5 =
* Major bug for some users who faced Geo-code error while adding the address.
* Minor Bug fixes which break some themes.

= 2.1.2 =
* Added Chinese Language 

= 2.1.1 =
* Bug fixed where user unable to get a popup on clicking locations.

= 2.1.0 =
* Ability to show 100 Location Markers on map by default
* Bug fixes: A bug is fixed where user see a blank page when clicking on delete location in the admin panel.

= 2.0 =
* All added locations can be edited from Admin Panel

= 1.1.2 =
* Added Languages Support: Included Spanish Language.
* Added feature to switch distance in Kilometers/Miles.
* Small bug fixes

= 1.1.1 =
* Bug Fixes: Small bug with default map center feature fixed.

= 1.1 =
* Added Feature: Map centering based on any default location from your listing.
* Settings Panel: Search box heading text within settings panel can be left empty if a user wishes to hide that text.
* Bug Fixes: Fixed a bug Improving CSS where users face layout theme compatibility problems with their store locator page display.

= 1.0.2 =
* Bug Fixes: Fixed a bug where users see a white blank screen on add listing.

= 1.0.1 =
* Bug Fixed: A bug fixed where a user can't activate the plugin due to a fatal error.