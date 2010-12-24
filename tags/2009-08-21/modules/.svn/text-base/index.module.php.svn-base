<?php

class index extends Tangle {
	

	public function __construct() {
			
		// arent
		parent::__construct();	

	
		// body class
		$this->bodyClass = 'index';
	
	}


	/* main */
	public function main() {

		// loged
		if ( $this->loged ) {
			$this->go("/home");
		}

		// template	
		include( $this->tmpl("index/index") );
	
	}
	
	/* oauth */
	public function oauth() {
	
		// get cookie
		$cookie = $this->param('TTX',false,$_COOKIE);		

		// save the token in the db
		if ( $this->param('do') == 'auth' ) {
	
			// delete session stuff
			setcookie("TT","",time()+1,'/',DOMAIN);
			setcookie("L","",time()+1,'/',DOMAIN);
			setcookie("TA","",time()+1,'/',DOMAIN);		

			// oath
			$to = new TwitterOAuth(AUTH_KEY, AUTH_SECRET);  
		
			// get token 
			$tok = $to->getRequestToken();
			
			// ip
			$ip = $_SERVER['REMOTE_ADDR'];			
			
			// make an id
			$id = md5( time() * rand(9,time()) - time() * 9 . $ip );
			
			$d = array( 'ip' => $ip, 'token' => $tok);
			
				// link
				if ( $this->param('link') == 'true' ) {
					$d['link'] = $this->session['sid'];
				}			
			
			// check for remove 
			if ( $this->param('flow') == 'remove' AND $this->param('tok') == $this->md5('act:remove:account:'.$_SERVER['REMOTE_ADDR']) ) {
				$d['flow'] = 'remove';			
			}
			
			// data
			$data = json_encode( $d );
						
			// save
			$this->query("INSERT INTO oauth SET `id` = '??', `data` = '??' ", array($id,$data) );
			
			// set a cookie
			setrawcookie("TTX", base64_encode( serialize( array('id' => $id, 'ip' => $ip, 'sig' => md5($data) ) ) ), null, '/', '.twittangle.com' ); 

			// header
			header("Location:" . $to->getAuthorizeURL( $tok['oauth_token']) );
			
			// done
			exit();
		
		}
		else if ( $cookie ) {
		
			// nice cookie
			$c = unserialize( base64_decode( $cookie ) );
			
				// need an array
				if ( !is_array($c) ) {
					header("Location:/");
				}

			// get from the db
			$row = $this->row("SELECT * FROM oauth WHERE `id` = '??' ",array($c['id']));
			
			// data
			$data = @json_decode($row['data'],true);	
			
			// we good 
			if ( $row AND is_array($data) AND $c['ip'] == $_SERVER['REMOTE_ADDR'] AND $c['sig'] == md5($row['data']) ) {
			
				// delete oauth
				$this->query("DELETE FROM oauth WHERE id = '??' ",array($c['id']));		
			
				// no cookie
				setrawcookie("TTX","",time()+1,'/', '.twittangle.com');
			
    			// new auth to get the token
				$to = new TwitterOAuth(AUTH_KEY, AUTH_SECRET, $data['token']['oauth_token'], $data['token']['oauth_token_secret']);

				// get token
				$tok = $to->getAccessToken();
								
				// reset oauth with the access tokens 
				$to = new TwitterOAuth(AUTH_KEY, AUTH_SECRET, $tok['oauth_token'], $tok['oauth_token_secret']);

				// ask twitter for their information 
				$resp =  json_decode($to->OAuthRequest('https://twitter.com/account/verify_credentials.json', array(), 'GET'), true);
			
				// if good we need to save in database
				if ( $resp ) {				
				
					// check flow
					if ( array_key_exists('flow',$data) ) {
					
						// remove
						if ( $data['flow'] == 'remove' ) {
							
							// go to remove
							$this->query("INSERT INTO `block` SET `screen_name` = '??', `timestamp` = '??' ",array($resp['screen_name'],time()));
						
							// go to home
							$this->go( $this->url('index',array(),array('flow'=>'remove')) );
						
							// dump cache
							$this->mem->delete('query:'.md5("SELECT * FROM `block` WHERE `screen_name` = '??' ").':'.serialize(array($resp['screen_name'])));
						
							exit();
						
						}
					
					}
				
					// what should our ttl be 
					$ttl = ( true ?  time()+(60*60*24*14) : time()+(60*60*2) );
																
					// info
					$info = serialize($resp);
					
					// sid
					$sid = $this->md5( rand(9,99)*time() );
								
					// oauth
					$oauth = json_encode($tok);
								
					// we need to input them into te database
					$sql = "
						INSERT INTO
							`users`
						SET
							`id` = '??',
						 	`user` = '??',
						 	`password` = '',
						 	`password2` = '',
						 	`reg_timestp` = UNIX_TIMESTAMP(),
						 	`last_timestp` = UNIX_TIMESTAMP(),
						 	`ttl` = '{$ttl}',
						 	`pic` = '??',
						 	`info` = '??',
						 	`sid` = '{$sid}',
						 	`oauth` = '??'
						 ON DUPLICATE KEY UPDATE
						 	`password` = '',
						 	`password2` = '',
						 	`ttl` = '{$ttl}',
						 	`pic` = '??',
						 	`info` = '??',
						 	`sid` = '{$sid}',
						 	`oauth` = '??'
					";
					
					// db
					$db = $this->query($sql,array(
						$resp['id'],
						$resp['screen_name'],
						$resp['profile_image_url'],						
						$info,
						$oauth,
						$resp['profile_image_url'],
						$info,
						$oauth
					));
				
					// try if 
					if ( $db ) {
					
						// if switch we need to update this 
						// new account to all other accounts
						if ( array_key_exists('link',$data) ) {
							
							// get user 
							$this->getUser();
							
								// no user or sid is not the same
								if ( $this->loged AND $this->session['sid'] != $data['link'] ) {
									
									// get accounts for this user
									$accts = $this->row("SELECT accounts FROM users WHERE id = '??' ",array($resp['id']));
										
										// set it 
										$acts = json_decode($accts['acounts'],true);
										
										// add
										foreach ( $acts as $aid => $a ) {
											$this->accounts[$aid] = $a;
										}
									
									// add this new account to our current list of 
									// accounts
									$this->accounts[$resp['id']] = array( 'id' => $resp['screen_name'], 'sid' => $sid, 'ts' => time(), 'pic' => str_replace("http://s3.amazonaws.com/twitter_production/profile_images/","",$resp['profile_image_url']) );
															
									// aids
									$aids = array();
								
										// each one 	
										foreach ( array_keys($this->accounts) as $aid ) {
											$aids[] = " `id` = '{$aid}' ";
										}
						
									// update
									$this->query("UPDATE `users` SET `accounts` = '??' WHERE ".implode(' OR ',$aids), array( json_encode($this->accounts) ) );
									
								}
						
						}					
					
						// generate a session
						$session = base64_encode( serialize(array( 'user' => $resp['id'], 'sid' => $sid, 'ttl' => $ttl, 'req' => base64_encode(rand(9,99)*time()) )) );
					
						// save session cookie
						setcookie("TT",$session,$ttl,'/',DOMAIN);
						setcookie("L",time(),$ttl,'/',DOMAIN);
						setcookie("TA",$this->md5($resp['id']),$ttl,'/',DOMAIN);
					
						// set user 
						$this->user['id'] = $resp['id'];									
					
						// r
						if ( $this->param('a') ) {
							$url = base64_decode($this->param('a'));
							$this->go(URI.trim($url,'/'));
						}
						
						// try to get friends
						$fri = $this->getCloudCache($resp['id'],'tt-friends',true);
						
							// no friends
							if ( !$fri OR count($fri) == 0 ) {
								$this->go( $this->url('friends-import') );
							}
			
						// go			
						$this->go( $this->url('home') );
					
					}
				
				}
			
			
			}
			
		
		}
		
		// send index
		header("Location:/");
	
	}
	
