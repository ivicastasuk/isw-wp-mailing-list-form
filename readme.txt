=== ISW WP Mailing List Form ===
Contributors: ivicastasuk
Tags: mailing list, newsletter, email, form, subscription
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.0.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

A simple and customizable mailing list subscription form for WordPress.

== Description ==

ISW WP Mailing List Form is a lightweight plugin that allows you to easily add a mailing list subscription form to your WordPress site. Visitors can subscribe with their name and email address. All entries are stored in a custom database table and can be exported as CSV from the admin dashboard. The form and button are fully customizable via the WordPress admin panel.

**Features:**
* Simple and responsive subscription form
* Customizable input and button styles (colors, padding, border, etc.)
* Customizable success and error messages
* Stores subscribers in a custom database table
* Export subscribers as CSV
* Admin notification for new entries
* Customizable response email to subscribers
* Shortcode support: `[add_isw_ml_form]`

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to the "ISW ML" menu in your WordPress admin to configure the form and view subscribers.
4. Add the shortcode `[add_isw_ml_form]` to any post, page, or widget where you want the form to appear.

== Frequently Asked Questions ==

= How do I display the subscription form? =
Use the shortcode `[add_isw_ml_form]` in any post, page, or widget.

= Where are the subscribers stored? =
Subscribers are stored in a custom database table named `{prefix}isw_ml`.

= How do I export the mailing list? =
Go to the "ISW ML" admin menu and click the "Export as CSV" button.

= Can I customize the form style? =
Yes, you can customize input and button styles, placeholders, and messages from the "Customization" submenu.

= Does the plugin send confirmation emails? =
Yes, you can customize the response email template and subject in the settings.

== Screenshots ==

1. Frontend subscription form
2. Admin dashboard with subscriber list
3. Customization options in the admin panel

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
First public release.

== License ==

This plugin is free software, released under the