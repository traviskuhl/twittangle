<?php

	// figure the file 
	$f = $_GET['f'];
	$t = $_GET['t'];
	$v = $_GET['v'];
	$a = (array_key_exists('a',$_GET) ? $_GET['a'] : false);

	// name
	$name = "./$t/$f-$v.$t";

	// open it 
	$file = file_get_contents($name);



	// remove 
	$search = array(
		"/\/\/[a-zA-Z0-9\s\&\?\.]+\n/",
		"/\/\*(.*)\*\//",
		"/\t+/",
//		"/\n+/",
	//	"/\n+|\t+/",
	//	"/\s+/"
	);
	
	// ciss 
	if ( $t == 'css' ) {
		$search[] = "/\n+/";
		$search[] = "/\n+|\t+/";
		$search[] = "/\s+/";
	}

			
	// expires 
	$expires = 60 * 60 * 24 * 30;

	// header
	header("Content-type:text/".($t=='css'?'css':'javascript'));
	header( 'Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . 'GMT');
	header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', time()-$expires ) . ' GMT' );
	header( "Cache-Control: max-age={$expires}, must-revalidate" );
	header( "Pragma:");

	echo "/* Copyright 2008-09 twitTangle.com */\n";

	// alwatys
	if ( $a == 'true' ) {
		if ( file_exists("./$t/always.{$t}") ) {
			include("./$t/always.{$t}");
		}
	}

	// do it 
	echo  preg_replace($search," ",$file);


	// if js
	if ( $t == 'js' ) {
		echo "
		
/* (portions of this code fall under the copyright below)
 *
 * dsHistory, v1-beta1 \$Rev: 70 $
 * Revision date: \$Date: 2008-10-24 14:25:17 -0700 (Fri, 24 Oct 2008) $
 * Project URL: http://code.google.com/p/dshistory/
 * 
 * Copyright (c) Andrew Mattie (http://www.akmattie.net)
 * Licensed under the MIT License (http://www.opensource.org/licenses/mit-license.php)
 * THIS IS FREE SOFTWARE, BUT DO NOT REMOVE THIS COMMENT BLOCK
 */		
		";
	}

?>