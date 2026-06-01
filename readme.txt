=== WP Cron Inspector Lite ===
Contributors: nariyanto
Tags: cron, wp-cron, debugging, support, admin
Requires at least: 6.0
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Find duplicate, stuck, and suspicious WordPress cron events before they cause support issues.

== Description ==

WP Cron Inspector Lite is a read-only admin utility for support and debugging workflows. It helps you inspect scheduled WP-Cron events, identify duplicate hooks, flag unusually frequent events, and spot overdue jobs.

Initial scope:

* Scheduled event list.
* Duplicate hook detection.
* Unusually frequent recurring event detection.
* Overdue event count.
* Copy support report button.
* Read-only default behavior.

No destructive cleanup actions are included in v0.1.0.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`.
2. Activate **WP Cron Inspector Lite** from the Plugins screen.
3. Open **Tools → Cron Inspector**.

== Frequently Asked Questions ==

= Does this delete cron events? =

No. Version 0.1.0 is read-only.

= Why does it flag frequent hooks? =

Any recurring event with an interval shorter than one hour is highlighted for review. Some frequent events are legitimate, but they are useful to inspect during support troubleshooting.

== Changelog ==

= 0.1.0 =
* Initial development version.