	/* home */
	public function home() {

		$title = "My Timeline";

		// body
		$this->bodyClass = 'home';
	
		// loged in
		$this->validate();
	
		// holder
		$timeline = array();
	
		// url
		$pageUrl = trim($this->param('path','timeline/last-visit'),'/');	
	
		// path 
		$path = explode('/',$pageUrl);
		
		// params
		$page = $this->param('page',1,null,'num');
	
		// messages
		$menu_messages = array(
			'replies'	=> array( '@Replies', 'statuses/replies.json?page='.$page ),
			'dm'		=> array( 'Direct Messages', 'direct_messages.json?page='.$page ),
			'fav'		=> array( 'Favorites', 'favorites.json?page='.$page )
		);
		
		// timeline
		$menu_timeline = array(
			'last-visit'	=> array( 'Since Last Visit', 'last' ),
			'two-hours'		=> array( 'Two Hours Ago', 'two' ),
			'eight-hours'	=> array( 'Eight Hours Ago', 'eight' ),
			'yesterday'		=> array( 'Yesterday', 'yesterday' )
		);
		
		// get groups 
		$groups = $this->getGroups();		
		
		// load now 
		$loadnow = true;
	
			// if alpha
			if ( $this->param('_ctx') != 'xhr' ) {
				$loadnow = false;
			}
		
		// loadnow
		if ( $loadnow ) {	
			
			// get networks	
			$networks = $this->getUserNetworks();
			
			// reload
			$reload = true;
			
			// figure out what data to get		
			if ( $path[0] == 'timeline' ) {
			
				// title
				$title = "My Timeline";
			
				// get them 
				list($h,$timeline,$pager) = $this->getTimeline(array(
						'since' => $menu_timeline[$path[1]][1],
						'page' => $this->param('page')
					));			
					
				// set last 
				setrawcookie("L",time(),time()+(60*60*24*365),'/',DOMAIN);
			
				// rss
				$this->rssLink = URI."rss/{$this->user['user']}/timeline/{$path[1]}";
							
			}
			else if ( $path[0] == 'messages' ) {
			
				// title
				$title = $menu_messages[$path[1]][0];
	
				// get all replies
				$rep = $this->twitter($menu_messages[$path[1]][1]);			
			
				// print me 
				foreach ( $rep as $status ) {
					
					if ( array_key_exists('sender',$status) ) {
						$status['user'] = $status['sender'];
					}		
					
					// raw
					$timeline[$status['id']] = $status;
					
				}		
			
			}
			else if ( $path[0] == 'my-groups' ) {
			
				// get group info 
				$groups = $this->getGroups();
			
				// title
				$title = 'My Groups &#187; ' . $groups[$path[1]]['name'];		
			
				// get them 
				list($h,$timeline,$pager) = $this->getTimeline(array(
						'groups' => $path[1],
						'page' => $this->param('page')
					));						
					
				// set last 
				setrawcookie("L",time(),time()+(60*60*24*365),'/',DOMAIN);	

				// rss
				$this->rssLink = URI."rss/{$this->user['user']}/group/{$path[1]}";																		
			
				// set last 
				$this->updateLastTsCookie("g{$path[1]}",time());			
			
			}
			else if ( $path[0] == 'my-networks' ) {
				
				// get my networks
				$networks = $this->getUserNetworks();
				
				// title
				$title = "My Netowrks &#187; " . $networks[$path[1]]['title'];
				
				// get them
				list($rep) = $this->getNetworkUpdates($path[1],$page);
			
					// print me 
					foreach ( $rep['results'] as $item ) {
												
						$d = array(
							'text' => $item['text'],
							'id' => $item['id'],					
							'created_at' => $item['created_at'],
							'user' => array(
								'screen_name' => $item['from_user'],
								'profile_image_url' => $item['profile_image_url'],
								'id' => $item['from_user_id']
							),
							'favorited' => false,
							'source' => "the web",
							'in_reply_to_status_id' => false
						);					
					
						// raw
						$timeline[$item['id']] = $d;
						
					}		
			
			}
			else if ( $path[0] == 'my-searches' ) {
			
				// search
				$search = $this->savedSearches[$path[1]];
				
				// title
				$title = "Search &#187; " . htmlentities($search['q'],ENT_QUOTES,'utf-8');
				
				// $q 
				$q = $search['q'];
			
				// run it 
				$r = $this->curl("http://search.twitter.com/search.json?page={$page}&q=".urlencode($q));		
			
				// get j
				$j = json_decode($r,true);
			
				// results
				foreach ( $j['results'] as $item ) {
					$timeline[$item['id']] = array(
						'text' => $item['text'],
						'id' => $item['id'],					
						'created_at' => $item['created_at'],
						'user' => array(
							'screen_name' => $item['from_user'],
							'profile_image_url' => $item['profile_image_url'],
							'id' => $item['from_user_id']
						),
						'favorited' => false,
						'source' => "the web",
						'in_reply_to_status_id' => false
					);								
				}		
			
			}
			else if ( $path[0] == 'friend' ) {
			
				// title
				$title = "Friends &#187; " . $this->allFriends[$path[1]]['name'] . "<span><a href='/user/".$this->allFriends[$path[1]]['sn']."'>profile</a></span>";		
				$nicetitle = "Friends &#187; " . $this->allFriends[$path[1]]['name'];
			
				// get some info about this user
				$timeline = $this->twitter("statuses/user_timeline/{$path[1]}",false,true);			
			
			}
			
			// only one page
			if ( $page != 1 ) {
				$reload = false;
			} 
			
		}
		
		// title
		$this->title = $title;
		
			if ( isset($nicetitle) ) {
				$this->title = $nicetitle;
			}
	
		// template	
		include( $this->tmpl("index/home") );
	
	}

