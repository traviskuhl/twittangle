<?php

	// include the conf file
	include("./include/Config.class.php");
	
	// closed
	if ( defined('SITE_CLOSED') AND SITE_CLOSED === true AND !Config::getParam('alpha',false,$_COOKIE) ) {
		echo file_get_contents("brb.html");
		echo "<!-- ".date('r') . " - " . $_SERVER['SERVER_NAME']." - ".$_SERVER['SERVER_ADDR']." -->";
		exit();
	}	
	
	// check for beta 
	if ( $_SERVER['HTTP_HOST'] == 'beta.twittangle.com' ) {
		header("Location:http://www.twittangle.com");
	}
	
	// get an act
	$module = Config::getParam('module','index');

	// index	
	if ( !file_exists( MODULE . $module . '.module.php' ) ) {
		header("Location:/index/error"); exit;
	}
			
	// start a new request
	$t = new $module();

	// dispatch
	$t->dispatch();

?>