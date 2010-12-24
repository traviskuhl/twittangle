<?php

// friends
class friends extends Tangle {

	/* constuct */
	public function __construct() {
	
		// parent
		parent::__construct();
	
	}
		
	/* friends */
	protected function friends() {
	
		// validate
		$this->validate();
		
		// title
		$this->title = "Your Friends";
		$this->bodyClass = "friends full";
		
		// get all my friends
		$friends = $this->getFriends();
		
		// get page
		$page = $this->param('page',1);
		
		// cache
		$cid = md5("twitter.friends.{$page}.{$this->user['id']}");
		$ttl = (60*60*12);
		
		// check
		$data = $this->getCache($cid,$ttl);
		
			// no 
			if ( !$data ) {
			
				// data
				$d = $this->twitter('statuses/friends',array('page'=>$page));
				
				$data = array();
				
					// for each item
					foreach ( $d as $i ) {
						$data[$i['id']] = $i;
					}
				
				// save
				$this->saveCache($cid,$data,$ttl);
				
			}
		
		// more 
		$more = ( count($data) == 100 ? true : false );
						
		// tmpl
		if ( $this->param('type') == 'list' ) {
			include( $this->tmpl('friends/list') );
		}
		else {
			include( $this->tmpl('friends/friends') );
		}
	
	}
	
	/* friend */
	protected function friend() {
	
		// class
		$this->bodyClass = "friend";
		
		$this->validate();			
		
		// id 
		$id = $this->param('id');
				
		// get info about the friend
		$cid = md5('user.info.'.$id);
		$ttl = (60*60*5);
	
		// cache 
		$cache = $this->getCache($cid,$ttl);
		
			// no 
			if ( !$cache ) {
			
				// get it 
				$cache = $this->twitter("users/show/{$id}");
				
				// save
				$this->saveCache($cid,$cache,$ttl);
			
			}
			
		$this->title = $cache['screen_name'];
	
		// custom user
		$this->customProfile = $cache;
	
		// cid 
		$cid = md5('user.msg.'.$id);
		$ttl = (60*5);	
			
		// messag
		$messages = $this->getCache($cid,$ttl);
		
			// no cache
			if ( !$messages ) {
			
				// m
				$messages = $this->twitter('statuses/user_timeline',array('id'=>$id));
				
				// save
				$this->saveCache($cid,$messages,$ttl);
				
			}
			
	
		// tmpl
		include( $this->tmpl('friends/friend') );
		
	}
	

}


?>