	/* dm */
	public function dm() {
		
		// need to be loged
		if ( !$this->loged ) {
			exit($this->url( $this->url('home') ));
		}
	
		// to
		$to = explode(",",$this->param('to'));
		$txt = $this->param('txt');
		
		// send them out
		foreach ( array_slice($to,0,10) as $id ) {
		
			// p
			$p = array(
				'user' => $id,
				'text' => $txt
			);
		
			// what up
			$r = $this->twitter("direct_messages/new",$p);
		
		}
	
		// send back
		$this->go(URI.'home/messages/dm/complete');
	
	}

	/* search */
	public function search() {
	
		// q
		$q = urldecode(stripslashes($this->param('q')));
	
		// title
		$this->title = 'Search';
		$this->bodyClass = 'search';
		
		// get trending
		$cid = md5('search.trends');
		$ttl = 60*3;
		
		// get
		$trend = $this->getCache($cid,$ttl);
		
			// nope
			if ( !$trend ) {
			
				// req
				$r = $this->curl("http://search.twitter.com/trends.json");
				
				// get json
				$trend = json_decode($r,true);
			
				// save
				$this->saveCache($cid,$trend,$ttl);
			
			} 
		
		$timeline = array();
		
		// q me up 
		if ( $q ) {
			
			$page = 1;
		
			// title
			$this->title .= " &#187; " . htmlentities($q,ENT_QUOTES,'utf-8');
				
			// run it 
			$r = $this->curl("http://search.twitter.com/search.json?page={$page}&q=".urlencode($q));
		
				// need an r
				if ( !$r ) {
					$error = "Could not connect to Twitter search.";
				}
				
			// parse
			$j = json_decode($r,true); 
			
				// nope
				if ( !$j OR !is_array($j) ) {
					$error = "<span style='color:red'>Bad response from Twitter search. Please try again</span>";
				}		
							
			// figure out what page
/*
			if ( $j['next_page'] ) {
				if ( preg_match("/\?page=([0-9]+)/",$j['next_page'],$match) ) {
					$next = $match[1];
					$on = $next - 1;
					$prev = ( $on != 0 ? $on -1 : false );
				}
			}
*/
			
			$timeline = array();
			
			foreach ( $j['results'] as $item ) {
				$timeline[$item['id']] = array(
					'text' => $item['text'],
					'id' => $item['id'],					
					'created_at' => $item['created_at'],
					'user' => array(
						'screen_name' => $item['from_user'],
						'profile_image_url' => $item['profile_image_url'],
						'id' => $item['from_user_id']
					),
					'favorited' => false,
					'source' => "the web",
					'in_reply_to_status_id' => false
				);								
			}
		
		}
	
		// tmpl
		include( $this->tmpl('index/search') );
	
	}
	
	
	public function login() {
	
		// check for switch 
		$pageUrl = trim($this->param('path','/'),'/');	
	
		// path 
		$path = explode('/',$pageUrl);	
	
		// swithc
		if ( isset($path[0]) AND $path[0] == 'switch' ) {

			// check if this is good
			$this->getUser();
			
			// newid 
			$newid = $path[1];
			
			// accounts
			if ( !array_key_exists($newid,$this->accounts) ) {	
				exit(header("Location:/"));
			}	
			
			// get creds
			$user = $this->row(" SELECT id,user,password2,oauth FROM users WHERE `id` = '??' ",array($newid));
			
				// no sid we must log them out
				if ( !$user ) {
					$this->logout();
				}						
				
			// how	
			if ( $user['oauth'] ) {
			
				// ttl
				$ttl = time()+(60*60*24*14);
		
				// make a sid
				$sid = md5(rand(9,time())*time()+rand(9,99));
				
				// update
				$this->query("UPDATE users SET sid = '??' WHERE id = '??' ",array($sid,$user['id']));
			
				// generate a session
				$session = base64_encode( serialize(array( 'user' => $user['id'], 'sid' => $sid, 'ttl' => $ttl, 'req' => base64_encode(rand(9,99)*time()) )) );
			
				// save session cookie
				setcookie("TT",$session,$ttl,'/',DOMAIN);
				setcookie("L",time(),$ttl,'/',DOMAIN);
				setcookie("TA",$this->md5($user['id']),$ttl,'/',DOMAIN);			
			
				// go home
				$this->go( $this->url('home') );
				
				// done
				exit();
			
			}
			else {

				// all good
				$_REQUEST['u'] = $user['user'];
				$_REQUEST['p'] = base64_decode( base64_decode( $user['password2'] ) );
			
			}

			// submit
			$_REQUEST['do'] = 'submit';					

		}
	
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
				
				// try to end their sesson
				$this->curl("http://twitter.com/account/end_session.json",$this->user,array('end'=>'true'));
			
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
						 	`sid` = '{$sid}',
						 	`oauth` = ''
						 ON DUPLICATE KEY UPDATE
						 	`password` = '{$password}',
						 	`password2` = '{$password2}',
						 	`ttl` = '{$ttl}',
						 	`pic` = '??',
						 	`info` = '??',
						 	`sid` = '{$sid}',
						 	`oauth` = ''
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
					
						// if switch we need to update this 
						// new account to all other accounts
						if ( $this->param('type') == 'switch' ) {
							
							$this->getUser();
							
							// get accounts for this user
							$accts = $this->row("SELECT accounts FROM users WHERE id = '??' ",array($resp['id']));
								
								// set it 
								$acts = json_decode($accts['acounts'],true);
								
								// add
								foreach ( $acts as $aid => $a ) {
									$this->accounts[$aid] = $a;
								}
							
							// add this new account to our current list of 
							// accounts
							$this->accounts[$resp['id']] = array( 'id' => $resp['screen_name'], 'sid' => $sid, 'ts' => time(), 'pic' => str_replace("http://s3.amazonaws.com/twitter_production/profile_images/","",$resp['profile_image_url']) );
													
							// aids
							$aids = array();
						
								// each one 	
								foreach ( array_keys($this->accounts) as $aid ) {
									$aids[] = " `id` = '{$aid}' ";
								}
				
							// update
							$this->query("UPDATE `users` SET `accounts` = '??' WHERE ".implode(' OR ',$aids), array( json_encode($this->accounts) ) );
						
						}					
					
						// generate a session
						$session = base64_encode( serialize(array( 'user' => $resp['id'], 'sid' => $sid, 'ttl' => $ttl, 'req' => base64_encode(rand(9,99)*time()) )) );
					
						// save session cookie
						setcookie("TT",$session,$ttl,'/',DOMAIN);
						setcookie("L",time(),$ttl,'/',DOMAIN);
						setcookie("TA",$this->md5($resp['id']),$ttl,'/',DOMAIN);
					
						// set user 
						$this->user['id'] = $resp['id'];									
					
						// r
						if ( $this->param('a') ) {
							$url = base64_decode($this->param('a'));
							$this->go(URI.trim($url,'/'));
						}
						
						// check for a type
						if ( $this->param('type') == 'xhr' ) {
						
						}
						else {
						
							// go home
							if ( $this->param('mobile') ) {
								$this->go("http://m.twittangle.com/home");
							}
							else {
							
								// updates
								$row = $this->row("SELECT friends_updated,friends_count FROM users WHERE id = '??' ",array($this->user['id']));
												
								if ( $row['friends_updated'] == '0' OR time()-$row['friends_updated'] > (60*60*24) OR $row['friends_count'] != $resp['friends_count'] ) {
									$this->go( $this->url('friends-import',false,array('auto'=>true)) );
								}
								else {							
									$this->go( $this->url('home') );
								}
							}
							
						}
					
					}
				
				}
			
			}
		
		}
		
		// message
		$this->message = "Twitter says: Could not authenticate you";
		
		// index
		if ($this->param('type')=='xhr') {
			exit("<html><head><script type='text/javascript'> parent.TT.Global.addAccountCallback({'error':'".$this->message."'}) </script></head><body></body></html>");
		}		
		else if ( $this->param('mobile') ) {
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
		setcookie("TTLB","",time()+1,'/',DOMAIN);		
		setcookie("TA","",time()+1,'/',DOMAIN);							
	
		// no stid
		$this->query("UPDATE `users` SET `sid` = '0', `oauth` = '' WHERE `id` = '??' ",array($this->uid));
	
		// curl
		$this->curl("http://twitter.com/account/end_session.json",$this->user,array('end'=>'true'));
	
		// header
		$this->go( $this->url('index') );
	
	}	

	protected function user() {	
		
	    // blocked
	    $block = array('ffffelix');		
		
		// id 
		$id = preg_replace('/[\/]+/','',$this->pathParam(0));
		
			// no id
			if ( !$id ) {
				die("No User");
			}
			
		// check for blocked
		$blocked = $this->queryAndSave(array("SELECT * FROM `block` WHERE `screen_name` = '??' ",array($id)),(60*60));
			
		// yes
		if ( !$blocked ) {
				
			// url
			$this->path = explode('/',trim($this->param('path'),'/'));				
				
			// follow
			if ( $this->loged AND $this->pathParam(1) == 'follow' ) {
				
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
		
			// get some info about this user
			$user = $this->twitter("users/show/{$id}",false,true);
			
				// no user
				if ( !$user ) {
					exit("
						<h2>Not a Twitter User</h2>
						<p>Twitter says the user {$id} doesn't exists</p>
					");
				}
				
			// title
			$this->title = $user['name'];
			$this->bodyClass = 'user';
				
					// custom profile
					$this->customProfile = $user;			
				
			// get some info about this user
			$timeline = $this->twitter("statuses/user_timeline/{$id}",false,true);	
										
				// loged	
				if ( $this->loged AND $user['friends_count'] < 100000 ) {
				
					// get in common 
					$f = $this->twitter("friends/ids/{$id}");
					
					// friends
					$friends = array();
					
					// figure it 		
					foreach ( $this->allFriends as $id => $i ) {
						if (in_array($id,$f)) {
							$friends[] = $i;
						}
					}
				
				}
				
			}

		// template
		include( $this->tmpl('index/user') );
	
	}

	public function terms() {
	
		$this->title = "Terms";
		$this->bodyClass = "terms";

		// template
		include( $this->tmpl('index/terms') );
	
	}

	// beta 
	protected function beta() {
		
		// super 
		if ( $this->param('alpha') ) {
			setcookie("alpha",true,time()+(60*60*24*365),'/',DOMAIN);	
		}
	
		setcookie("beta",true,time()+(60*60*24*365),'/',DOMAIN);
		$this->go('http://www.twittangle.com/');
	}	
	
	protected function nobeta() {
	
		
		// delete session stuff
		setcookie("TT","",time()+1,'/',DOMAIN);
		setcookie("L","",time()+1,'/',DOMAIN);
		setcookie("TA","",time()+1,'/',DOMAIN);							
	
		// no stid
		$this->query("UPDATE `users` SET `sid` = '0', `oauth` = '' WHERE `id` = '??' ",array($this->uid));
	
		// curl
		$this->curl("http://twitter.com/account/end_session.json",$this->user,array('end'=>'true'));	
	
		setcookie("beta",false,time()+1,'/',DOMAIN);
		$this->go('http://www.twittangle.com/');	
	}
	
	// 404
	protected function error() {
		$this->title = 'Page Not Found';
		
		// print 
		echo "
			<h2>Page Not Found</h2>			
			<p style='width: 50%'>We couldn't find the page you requested. If you typed the link directly, make sure 
			you spelled everything correctly. If you clicked a link, go back and try the link again.</p>
		
		";
		
	}

	protected function remove() {
	
		// toke
		$token = $this->md5('act:remove:account:'.$_SERVER['REMOTE_ADDR']);
	
		echo "
		
			<h1>Remove your Profile</h1>
			
			<div class='yui-gc'>
				<div class='yui-u first'>
					<div class='module'>
						<div class='bd'>
							<p class='padd10'>In order to remove your profile from twitTangle.com, we need to first verify that you are you. In order to
							do that we need you to confirm your user account with Twitter. To do so, please click the button below.</p> 
							
							<a class='login-btn ignore' style='width: 300px; margin: 20px auto 0;' href='/index/oauth?do=auth;flow=remove;tok={$token}'>Login with Twitter</a>							
							
							<p class='gray padd10 center'>By logging in above, you will not be creating a twitTangle.com account. We will only use this authorization to confirm your identity. After that, all records relating to your account will be removed from twitTangle.</p>
						</div>
					</div>
				</div>
			</div>
		
		";
	
	}

}

?>