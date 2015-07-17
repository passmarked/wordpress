<?php

// protect the GATES !
defined( 'ABSPATH' ) or die( 'Umm... You saw nothing ...' );

/**
* Function that handles when report is saved/updated to start a new test
**/
function passmarked_action_post_update($post_id) {

	// If this is just a revision, don't send the email.
	if ( wp_is_post_revision( $post_id ) )
		return;

	// get the details on the post
	$post_title = get_the_title( $post_id );
	$post_url = get_permalink( $post_id );

	// start the post params obj that we will be saving
	$test_params = Array(

		'title' 		=> $post_title,
		'url' 			=> $post_url,
		'postid' 		=> $post_id,

	);

	// start a test for this report
	$response_str = passmarked_start_test( $post_url );

	// set the response we got from the API
	$test_params['response'] = $response_str;

	// try to parse
	try {

		// parse out json
		$passmarked_response_obj = json_decode($response_str)

	} catch(Exception $err) { return; }

	// update details on the response
	$test_params['message'] = $passmarked_response_obj['message'];

	// if a report was started save those details
	if( $passmarked_response_obj['id'] != NULL ) {

		// save them
		$test_params['status'] 				= 20; // set as busy
		$test_params['reportid']  			= $passmarked_response_obj['id'];
		$test_params['tests']  				= implode(',', $passmarked_response_obj['tests']);
		$test_params['domain']  			= $passmarked_response_obj['domain'];
		$test_params['score']  				= $passmarked_response_obj['score'];
		$test_params['status']  			= $passmarked_response_obj['status'];
		$test_params['result']  			= $passmarked_response_obj['result'];
		$test_params['url']  				= $passmarked_response_obj['url'];

	} else {

		// set as a failed try ...
		$test_params['status'] = 0;

	}

	// save in database
	passmarked_db_save_test( NULL, $test_params );

}

/**
* Returns the parsed url using our placeholder synxtax
**/
function passmarked_parse_url($url) {

	// add in our replacements
	$url = str_replace('{api.passmarked.com}', PASSMARKED_API_BASE, $url);
	$url = str_replace('{passmarked.com}', PASSMARKED_WEB_BASE, $url);

	// return it
	return $url;

}

/**
* Creates a generate curl channel we can use and specialize in requests
**/
function passmarked_get_curl($uri, $headers=NULL) {

	// add in api replacements if any
	$url = passmarked_parse_url($uri);

	//open connection
	$ch = curl_init();

	//set the url, number of POST vars, POST data
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, PASSMARKED_HTTP_TIMEOUT);

	// add headers if any
	if($headers != NULL)
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	// return it
	return $ch;

}

/**
* Performs a GET and includes the token if present
**/
function passmarked_do_get($uri, $headers=NULL) {

	// add in api replacements if any
	$url = passmarked_parse_url($uri);

	//open connection
	$ch = passmarked_get_curl($url, $headers);

	//execute post
	$result = curl_exec($ch);

	//close connection
	curl_close($ch);

}

/**
* Performs a POST and includes the token if present
**/
function passmarked_do_post($uri, $form={}, $headers=NULL) {

	// add in api replacements if any
	$url = passmarked_parse_url($uri);

	// build url encoded body string
	$form_str = http_build_query($form);

	//open connection
	$ch = passmarked_get_curl($url, $headers);

	//set the url, number of POST vars, POST data
	curl_setopt($ch, CURLOPT_POST, count($form));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

	//execute post
	$result = curl_exec($ch);

	//close connection
	curl_close($ch);

}

/**
* Starts a test on the Passmarked servers.
* @url - the full url to test
* @return the JSON object returned by the Passmarked web service
**/
function passmarked_start_test($url) {

	// submit the url
	passmarked_do_post('{api.passmarked.com}/submit', Array(

		'url': $url

	))

}