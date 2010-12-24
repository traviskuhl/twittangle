<?php

class dir extends Tangle {
	

	public function __construct() {
			
		// arent
		parent::__construct();	

	
		// body class
		$this->bodyClass = 'dir';
	
	}

	public function main() {
	
		include( $this->tmpl('dir/index') );
	
	}
	
}

?>