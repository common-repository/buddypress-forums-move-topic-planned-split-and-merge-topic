<?php
/*
Plugin Name: BuddyPress Group Forums - Move Topic
Plugin URI: http://buddypress.org/forums/topic/new-plugin-buddypress-group-forums-move-topic
Description: Provides a drop-down on Forum Topic page so Group Admins / Moderators can move topic thread to another forum. Generates email alert to topic author.
Version: 0.0.6
Author: 3sixty
Author URI: http://buddypress.org/developers/3sixty/
*/

/* Only load code that needs BuddyPress to run once BP is loaded and initialized. */
function my_plugin_init() {
    require( dirname( __FILE__ ) . '/buddypress-forums-move-topic.php' );
}
add_action( 'bp_init', 'my_plugin_init' );

/* If you have code that does not need BuddyPress to run, then add it here. */

// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
?>