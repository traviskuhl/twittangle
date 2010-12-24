<?php

class user extends Fe {

	public function __construct() {
		parent::__construct();
	}

	public function main() {
		
		// title and body class
		$this->title = "Home";
		$this->bodyClass = "home";
		
		// require session
		$this->requireSession();		
		
		// list
		$lists = $this->getUserLists();
		
		// follow
		$follow = $this->getUserFollowLists();
		
		// searches
		$searches = $this->getUserSearches();		
		
		// args 
		$args = array(
			'user' => $this->user['user']
		);
		
		// switch based on type
		switch(pp(0)) {
			
			// list
			case 'list':
				$args = array(
					'type' => 'list',
					'user' => pp(1),
					'id' => pp(2)
				);
			break;
			
			// smple
			case 'fav':
			case 'mentions':
				$args['type'] = pp(0);
			break;
			
			
		};
		
		// timeline
		$timeline = $this->getUserTimeline($args);
		
			// get them
			$mentions = $timeline->getMentions();
						
			// links
			$links = $timeline->getLinks();
		
			// photos
			$images = $timeline->getImages();

			// expand
			$expand = array();
		
		// user
		include( $this->tmpl('user/main') );
	
	}

}

?>