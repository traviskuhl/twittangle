<?php

	// root 
	define("ROOT", realpath(".") . "/" );
	
	// include
	require_once( realpath(ROOT."../") . "/framework/Global.php" );
	require_once( FRAMEWORK_ROOT . "Framework.php" );

	// figure out what module we want
	$module = p('module','index');

	// make sure the module exists
	if ( !class_exists($module,true) ) {
		header("Location:/404");
	}

	// make it 
	$m = new $module();
	
	// dispatch
	$m->dispatch();

?>