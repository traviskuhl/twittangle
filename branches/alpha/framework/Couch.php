<?php

class Couch {
	
	// host
	const HOST = "http://localhost:5984/";

	// construct
	public function __construct() {
	
		// set some stuff
	
	}

	
	// curl
	public function _curl($uri,$params=false) {
	
		// init
		$ch = curl_init( self::HOST . $uri);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);

		// params
		if ( $params ) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		}
		
		// headers
		if ( $headers ) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
		
		// get it 
		$output = curl_exec($ch);      		
		
		// curl
		curl_close($ch);
		
		
	
	}

}

?>