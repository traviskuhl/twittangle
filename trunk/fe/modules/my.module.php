<?php

define('MAX_PAGES','200');

// my 
class my extends Tangle {

	public function __construct() {
	
		// arent
		parent::__construct();	
		
		// validate
		$this->validate();
		
		// path
		$this->path = explode('/',trim($this->param('path'),'/'));
		
	}

	// groups	
	public function groups() {	

		// get groups
		$groups = $this->getGroups();	
	
		// create
		if ( $this->pathParam(0) == 'create' ) {
			
			// name 
			$name = $this->param('name');
					
			// no name
			if ( !$name ) {
				$this->go( $this->url('my-groups') );
			}
			
			// clean
			$n = preg_replace("/[^a-zA-Z0-9\s]+/","",$name);
		
			// insert
			$r = $this->query("INSERT INTO `groups` SET `name` = '??', `user` = '??', `created` = '??' ",array($name,$this->uid,time()));
			
			// id
			$id = $this->dbh->insert_id;
			
			// clean groups
			$this->clearCache(md5('groups.'.$this->uid));
		
			// go
			$this->go(  $this->url('my-groups')."/{$id}/#group-".$id );
			
		}
		else if ( $this->pathParam(0) == 'delete' ) {
		
			// id 
			$id = $this->param('id');
			
				// valid group
				if ( !array_key_exists($id,$groups) ) {
					$this->go( $this->url('my-groups') );
				}
				
			// how many 
			$n = $groups[$id]['count'];
		
			// remove it
			$this->query("DELETE FROM groups WHERE id = '??' LIMIT 1 ",array($id));
			$this->query("DELETE FROM group_map WHERE group_id = '??' LIMIT {$n} ",array($id));
		
			// remove from cache 
			$this->clearCache(md5('groups.'.$this->uid));
			$this->clearCache(md5('group.'.$id));
		
			// go home	
			$this->go($this->url('my-groups'));		
		
		}

		$this->title = "My Groups";
		$this->bodyClass = 'groups';
	
		include( $this->tmpl('my/groups') );
	
	}
	
	// friends
	public function friends() {
	
		if ( $this->pathParam(0) == 'import' ) {
			$this->_import();
		}
		else {
	
			$friends = array();
	
			// stuff			
			$this->title = "My Friends";
			$this->bodyClass = 'friends';
			
			// total
			$total = count($this->allFriends);
		
			// none?			
			if ( !is_array($this->allFriends) OR $total == 0 ) {
			
				// echo
				echo "
					<h2>My Friends</h2>
				
					<br><br>
					<div style='text-align:center'>					
						<em class='b'>You Don't Have Any Friends</em>
						<div class='padd5 gray'>
							We could find any friends for you. You need friends to get started.
							<a href='/my/friends/import'>Maybe try importing again</a>
						</div>
					</div>
				
				";
			
			}
			else {
											
				// len
				$len = 209;
				
				// page
				$off = ($this->param('page',1) - 1)*$len;
				
				// friends
				$friends = array_slice($this->allFriends,$off,$len,true);
				
				// pages
				$pages = ceil( $total / $len );
								
				// tmpl
				include( $this->tmpl('my/friends') );
				
			}
			
		}
	
	}

	// import 
	public function _import() {
		
		// how long 
		if ( false ) { // $this->user['friends_count'] > 0 AND time()-$this->user['friends_updated'] < (60*60) ) { 
			
			// who did it
			if ( $this->param('auto') ) {
				$this->go( $this->url('home') );
			}
			else {
				echo "<h2>Can Not Complete Import</h2> You have already run an import in the last 60 mintues.";
			}
			
		}
		else {
		
			// what
			if ( $this->pathParam(1) == 'do' ) {
			
				// friends
				$friends = array();
			
				// lets start importing them 
				$data = $this->twitter('statuses/friends');
				
					// get them
					foreach ( $data as $d ) {
						$friends[$d['id']] = array(
							'id' => $d['id'],
							'sn' => $d['screen_name'],
							'name' => $d['name'],						
							'img' => $d['profile_image_url']
						);	
					}			
				
				// i 
				$i = 1;
				
				// get them
				while ( count($data) > 80 AND $i < MAX_PAGES ) {
				
					// data
					$data = $this->twitter("statuses/friends.json?page=".$i++);						
					
					// get them
					foreach ( $data as $d ) {
						$friends[$d['id']] = array(
							'id' => $d['id'],
							'sn' => $d['screen_name'],
							'name' => $d['name'],
							'img' => $d['profile_image_url']
						);	
					}
											
				}
	
				// save
				if ( count($friends) > 0 ) {
    				$r = $this->saveCloudCache($this->uid,$friends,'tt-friends');
                }
				
				// if 
				if ( $r == true ) {
								
					// update
					$r = $this->query("UPDATE users SET friends_updated = '??', friends_count = '??' WHERE id = '??' ",array(time(),count($friends),$this->uid));	
				
					// done
					exit( json_encode(array(true)) );
					
				}
				else {
					exit( json_encode(array(false)) );
				}		
			
			}
			
			// laoding
			$this->title = 'Import My Friends';
			$this->bodyClass = 'friends';
		
			// print 
			echo "
				<h1>Import Your Friends</h1>
				
				<div class='module'>
					<div class='bd'>
						<div class='import-loading'>
							<em>We're importing the list of users you are following... </em>
							Depending on how many users you are following this could take a few 
							minutes. So sit back, relax and enjoy the little loading icon. You may be
							asking yourself 'Hey! I already did this, why do I have to do it again.' Well we like 
							to run updates every few days to make sure we have all the users you're following.
							<div class='small'>We only import up to ". number_format(MAX_PAGES*100)." followers</div>
						</div>
					</div>
				</div>
				
				<script type='text/javascript'>
					TT.addToQueue(function(){
					
						// callbacks
						var callback = {
							'success': function(o) {
								var j = \$j.parse(o.responseText);
								if ( j ) { 
									window.location.href = '/my/friends';
								}
								else {
									alert('There was a problem importing your friends. Please hit refresh and try again');
								}
							}
						};
						
						// url
						var url = '/my/friends/import/do';
					
						// go for it 
						var r = \$c.asyncRequest('GET',url,callback);
					
					});
				</script>
					
			";
			
		}
		
	}

}

?>