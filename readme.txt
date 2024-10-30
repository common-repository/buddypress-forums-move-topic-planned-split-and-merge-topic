=== BuddyPress Forums - Move Topic (Planned: Split and Merge Topic) ===
Contributors: 3sixty
Version : 0.0.6
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=TTKD2PZSMW3T8
Tags: buddypress, forums, group forums, move topic
Requires at least: 2.9.2
Tested up to: 2.9.2
Stable tag: 0.0.6
Plugin URI: http://buddypress.org/forums/topic/new-plugin-buddypress-group-forums-move-topic 
Author URI: http://buddypress.org/developers/3sixty/
License: GNU General Public License 3.0 (GPL) http://www.gnu.org/licenses/gpl.html

Provides a drop-down on Forum Topic page so Group Admins / Moderators can move topic thread to another forum. Generates email alert to topic author.

== Description ==

Provides a drop-down on Forum Topic page for Group Admins / Moderators to move topic thread to another public, private, or hidden forum. To avoid user confusion, topic author gets email alert with helpful link to the relocated topic. Currently OK for mods/admins to move topic to a forum where they are NOT a mod/admin - may address finer-grained permissions if there is interest. Future considerations include 'split topic' and 'merge topic' functions. 

== Installation ==

Unzip and place the folder in your /wp-content/plugins folder. Activate this through the normal method. 
1. Unzip and upload plugin folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress/WPMU.
1. There is no admin panel. (Limited) configuration options are built right in to the `move topic` drop-down menu.
1. Visit a forum topic page. You should see the drop-down at the top right of the forum topics. It will "learn" your forum structure the first time it loads, and "re-learn" it every time you add a forum.
1. If you delete a forum, rebuild the drop-down index by choosing `-Update Forum List-`.

== Frequently Asked Questions ==

= I have a problem/question/support request. =

Great - please post it here: http://buddypress.org/forums/topic/new-plugin-buddypress-group-forums-move-topic

= I changed my forum name (or group name) but the drop-down isn't picking up all items may become inconsistent or erratic if you change the name of a group or forum.  =

This is a known issue related to a BuddyPress bug. Please post a support request at the URL above.

= I deleted a forum, but it's still in the drop down. =

Choose the last option in the drop-down, `-Update Forum List-`, and it should be resolved. If not, post a support request at the URL above.

== Screenshots ==

None yet.

== Changelog ==

= 0.0.4 through 0.0.6 =
`Alpha` but essentially stable release. Known issue with building forum list may cause problems for people who have renamed their groups and/or forums.

== Arbitrary section ==

Because sometimes you need to be arbitrary.