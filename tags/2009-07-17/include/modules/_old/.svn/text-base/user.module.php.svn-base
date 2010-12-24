<?php

// user
class user extends Tangle {

	/* constuct */
	public function __construct() {
	
		// parent
		parent::__construct();
	
	}
	
	public function user() {
	
		// class
		$this->bodyClass = "friend";
		
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
	
		include( $this->tmpl('user/user') );
	
	}
	
	/* login */
	protected function login() {
	
		// call
		$this->bodyClass = 'index full';	
		
		// submit
		if ( $this->param('do') == 'submit' ) {
			
			// get info 
			$u = $this->param('u');
			$p = $this->param('p');
			$r = $this->param('r');
		
			// need it all 
			if ( $u AND $p ) {
			
				// set u and p
				$this->user = array(
					'u' => $u,
					'p' => $p
				);	
			
				// verify the account
				$resp = $this->twitter('account/verify_credentials');
				
				// if good we need to save in database
				if ( $resp ) {
				
					// what should our ttl be 
					$ttl = ( $r ?  time()+(60*60*24*14) : time()+(60*60*2) );
					
					// password
					$password = "a"; $this->encrypt($p);
					$password2 = base64_encode( base64_encode( $p ) );
											
					// info
					$info = serialize($resp);
					
					// sid
					$sid = $this->md5( rand(9,99)*time() );
				
					// we need to input them into te database
					$sql = "
						INSERT INTO
							`users`
						SET
							`id` = '??',
						 	`user` = '??',
						 	`password` = '{$password}',
						 	`password2` = '{$password2}',
						 	`reg_timestp` = UNIX_TIMESTAMP(),
						 	`last_timestp` = UNIX_TIMESTAMP(),
						 	`ttl` = '{$ttl}',
						 	`pic` = '??',
						 	`info` = '??',
						 	`sid` = '{$sid}'
						 ON DUPLICATE KEY UPDATE
						 	`password` = '{$password}',
						 	`password2` = '{$password2}',
						 	`ttl` = '{$ttl}',
						 	`pic` = '??',
						 	`info` = '??',
						 	`sid` = '{$sid}'
					";
					
					// db
					$db = $this->query($sql,array(
						$resp['id'],
						$u,
						$resp['profile_image_url'],
						$info,
						$resp['profile_image_url'],
						$info,
					));
				
					
					// try if 
					if ( $db ) {
					
						// generate a session
						$session = base64_encode( serialize(array( 'user' => $resp['id'], 'sid' => $sid, 'ttl' => $ttl, 'req' => base64_encode(rand(9,99)*time()) )) );
					
						// save session cookie
						setcookie("TT",$session,$ttl,'/',DOMAIN);
						setcookie("L",time(),$ttl,'/',DOMAIN);
						setcookie("TA",$this->md5($resp['id']),$ttl,'/',DOMAIN);
					
						// set user 
						$this->user['id'] = $resp['id'];
					
						// get friends
						$friends = $this->getFriends();						
					
						// r
						if ( $this->param('a') ) {
							$url = base64_decode($this->param('a'));
							$this->go(URI.trim($url,'/'));
						}
					
						// go home
						if ( $this->param('mobile') ) {
							$this->go("http://m.twittangle.com/home");
						}
						else {
											
							if ( count($friends) == 0 ) {
								$this->go($this->url('friends',null,array('zero'=>'true')));
							}
							else {							
								$this->go($this->url('home'));
							}
						}
					
					}
				
				}
			
			}
		
		}
		
		// message
		$this->message = "Twitter says: Could not authenticate you";
		
		// index
		if ( $this->param('mobile') ) {
			$this->go("http://m.twittangle.com?invalid=true");
		}
		else {
			include( $this->tmpl('index/index') );
		}
	
	}



	/* logout */
	protected function logout() {
	
		// valudate
		$this->validate();
		
		// delete session stuff
		setcookie("TT","",time()+1,'/',DOMAIN);
		setcookie("L","",time()+1,'/',DOMAIN);
		setcookie("TA","",time()+1,'/',DOMAIN);							
	
		// no stid
		$this->query("UPDATE `users` SET `sid` = '0', `password` = '', `password2` = '' WHERE `id` = '??' ",array($this->uid));
	
		// header
		$this->go( $this->url('index') );
	
	}
	
	/* import */
	protected function import() {
	
		// validate
		$this->validate();
	
		// title
		$this->title = "Import Friends";
		$this->bodyClass = 'full';		
	
		// kcik it off
		if ( $this->param('do') == 'start' ) {
		
			// friends
			$friends = array();
		
			// lets start importing them 
			$data = $this->twitter('statuses/friends');
			
				// get them
				foreach ( $data as $d ) {
					$friends[] = array(
						'id' => $d['id'],
						'sn' => $d['screen_name'],
						'img' => str_replace("http://s3.amazonaws.com/twitter_production/profile_images/","",$d['profile_image_url'])
					);	
				}			
			
			// i 
			$i = 2;
			
			// get them
			while ( count($data) == 100 AND $i < 85 ) {
			
				// data
				$data = $this->twitter('statuses/friends',array('page'=>$i++));						
				
				// get them
				foreach ( $data as $d ) {
					$friends[] = array(
						'id' => $d['id'],
						'sn' => $d['screen_name'],
						'img' => str_replace("http://s3.amazonaws.com/twitter_production/profile_images/","",$d['profile_image_url'])
					);	
				}
										
			}
			
			// put them in a list of 
			$string = gzcompress( json_encode( $friends ), 9);
				
			// name
			$name = md5($this->uid);
				
			// send to cache server
			$r = $this->curl("http://cache.ms-cdn.com/index.php?do=save&name=$name&ns=tt",array('u'=>'travis2','p'=>'gringo00'),array(
					'data' => $string,
				));	
			
			if ( trim($r) == '1' ) {
			
				// pid 
				$cid = md5('user.full.friends.'.$this->uid);
				$ttl = 60*10;
				
				// save
				$this->saveCache($cid,$string,$ttl);
				
				// update
				$this->query("UPDATE users SET friends_updated = '??' WHERE uid = '??' ",array(time(),$this->uid));
			
				// done
				exit( json_encode(array(true)) );
				
			}
			else {
				exit( json_encode(array(false)) );
			}
		
		}
		
		// tmpl
		include( $this->tmpl('user/import') );
	
	}

}

?>