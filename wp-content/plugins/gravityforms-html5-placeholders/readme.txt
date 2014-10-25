=== Plugin Name ===
Contributors: isoftware
Tags: forms, gravity, gravityforms, html5, wpml, jquery.placeholders.js, placeholders
Requires at least: 3.5
Tested up to: 4.0
Stable tag: 2.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin adds native HTML5 placeholder support to Gravity Forms' fields with javascript fallback.

== Description ==

The plugin extends the default Gravity Forms form editor to support native HTML5 placeholders and label management.

It allows you to define placeholders for a wide variety of build-in fields and to customize field labels & sublabels by either overriding the default with your own and/or allowing you to hide them individually.  

Javascript fallback is used for old browsers that don't support the HTML5 placeholder attribute.

It is fully compatible with WPML for placeholder and label translation using the Gravity Forms Multilingual plugin. 

= Supported Standard Fields =
* Single Line Text
* Paragraph Text
* Number

= Supported Advanced Fields =
* Name
* Email
* Phone
* Website
* Address
* Date
* Time

= Supported Post Fields =
* Title
* Body
* Excerpt
* Tags
* Custom Field

= Supported Pricing Fields =
* Product
* Quantity


= Requirements =
This plugin requires prior installation and activation of [Gravity Forms](http://www.gravityforms.com/) plugin by [Rocketgenius](http://www.rocketgenius.com/) ver. 1.7 and above.

= Tested =
Up to Gravity Forms plugin ver. 1.8.12.6

== Installation ==

1. Download the gravityforms-html5-placeholders.zip file to your local machine.
2. Either use the automatic plugin installer (Plugins - Add New) or Unzip the file and upload the gravityforms-html5-placeholders folder to your /wp-content/plugins/ directory.
3. Activate the plugin through the Plugins menu
4. Visit the Gravity Forms general settings page ( Forms -> Settings ) and make sure that Output HTML5 option is set to yes.
5. Visit the Gravity Forms form editor and add html5 placeholders to your fields.

== Screenshots ==

1. Editing a Simple Name Field
2. Editing an Extended Name Field
3. Editing an Email Field with confirmation
4. Editing an International Address Field

== Changelog ==

= 2.7 =
* Added support for Product and Quantity pricing fields
* Added support for Tags and Custom Field post fields
* Fixed Html Escaped all labels on processing
* Various code optimizations and cleanup

= 2.6 =
* Fixed scripts not loading when Gravity Forms no-conflict mode was enabled
* Added required fields that have hidden labels are now marked with a red asterisk using css background ( .gfield_contains_required.gfield_hidden_label ) to denote that they are required 
* Added 'gform_placeholders_disable_css' filter to conditionally load the css on frontend that depends by default on gravity forms settings output css option
* When Gravity Forms Output HTML5 setting is set to no, field placeholder settings are not rendered in form editor and only label management features are available

= 2.5.2 =
* Fixed incorrect plugin version

= 2.5.1 =
* Fixed fallback script loading error

= 2.5 =
* Added support for Address, Date, Time and Number fields
* Simplified complex fields placeholder and sublabels editor
* Fixed section break field label visibility
* Fixed name field label placeholder not rendering when nameFormat empty
* Fixed field label hidden css to allow for margin
* Added minified versions of scripts 

= 2.4 =
* Added support for Title, Body and Excerpt post fields

= 2.3 =
* Initial WordPress.org Release
