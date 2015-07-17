<?php

// protect the GATES !
defined( 'ABSPATH' ) or die( 'Umm... You saw nothing ...' );

/**
 * @package Passmarked
 */
/*
Plugin Name: Passmarked
Plugin URI: http://passmarked.com/
Description: Run tests on your website to ensure performance
Version: 0.0.1
Author: Passmarked Inc
Author URI: http://passmarked.com
License: Apache Version 2.0, January 2004
Text Domain: Passmarked
*/

// Define a few constants for us to use
define( 'PASSMARKED_VERSION', '0.0.1' );
define( 'PASSMARKED__MINIMUM_WP_VERSION', '3.2' );
define( 'PASSMARKED__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PASSMARKED__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// service constants
define( 'PASSMARKED_API_BASE', 'https://api.passmarked.com' )
define( 'PASSMARKED_WEB_BASE', 'https://passmarked.com' )
define( 'PASSMARKED_HTTP_TIMEOUT', 5000 )

// set table names
define( 'PASSMARKED_TABLE_RESULTS', 5000 )

// include needed functions
require_once('passmarked.db.php');
require_once('passmarked.func.php');

// setup all our events
add_action( 'save_post', array( $this, 'passmarked_action_post_update' ) );