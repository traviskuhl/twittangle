<?php

	$url = rawurldecode($_GET['url']);
	list($width,$height) = explode('x',$_GET['size']);

	// require
	require_once 'Image/Transform.php';
	
	// un	
	$id = uniqid();
	
	file_put_contents("/tmp/{$id}",curl($url));
	
	//create transform driver object
	$it = Image_Transform::factory('GD');

	// load
	$it->load("/tmp/{$id}");

	// reize
	$it->scaleByX($width);
	
	$it->display('png');

	function curl($url) {
	
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);	
			
		$output = curl_exec($ch);  		
		
		curl_close($ch);
		
		return $output;	
	
	}

?>