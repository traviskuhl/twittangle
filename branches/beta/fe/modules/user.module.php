<?php

class user extends Tangle {
	

	public function __construct() {
			
		// arent
		parent::__construct();	
		
		// id 
		$id = preg_replace('/[\/]+/','',$this->param('act'));
		
			// no id
			if ( !$id ) {
				die("No User");
			}
			
		// get some info about this user
		$user = $this->twitter("users/show/{$id}",false,true);
		
			// no user
			if ( !$user ) {
				exit("
					<h2>Not a Twitter User</h2>
					<p>Twitter says the user {$id} doesn't exists</p>
				");
			}		

		$this->usr = $user;

		// path
		$path = explode('/',trim($this->param('path','main'),'/'));
		
		// reset act
		$_REQUEST['act'] = $path[0];	
			
		// check for blocked
		$blocked = $this->queryAndSave(array("SELECT * FROM `block` WHERE `screen_name` = '??' ",array($id)),(60*60));
												
			// blocked
			if ( $blocked ) {
				$_REQUEST['act'] = 'blocked';	
			}
			
		// follow
		if ( $this->loged AND $path[0] == 'follow' ) {
			
			// follow
			$resp = $this->curl("http://twitter.com/friendships/create/{$id}.json",$this->user,array('a'=>'b'));
			
			// parse
			$r = json_decode($resp,true);
		
			// what up
			if ( !array_key_exists('error',$r) ) {
			
				$this->allFriends[$r['id']] = array(
					'id' => $r['id'],
					'sn' => $r['screen_name'],
					'name' => $r['name'],						
					'img' => str_replace("http://s3.amazonaws.com/twitter_production/profile_images/","",$r['profile_image_url'])
				);			
			
				// save
				$c = $this->saveCloudCache($this->uid,$this->allFriends,'tt-friends');			
			
				// take back
				$this->go( $this->url('user',$r) );
								
				// done
				exit();
			
			}
			
			$error = "Twitter said: " . $r['error'];
		
		}				
			
		// call all
		$this->_all($user);			
			
		// body class
		$this->bodyClass = 'user';	
	
	}
	

	protected function _all($user) {	
	
		// user
		$id = $user['id'];						
			
		// custom profile
		$this->customProfile = $user;			
								
		// loged	
		if ( $this->loged AND $user['friends_count'] < 100000 ) {
		
			// get in common 
			$f = $this->twitter("friends/ids/{$id}");
			
			// friends
			$friends = array();
			
			// figure it 
			if ( is_array($f) ) {
    			foreach ( $this->allFriends as $id => $i ) {
    				if (in_array($id,$f)) {
    					$friends[] = $i;
    				}
    			}
            }
	
		// friends
		$this->_friends = $friends;	
		
		}
	
		
		// get groups 
		$groups = $this->getGroups($user['id']);
		
		// view groups
		$aGroups = array();
		
		// loop through
		foreach ( $groups as $g ) {
			if ( array_key_exists('privacy',$g['settings']) ) {

				// priv
				$priv = $g['settings']['privacy'];

				// what up
				if ( $priv == 'friends' AND $this->loged ) {
					
					// get users firends
					$friends = $this->getCloudCache($user['id'],'tt-friends',true);
					
					// are they a friend
					if ( is_array($friends) AND array_key_exists($this->uid,$friends) ) {
						$aGroups[] = $g;
					}
					
				}
				else if ( $priv == 'group' AND $this->loged ) {
				
					// get users
					$users = $this->getGroupUsers($g['id']);
				
					// yes
					if ( is_array($users) AND array_key_exists($this->uid,$users) )  {
						$aGroups[] = $g;
					}
				
				}
				else if ( $priv == 'public' ) {
					$aGroups[] = $g;
				}

			}
		}
		
		// groupts
		$this->aGroups = $aGroups;
	
	}
	
	protected function blocked() {
		include( $this->tmpl('user/message') );
	}	
	
	protected function main() {
	
		$user = $this->usr;
		$id = $user['id'];
			
		// get some info about this user
		$timeline = $this->twitter("statuses/user_timeline/{$id}",false,true);		
	
		// title
		$this->title = $user['name'];
	
		// url
		$this->path = explode('/',trim($this->param('path'),'/'));				
	
	
		include( $this->tmpl('user/user') );
		
	}

	protected function group() {
		
		// user
		$user = $this->usr;
		
		// info
		$name = $this->pathParam(1);
		$id = $this->pathParam(2);

		// get groups 
		$groups = $this->getGroups($user['id']);

		// bad?
		$badgroup = false;
	
			// bad
			if ( !array_key_exists($id,$groups) ) {
			
				// bad
				$badgroup = true;
				
				// message
				include( $this->tmpl('user/message') );
				
			}
	
		if ( !$badgroup ) {
		
			// g
			$g = $groups[$id];
		
			// yes or no	
			if ( array_key_exists('privacy',$g['settings']) AND $this->uid != $user['id'] ) {
	
				// priv
				$priv = $g['settings']['privacy'];
	
				// what up
				if ( $priv == 'friends' AND $this->loged ) {
					
					// get users firends
					$friends = $this->getCloudCache($id,'tt-friends',true);
					
					// are they a friend
					if ( !array_key_exists($this->uid,$friends) ) {
						$badgroup = true;
					}
					
				}
				else if ( $priv == 'group' AND $this->loged ) {
				
					// get users
					$user = $this->getGroupUsers($g['id']);
				
					// yes
					if ( !array_key_exists($this->uid,$users) )  {
						$badgroup = true;
					}
				
				}
				else if ( $priv == 'private' ) {
					$badgroup = true;
				}
	
			}
			
			if ( $badgroup ) {
				// message
				include( $this->tmpl('user/message') );			
			}
			else {
		
				// get a timeline for this group
				list($html,$timeline) = $this->getTimeline(array(
					'user' => $user['id'],
					'groups' => array($id)
				));		
				
				// template
				include( $this->tmpl('user/group') );
				
			}
		
		}	
		
		
	}

}

?>