<?php

/**
 *	Functions
 *	Owen Mundy owenmundy.com
 */

// remove before making live...
// error_reporting(E_ALL);
// ini_set('display_errors', 'on');



/**
 *	curl() - Download and return remote files
 */
function curl($url){

	// make sure cURL is installed
	if (!function_exists('curl_init')) die('Sorry cURL is not installed!');

	$ch = curl_init();							// create a new cURL resource handle

	// optional options
	curl_setopt($ch, CURLOPT_URL, $url);			// URL to download
    curl_setopt($ch, CURLOPT_REFERER, "");			// set referer
	$user_agents = array('Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.74.9 (KHTML, like Gecko) Version/7.0.2 Safari/537.74.9','Mozilla/5.0 (Windows NT 6.1; WOW64; rv:27.0) Gecko/20100101 Firefox/27.0','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.152 Safari/537.3','Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:27.0) Gecko/20100101 Firefox/27.0','Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36');
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agents[array_rand($user_agents)]); // user agent
    curl_setopt($ch, CURLOPT_HEADER, 0);			// include header in result? (0 = yes, 1 = no)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return (true) or print (false) data?
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);			// timeout in seconds
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	// return data as a string


	$data = curl_exec($ch);		// store the response
	$info = curl_getinfo($ch);	// get info about the response

	if (empty($data)) {
		// some kind of an error happened
		die(curl_error($ch));
	    curl_close($ch); // close cURL handler
	}

	// check for an error
	if(curl_errno($ch)){// || $info['http_code'] == 302){

		print "\nhttp_code: ".$info['http_code'] ."\n";
		curl_close($ch);		// close connection
		return false;
	}


	curl_close($ch);		// close connection
	return $data;			// return data to let calling function write disk
}
// usage
//var_dump(curl("owenmundy.com"));
// or
//$file = curl($url);
//file_put_contents($path, $file);



/**
 * Keep track of total time script takes to run
 *
 * @params	int $start_time UNIX timestamp
 * @return	float
 * @author	Owen Mundy <owenmundy.com>
 */
function time_tracker($start_time)
{
	// determine how much time script is taking
	$m_time = microtime();
	$m_time = explode(" ",$m_time);
	$m_time = $m_time[1] + $m_time[0];

	if ($start_time == NULL){
		// if undefined, return start time
		return $m_time;
	} else {
		// $start_time is defined so figure out end time
		$end_time = $m_time;
		$total_time = ($end_time - $start_time);
		return round($total_time,3);
	}
}

/**
 *	Convert value from one number range to another
 */
function convertRange($old_value,$old_min,$old_max,$new_min,$new_max,$round=2){
	// print ("$old_value,$old_min,$old_max,$new_min,$new_max,$round");
	$old_range = ($old_max - $old_min);
	$new_range = ($new_max - $new_min);
	// print ("$old_range,$new_range");
	if ($old_range == 0 || $new_range == 0) return 0;
	$new_value = ((($old_value - $old_min) * $new_range) / $old_range) + $new_min;
	return round( $new_value ,2 );
}


/**
 *	Nap function for tricking robots while data scraping
 */
function nap($low=0,$high=1,$print=true){
	$seconds = rand( ($low*1000000),($high*1000000) );
	if ($print) print "sleeping ".round($seconds/1000000,2)." seconds\n";
	usleep( $seconds );
}


/**
 * Quit function for command line beep
 */
function quit(){
	// all done bell for terminal
	print exec('afplay /System/Library/Sounds/Purr.aiff ');
	die("\n\n####################### ALL DONE #######################\n\n");
}

?>
