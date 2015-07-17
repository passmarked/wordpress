<?php

// protect the GATES !
defined( 'ABSPATH' ) or die( 'Umm... You saw nothing ...' );

/**
* Tries to create the database schema to use
**/
function passmarked_db_init() {

	// use the global database object
	global $wpdb;

	// use the charset
	$charset_collate = $wpdb->get_charset_collate();

	// name of the table
	$table_name = $wpdb->prefix . PASSMARKED_TABLE_RESULTS

	// build sql to run it
	$sql = "CREATE TABLE $table_name (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	  lastupdated datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	  UNIQUE KEY id (id)
	) $charset_collate;";

	// include for the delta function
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	// perform the delta
	dbDelta( $sql );

}

/**
* Returns the last 5 pending tests to check status off in our cron job
**/
function passmarked_db_result_get_pending_checks($reportid) {

	// use the global database object
	global $wpdb;

	// name of the table
	$table_name = $wpdb->prefix . PASSMARKED_TABLE_RESULTS

	// return
	return $wpdb->get_results( 

		"SELECT * FROM " . $table_name,
		ARRAY_A

	);

}

/**
* Returns a list of reports that were run for the report
**/
function passmarked_db_result_get_by_reportid($reportid) {

	// use the global database object
	global $wpdb;

	// name of the table
	$table_name = $wpdb->prefix . PASSMARKED_TABLE_RESULTS;

	// return
	return $wpdb->get_row( $wpdb->prepare( 

		'SELECT * FROM ' . $table_name . ' WHERE id=%d', $reportid ), 
		ARRAY_A 

	);

}

/**
* Expires tests that are older than 10 minutes as their probably not
* going to return us any values.
**/
function passmarked_db_result_expire($uid, $params) {

	// use the global database object
	global $wpdb;

	// name of the table
	$table_name = $wpdb->prefix . PASSMARKED_TABLE_RESULTS;

	// prepare our query
	$query_str = $wpdb->prepare( 
		"UPDATE " . $table_name . " 
		SET status=50 
		WHERE created < (NOW() - INTERVAL 10 MINUTE);"
	);
 
	// update
	$wpdb->query( $query_str );

}

/**
* Saves/Updates a new test run
**/
function passmarked_db_result_save($uid, $params) {

	// create the table name
	$table_name = $wpdb->prefix . PASSMARKED_TABLE_RESULTS;

	// check if the uid was given, else insert
	if( is_numeric($uid) ) {

		// update the url
		$wpdb->update( 

			$table_name, 
			$params, 
			array( 'id' => $uid )

		);

	} else {

		// insert
		$wpdb->insert( 

			$table_name, 
			$params 

		);

	}

}