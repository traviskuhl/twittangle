#!/usr/bin/php
<?php

define("CONFIG_FILE","/home/twittangle.com/config.php");

// get them
$resp = json_decode( curl("http://twitter.com/statuses/mentions.json",array('u'=>'twittangle','p'=>'finger')),true );

// remove
$remove = array();

// loop
foreach ( $resp as $r ) {

	// go for it
	if ( stripos($r['text'],"@twittangle remove") !== false  ) {
		
		// remove them
		$remove[] = $r['user']['screen_name'];
	
	}

}

// no
if ( count($remove) == 0 ) {
	exit;
}

$c = include(CONFIG_FILE);

// info
$info = $c['db'];

// try and connect
$dbh = new mysqli('localhost',$info['user'],$info['pass'],$info['db']);

// do it
foreach ( $remove as $r ) {
	echo "removed {$r}\n";
	$dbh->query("INSERT INTO `block` SET `screen_name` = '".$r."', `timestamp` = '".time()."' ");
}

// curl
function curl($url,$userinfo,$params=false,$headers=false) {

			// add headers
			$headers[] = 'Expect:';
		
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		
			if ( is_array($userinfo) ) {
				curl_setopt($ch, CURLOPT_HTTPAUTH,CURLAUTH_BASIC);
				curl_setopt($ch, CURLOPT_USERPWD, $userinfo['u'].':'.$userinfo['p']);
			}
			if ( $params ) {
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			}
			if ( $headers ) {
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			}
			
			$output = curl_exec($ch);      		
			
			curl_close($ch);
			return $output;	

}

?>