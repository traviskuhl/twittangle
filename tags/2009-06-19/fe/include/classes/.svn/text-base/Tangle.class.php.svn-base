<?php

	// set auth stuff
	define("AUTH_KEY", getenv('TT_AUTH_KEY') ); 
	define("AUTH_SECRET", getenv('TT_AUTH_SECRET') ); 

	// include
	include(ROOT."include/classes/OAuth.class.php");

	/* tangle */
	class Tangle extends Base {
	
		// user
		public $user = false;
		public $loged = 0;
		public $uid = 0;
		public $req = 0;
		public $customProfile = array();
		public $profileCss = "";
		public $savedSearches = array();		
		public $accounts = array();
	
		public $cache = array();
	
		/* construct */
		public function __construct($mod='') {
			
			// parent
			parent::__construct();
			
			// start mem cache
//			$this->mem = new Memcache;
//			$this->mem->connect('localhost', 11211);			
			
			// we're beta
			$this->beta = $this->param('beta',false,$_COOKIE);
			$this->alpha = $this->param('alpha',false,$_COOKIE);			
					
			// get session
			if ( $mod != 'rss' AND $mod != 'api' AND $mod != 'cron' ) { 
			
				// user 
				$this->getUser();	
				
				// custom profile
				$this->customProfile = $this->user['info'];
			
				if ( $this->oauth  ) {
							
					// no stid
//					$this->query("UPDATE `users` SET `sid` = '0', `oauth` = '' WHERE `id` = '??' ",array($this->uid));				
			
//					setcookie("TT","",time()+1,'/',DOMAIN);
//					setcookie("L","",time()+1,'/',DOMAIN);
//					setcookie("TA","",time()+1,'/',DOMAIN);							
					
//					header("Location:http://twittangle.com");

				}
				
			}
		
			$this->linkTypes = array(
				array('tinyurl.com','expand'),
				array('twurl.nl','expand'),
				array('bit.ly','expand'),				
				array('twitpic.com','pic'),
				array('yfrog.com','pic'),				
				array('youtube.com/watch','video'),
				array('qik.com/video','video'),
				array('ff.im','expand'),
				array('tr.im','expand'),				
			);
			
			
			// 
			$this->linkTypeTitles = array(
				'expand' => 'expand',
				'pic' => 'view pic',
				'video' => 'view video'
			);		
		
			// path
			$this->path = explode("/", trim($this->param('path'),'/') );
		
		}
		

		
	 		
	 
	 	/* get final URL */
		function getFinalUrl($url) {
		
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_HEADER, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_NOBODY, 1);						
				$output = curl_exec($ch);      					
				curl_close($ch);
				
				// $lnk 
				$lnk = false;
				
				if ( preg_match("/Location:([^\n]+)/",$output,$m) ) {
					$lnk = $m[1];
				}
				
				return $lnk;			
		
		}		 
		
		public function getManyUrls($urls) {
		
			// mh			
			$mh = curl_multi_init();
			
			// url
			$curl = array();
			$map = array();			

			// go for it 
			foreach ( $urls as $url ) {
			
				$id = md5($url);
				
				// curl
				$curl[$id] = curl_init($url);
			
				// set opn
				curl_setopt($curl[$id], CURLOPT_HEADER, 1);
				curl_setopt($curl[$id], CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl[$id], CURLOPT_NOBODY, 1);
				curl_setopt($curl[$id], CURLOPT_TIMEOUT, 5);
			
				// add
				curl_multi_add_handle($mh, $curl[$id]);
						
			}
			
			// not running			
			$running = null;
			
			// start
			do {
				curl_multi_exec($mh, $running);
			} while($running > 0);
			
			// links
			$final = array();
			
			// loop
			foreach ( $curl as $id => $c ) {
			
				// $output 
				$output = curl_multi_getcontent($c);
				
				// remove
				curl_multi_remove_handle($mh, $c);
			
				// match
				if ( preg_match("/Location:([^\n]+)/",$output,$m) ) {
					$final[$id] = $m[1];
				}			
			
			}
			
			// end;
			curl_multi_close($mh);

			
			// give back
			return $final;
		
		}
	 
	 	/* validate */
	 	protected function validate($r=false) {
	 		
	 		// are they loged in
	 		if ( !$this->loged ) {
			 			
				setcookie("TT","",time()+1,'/',DOMAIN);
				setcookie("L","",time()+1,'/',DOMAIN);
				setcookie("TA","",time()+1,'/',DOMAIN);									 			
	 			
	 			$a = "";
	 			
	 				if ($r) {
	 					$a = '?r='.base64_encode("{$r}");
	 				}
	 			
	 			// clear any 
	 			$this->go( URI . 'login' . $a);
	 		}
	 	
	 	}
	 	
	 	public function getCloudCache($id,$ns='tt',$cache=false) {
	 	
	 		$id = md5($id);
	 	
	 		// cache?
/*
	 		if ( $cache ) {
	 			
	 			// cid
	 			$cid = md5("cloud.$ns.$id");
	 			$ttl = 60;
	 			
	 			// get 
	 			$r = $this->getCache($cid,$ttl);
	 		
	 				// r?
	 				if ( $r ) {
	 					return json_decode(stripslashes($r),true);
	 				}
	 		
	 		}
*/
	 	
	 		// r
			$r = $this->curl(
					"http://cache.ms-cdn.com/index.php?do=get&name={$id}&ns={$ns}",
					array('u'=>'travis2','p'=>'gringo00')
				);	
				
				// cache?
				if ( $cache ) {
				//	$this->saveCache($cid,$r,$ttl);
				}
				
			// unfreeze
			return json_decode(stripslashes($r),true);
	 	
	 	}
	 	
	 	public function saveCloudCache($id,$data,$ns='tt') {
	 	
			// send to cache server
			$r = $this->curl(
					"http://cache.ms-cdn.com/index.php?do=save&name=".md5($id)."&ns={$ns}",
					array('u'=>'travis2','p'=>'gringo00'),
					array(
						'data' => json_encode($data),
					)
				);
				
			// unfreeze
			return $r;
	 	
	 	}	 	
	
		/* get user */
		protected function getUser() {
		
			// session
			$sess = $this->param('TT',false,$_COOKIE);
			$user = $this->param('TA',false,$_COOKIE);
			
				// no session
				if ( !$sess OR !$user ) {	
					return false;
				}
		
			// get session info
			$info = unserialize( base64_decode( $sess ) );
		
				// user match
				if ( $this->md5($info['user']) != $user ) {
					return false;
				}

			// get session from database
			$row = $this->row("SELECT * FROM `users` WHERE `id` = '??' AND `sid` = '??' ", array($info['user'],$info['sid']));
		
				// no row or row and no password
				// or row and ttl is reached
				// invalid session. force to login
				// again
				if ( !$row OR ( $row AND (time() > $row['ttl']) ) ) {
				
					// looks like a bad session
					// so we need to unset the session cookie
					setcookie("TT",false,time()+1,'/',DOMAIN);

					// go to login
					$this->go( $this->url('index'));
	
				}			
		 
		 	// save user 
		 	$this->user = $row;
		 	
		 	// fix info
		 	$this->user['info'] = unserialize($row['info']);
		 	
		 	// add u and p for curl
		 	$this->user['u'] = $row['user'];
		 	
		 	if ( $row['oauth'] ) {
		 	
		 		// get
		 		$tok = json_decode($row['oauth'],true);
		 	
				// reset oauth with the access tokens 
				$this->oauth = new TwitterOAuth(AUTH_KEY, AUTH_SECRET, $tok['oauth_token'], $tok['oauth_token_secret']);		 	
		 	
		 			// no oauth
		 			if ( !$this->oauth ) {
		 				
						// looks like a bad session
						// so we need to unset the session cookie
						setcookie("TT",false,time()+1,'/',DOMAIN);
	
						// go to login
						$this->go( $this->url('index'));		 				
		 				
		 			} 
		 	
		 	}
		 	else {
		 	
			 	// password
			 	if ( $row['password2'] ) {
			 		$this->user['p'] = base64_decode( base64_decode( $row['password2'] ) );
			 	}
			 	else {
				 	$this->user['p'] = $this->decrypt($row['password']);
				}		 	
		 	
		 	}		 
			
			// saerches
			if ( $row['searches'] ) {
			
				// make them
				$this->savedSearches = unserialize($row['searches']);
			
			}
			
			if ( !$this->savedSearches ) {
				$this->savedSearches = array();
			}
			
			// get all friends
			$this->allFriends = $this->getCloudCache($row['id'],'tt-friends',true);
						 	
		 	// add reque id
		 	$this->user['req'] = $info['req'];
		 	
		 	$this->uid = $row['id'];
		 	$this->req = $info['req'];
		 	$this->session = $info;
		 	
		 	// loged
		 	$this->loged = true;
		 	
		 	// set acounts
		 	$this->accounts = json_decode($row['accounts'],true);
		 	
		 	// add to accounts
		 	$this->accounts[$this->uid] = array( 'id' => $this->user['info']['screen_name'], 'sid' => $info['sid'], 'ts' => time(), 'pic' => $this->user['info']['profile_image_url'] );
		 
		}
	
		/* twitter */
		public function twitter($call,$params=null,$noerror=false) {
		
			// construct the call
			if ( strpos($call,'.json') === false ) {
				$url = "http://twitter.com/{$call}.json";
			}
			else {
				$url = "http://twitter.com/{$call}";
			}
		
			// what kind
			if ( isset($this->oauth) AND $this->oauth !== false ) {				
			
				$p = $params;
			
				// parse out the url 
				if ( !$params ) {
					
					// parse
					$parsed = parse_url($url,PHP_URL_QUERY);
					
					// xplode
					if ( $parsed ) {
						foreach ( explode('&',$parsed) as $q ) {
							list($k,$v) = explode('=',$q);
							$p[$k] = $v;
						}
					}
					
				}
			
				$r = $this->oauth->OAuthRequest($url, $p, ($params?'POST':'GET'));						
				
			}
			else {
				$r = $this->curl($url,$this->user,$params);
			}
			
			// check response 
			$j = json_decode($r,true);
			
			//var_dump($url,$j);
			
			// check for error
			if ( !$noerror AND (!is_array($j) OR @array_key_exists('error',$j)) )  {
								
				$s = var_export($_SERVER, true);				
								
				// send a main
				mail( "info@twittangle.com", "Twitter Error", $r . ' | ' . $s );
				
				return false;
			}
			
			// give back
			return $j;
			
		}	
		
		
		public function getLatestStatus($noparse=false) {
			
			// check cache 
			$cid = md5('user.last.status.'.$this->uid);
			$ttl = (60*2);
			
			$cache = $this->getCache($cid,$ttl);
			
				if ( !$cache ) {
					
					// get it 
					$r = $this->twitter("users/show/".$this->uid);
					
					
					if ( $r ) {
						$cache = $r['status']['text'];
					}
					else {
						$cache = $this->user['info']['status']['text'];
					}
					
					// save
					$this->saveCache($cid,$cache,$ttl);
					
				}
				
			// back
			if ( $noparse ) {
				return $cache;
			}
			else {
				return $this->parseStatus($cache);
			}
			
		}			
		
		/* get groups */
		public function getGroups($uid=false) {
			
			// uid
			if ( !$uid ) {
				$uid = $this->uid;
			}
			
			// check for cache 
			if ( array_key_exists('groups',$this->cache) ) {
				return $this->cache['groups'];
			}
			
			// cid 
			$cid = md5('groups.'.$uid);
			$ttl = (60*60*24);
			
			// check
			$cache = $this->getCache($cid,$ttl);
			
				// no
				if ( !$cache ) {
					
					// get them
					$sth = $this->query("SELECT g.*,
						(SELECT COUNT(m.group_id) FROM group_map as m WHERE m.group_id = g.id) as count 
						FROM groups as g 
						WHERE g.user = '??' ORDER BY count DESC ", 
					array($uid));
				
					// sam
					$cache = array();
					
						// go 
						while ( $row = $sth->fetch_assoc() ) {
							$cache[$row['id']] = $row;
						}
				
					// save
					$this->saveCache($cid,$cache,$ttl);
				
				}
			
			// save in local
			$this->cache['groups'] = $cache;
			
			// return
			return $cache;
			
		}
	
		/* get Group Users */
		public function getGroupUsers($gid) {
			
			// icd
			$cid = md5('group.'.$gid);
			$ttl = (60*60*24);
			
			// in session
			if ( array_key_exists($cid,$this->cache) ) {
				return $this->cache[$cid];
			}
		
			// cahce
			$cache = $this->getCache($cid,$ttl);
			
				// nope
				if ( !$cache ) {
				
					// call
					$sth = $this->query("SELECT * FROM group_map WHERE `group_id` = '??' ",array($gid));
				
					// nice
					$cache = array();
					
						while ( $row = $sth->fetch_assoc() ) {
							$cache[$row['friend_id']] = $row;
						}
				
					// save
					$this->saveCache($cid,$cache,$ttl);
				
				}
		
			// save
			$this->cache[$cid] = $cache;
		
			// give back
			return $cache;
		
		}
	
		/* get user ratings */
		public function getFriends($user=false) {
			
			// check in local cache first
			if ( array_key_exists('friends',$this->cache) ) {
				return $this->cache['friends'];
			}
			
			// user 
			if (!$user) {
				$user = $this->user['id'];
			}
			
			// cache
			$cid = 'friends.'.$user;
			$ttl = (60*60*24*14);
			
			// check
			$cache = $this->getCache($cid,$ttl);
			
				// no cache
				if ( !$cache ) {
									
					// get them 
					$sth = $this->query("SELECT * FROM friends WHERE `user` = '??' ",array($user));
					
					// cache
					$cache = array();
					
					// go throguh
					while ( $row = $sth->fetch_assoc() ) {
						$cache[$row['friend']] = array(
							'rating' => $row['rating'],
							'tags' => ( $row['tags'] ? explode(',',$row['tags']) : array() )
						);
					}
					
					// save it 
					$this->saveCache($cid,$cache,$ttl);
					
				}
				
				// local cache
				$this->cache['friends'] = $cache;
				
			// give back
			return $cache;
				
		}
		
		/* get popular tags */
		public function getPopularTags($n=1000) {
		
			// get their friends
			$friends = $this->getFriends();
			
			// tags
			$tags = array();
		
			// go through all tags
			foreach ( $friends as $f ) {
				// if tags
				if ( $f['tags'] ) {
					// add tags
					foreach ( $f['tags'] as $t ) {
						$t = trim($t);
						if ( array_key_exists($t,$tags) ) {
							$tags[$t] += 1;
						}
						else {
							$tags[$t] = 1;
						}
					}
				}
			}
			
			// sort 
			arsort($tags);
		
			// give back
			return array_slice($tags,0,$n);
		
		}
		
		/** get networks */
		public function getUserNetworks($id=null) {
		
			if ( !$id ) {
				$id = $this->uid;
			}
			
			if ( !$id ) {
				return array();
			}
		
			// cache
			$cid = md5("networks.user.{$id}");
			$ttl = (60*60*24);
			
			// check
			$cache = $this->getCache($cid,$ttl);
			
				// n
				if ( !$cache ) {
					
					// get my networks
					$sql = "
						SELECT 
							n.*
						FROM
							networks as n,
							network_map as m 
						WHERE
							n.id = m.network_id AND 
							m.user_id = '??'
						ORDER BY n.title
					";
					
					// go for it
					$sth = $this->query($sql,array($id));
					
					// array
					$cache = array();
					
						while ( $row = $sth->fetch_assoc() ) {
							$cache[$row['id']] = $row;
						}
					
					// save
					$this->saveCache($cid,$cache,$ttl);
					
				}		
		
			return $cache;
		
		}
		
		/** getNetworkUpdates */
		public function getNetworkUpdates($id,$page=1,$convert=false) {		
		
			// get latest updates
			$cid = md5("network.updates.{$id}.{$page}");
			$ttl = 30;
		
			// get 
			$updates = $this->getCache($cid,$ttl);
			
				// nope
				if ( !$updates ) {
					
					// go for it on search
					$resp = $this->curl("http://search.twitter.com/search.json?page={$page}&rpp=20&q=".urlencode("#tt:".$id));
				
					// bad
					if ( !$resp ) {
						$this->go("/500");	
					}
					
					// set
					$updates = json_decode($resp,true);
					
					if ( count($updates['results']) == 0 ) {
						return array(array(),false,false);
					}
				
					// save
					$this->saveCache($cid,$updates,$ttl);
				
				}
			
			$next = "";
			$prev = "";
			
			// figure out what page
			if ( array_key_exists('next_page',$updates) ) {
				if ( preg_match("/\?page=([0-9]+)/",$updates['next_page'],$match) ) {
					$next = $match[1];
					$on = $next - 1;
					$prev = ( $on != 0 ? $on -1 : false );
				}
			}
			
			// convert
			if ( $convert ) {			
			
				// u
				$u = array();
				
				// eacj
				foreach ( $updates['results'] as $item ) {
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
					$u[$d['id']] = $d;
				}	
				
				$updates = $u;
			
			}
			
			return array($updates,$next,$prev);				
		
		}
	
		public function getNetworkMembers($id) {
		
		// members
		$cid = md5("network.members.hp.{$id}");
		$ttl = (60*60);
		
		// members
		$members = $this->getCache($cid,$ttl);
		
			// nope
			if ( !$members ) {
				
				// sql
				$sql = "
					SELECT 
						u.user,
						u.pic
					FROM 
						network_map as m, 
						users as u 
					WHERE
						m.network_id = '??' AND 
						m.user_id = u.id
					ORDER BY m.timestmp DESC
					LIMIT 100
				";
			
				// go 
				$sth = $this->query($sql,array($id));
			
				// put 
				$members = array();
				
				// what 
				while ( $row = $sth->fetch_assoc()) {
					$members[] = $row;
				}
			
				// save
				$this->saveCache($cid,$members,$ttl);
			
			}
			
			return $members;		
		
		}
		
		/**
		 * format and display a timestamp 
		 * @method	displayTimestamp
		 * @param	{Int} timestamp
		 * @return	{String} formatted timestamp 
		 */
		public function displayTimestamp($ts,$short=false) {
		
			// figure the time 
			$diff = time()-$ts;
		
			// what up 
			if ( $diff < 60 ) {
				if ( $short ) {
					$msg = "seconds ago";
				}
				else {
					$msg = "a couple of seconds ago";
				}
			}
			else if ( $diff < 60*60 ) {
				$n = round($diff/60);
				$msg =  $n. " minute".($n==1?'':'s')." ago ";
			}
			else if ( $diff < 60*60*24 ) {
				$n = round($diff/(60*60));
				$msg = $n . " hour".($n==1?'':'s')." ago ";
			}
			else {
				$msg = " on " . date("m/d/y g:ia",$ts);
			}
			
			// msg
			return $msg;
		
		}		
		
		/* getTwitterUser */
		public function getTwitterUser($id) {
			
			// check cache
			$cid = md5("twitter.user.{$id}");
			$ttl = (60*60*24);
			
			// cache
			$cache = $this->getCache($cid,$ttl);
			
				// yes no?
				if (!$cache) {
				
					// check our database
					$row = $this->row("SELECT info FROM `users` WHERE `id` = '??' ",array($id));
				
					// ro 
					if ( !$row ) {
						
						// go to twitter
						$j = $this->twitter("users/show/{$id}",false,true);
					
							// no return bad
							if ( !$j ) {
								return false;
							}
					
						// set it 
						$cache = $j;
					
					}
					else {
						$cache = unserialize($row['info']);
					}
				
					// save it 
					$this->saveCache($cid,$cache,$ttl);
				
				}
		
			// give it back
			return $cache;
		
		}

		/* getminiimg */
		public function getUserPic($pic) {
			
			// statis
			if ( strpos($pic,'static.twitter.com') !== false ) {
				return $pic;
			}
			
			// str pos
			if (strpos($pic,'http://s3.amazonaws.com/twitter_production/profile_images/') === false ) {
				$pic = 'http://s3.amazonaws.com/twitter_production/profile_images/' . $pic;
			}
		
			// return
			return $pic;
		}
		
		/* getminiimg */
		public function getMiniPic($pic) {
			
			// statis
			if ( strpos($pic,'static.twitter.com') !== false ) {
				return $pic;
			}			
			
			// str pos
			if (strpos($pic,'http://s3.amazonaws.com/twitter_production/profile_images/') === false ) {
				$pic = 'http://s3.amazonaws.com/twitter_production/profile_images/' . $pic;
			}
		
			// return
			return preg_replace("/\/([a-zA-Z0-9\_\.\-]+)_normal\.([a-zA-Z]{3,3})/","/$1_mini.$2",$pic);
		}
		
		/* getTweets */
		public function getTweets($since,$page=1,$tag=false,$per=20,$pager=true,$group=false) {	
			
			// offset 
			$off = ($page-1) * $per;
	
			// t 
			$t = $this->user['last_timestp'];			
		
				// figure since offset
				switch($since) {
					case 'two':
						$t -= (60*60*2); break;
					case 'eight':
						$t -= (60*60*8); break;
					case 'yesterday':
						$t -= (60*60*24); break;
				}
				
				// max age
				$max = (60*60*24*5);
				
				// is too big
				if ( time()-$t > $max ) {
					$t = time()-$max;
				}
				
				$Since = $t;
				
			// no last id
			$last_id = 0;
		
			// get from cache 
			$cid = md5("timeline.{$since}.{$this->user['u']}");
			$ttl = (60*4);
		
			// check 
			$cache = false; // $this->getCache($cid,$ttl);
			
			// go multiple pages
			$multi = true;				
			$uri  = 'statuses/friends_timeline.json';
								
			// no cache
			if ( !$cache ) {
						
				// what's the last time we requested info
				$last = date('c',$t);
				
				// hlder
				$data = array();
				$cache = array();
				$i = 2;					
				
				if ( $last_id > 0 ) {
					$uri .= "?since_id=$last_id&count=200&page=1";
				}
				else {
					$uri .= "?count=200&page=1";
				}
							
				// get our first page
				$data = $this->twitter($uri);
				
				// copy into array
				$cache = $data;
								
					// get everything. up to 10 calls
					while ( count($data) == 200 AND $i < 3 AND $multi ) {
					
						if ( $last_id > 0 ) {
							$uri = "statuses/friends_timeline.json?since_id=$last_id&count=200&page={$id}";
						}
						else {
							$uri = "statuses/friends_timeline.json?count=200&page={$id}";
						}
					
						// make call
						$data = $this->twitter($uri);
										
						// merge to all
						$cache = array_merge($cache,$data);
						
						// up the i
						$i++;												
						
					}
					
					if ( count($cache) > 0 ) {
						$last_id = $cache[count($cache)-1]['id'];					
					}					
					
				// if nothing
				if ( count($cache) == 0 ) {
				
					// overried the get
				
				}
				else {
						
					// now go through and get all ratings of people i'm following
					// and bubble the people i like to the top
					$ratings = $this->getFriends();
					
					// loop by 10 mintes
					$byTime = array();
					
						foreach ( $cache as $item ) {
							
							// ts
							$ts = strtotime( $item['created_at'] );
							
							// timestamp 
							$item['timestamp'] = $ts;
							
							// key
							$key = date("YmdH",$ts);
							
							// add to this index
							$byTime[$key][] = $item;
														
						}
						
					// byRating
					$byRating = array();
					
						// foreach 
						foreach ( $byTime as $key => $items ) {
						
							// list
							$list = array();
							
							// each item in the list
							foreach ( $items as $item ) {
								
								// user
								$u = $item['user']['id'];
								
								// rating
								$rating = ( array_key_exists($u,$ratings) ? $ratings[$u]['rating'] : 0 );
								
								// add simple timestamp to item

								$item['rating'] = $rating;
								
								// save by rating
								$list[$rating][] = $item;
								
							}
							
							// now sort by key
							krsort($list);
						
							// now merge with byRating
							$byRating = array_merge($byRating,$list);
						
						}
						
					// falt
					$cache = array();
					
						// now flatten out the list
						foreach ( $byRating as $list ) {

							// now merge to flat 
							$cache = array_merge($cache,$list);
							
						}
					
						
					// update the last time
					$this->query("UPDATE `users` SET `last_timestp` = UNIX_TIMESTAMP(), `last_id` = '??' WHERE `id` = '??' ",array($last_id,$this->user['id']) );
					
				}
											
				// save in cache
				if ( !$this->param('live') ) {
					$this->saveCache($cid,$cache,$ttl);
				}
								
			}				
			
				
				$this->_insertTweets($cache);			
			
				$holder = array();
			
				// get only last 
				foreach ( $cache as $item ) {
					if ( $Since <= strtotime($item['created_at']) ) {
						$holder[] = $item;
					}
				}
								
				$cache = $holder;
						
				// check for tag 
				if ( $tag ) {
				
					// firends
					$friends = $this->getFriends();
														
					// new 
					$new = array();
					
					// go through each and see if friends 
					foreach ( $cache as $item ) {
						$u = $item['user']['id'];	
																		
						if ( array_key_exists($u,$friends) AND in_array($tag, $friends[$u]['tags']) ) {
							$new[] = $item;
						}
					}

					// reset cache
					$cache = $new;
				
				}
				
				// group
				if ( $group ) {
					
					// get groups 
					$users = $this->getGroupUsers($group);
				
					// new 
					$new = array();
					
					// go through each and see if friends 
					foreach ( $cache as $item ) {
						$u = $item['user']['id'];	
																		
						if ( array_key_exists($u,$users) ) {
							$new[] = $item;
						}
					}

					// reset cache
					$cache = $new;
				
				}
			
			// total
			$total = count($cache);
			
			// raw
			$r = array_slice($cache,$off,$per);
			$raw = array();
			
			// make nice list 
			foreach ( $r as $item ) {
				$raw[$item['id']] = $item;
			}
			
			// tag
			$tagp = ($tag?";tag={$tag}":"");
			
			// how many pages 
			$n = ($total == 0 ? 0 : ceil($total/$per));
			
			// pager url
			if ( $group ) {
				$pagerUrl = "/groups?since={$since};id={$group}{$tagp};page=";
			}
			else {
				$pagerUrl = "/home?since={$since}{$tagp};page=";
			}
			
			// html
			$html = "";		
			
			$html .= "
				<ul class='timeline' id='status-list'>
			";

			// make html				
			foreach ( $r as $item ) {
				$html .= $this->_bit_displayStatus($item,$this->param('live'));
			}

			$html .= "
				</ul>
			";
			
			if ( $pager === true ) {
				
				$html .= "
					<ul class='nav'>
						<li class='bottom'>	
				";
		
					if ( $total > 0 ) {
					
						$html .= "Page: ";				
					
						for ($i=1;$i<=$n;$i++) {
							$html .= " <a class='".($i==$page?'b':'')."' href='{$pagerUrl}{$i}'>{$i}</a> ";
						}
						
					}
					
				$html .= "</li></ul>";	
			}
			
			// none
			if ( $total == 0 ) {
				$html = "
					<div class='no-timeline'>
						<h3>No Tweets</h3>
						<p>We couldn't find any tweets that matched your request. Might want to try something else</p>
					</div>
				";
			}			
		
			// give back list
			return array($html,$raw,$n);
		
		}
		
		// new get tweets
		public function newGetTweets($since,$page=1,$tag=false,$per=20,$pager=true,$group=false) {
		
			// figure since offset
			switch($since) {
				case 'two':
					$t -= (60*60*2); break;
				case 'eight':
					$t -= (60*60*8); break;
				case 'yesterday':
					$t -= (60*60*24); break;
			}	
	
			// start out by requesting the last 
			$last_id = $this->user['last_id'];
		
			// now loop through twitter and request that stuff
			$i = 0;
			
			// tweets
			$tweets = array();
			
			// go for it
			while ( count($data) == 200 AND $i < 3 ) {
			
				// uri		
				$uri = "statuses/friends_timeline.json?since_id=$last_id&count=200&page={$i}";
			
				// make call
				$data = $this->twitter($uri);		
				
				// what up 
				$tweets = array_merge($tweets,$data);
		
				// set last it
				if ( count($data) !== 0 ) {
					$last_id = $data[0]['id'];
				}
		
				// up the i
				$i++;
			
			}
		
			// now lets add these tweets
			foreach ( $tweets as $item ) {
				$this->query("
						INSERT INTO twittangle_tweets.tweets 
						SET `for_user` = '??', `id` = '??', `user` = '??', `name` = '??', `text` = '??', `groups` = '??', `data` = '??', `timestamp` = '??'
					",array(
					$this->uid,
					$item['id'],
					$item['user']['id'],
					$item['user']['screen_name'],
					$item['text'],
					$groups,
					json_encode($item),
					strtotime($item["created_at"])
				));				
			}
		
		}
	
		public function _insertTweets($tweets) {
			
			// now - something
	//		$ts = time() - ( 60 * 60 * 24 * 5 );
			
			// remove old ones 
	//		$this->query(" DELETE FROM twittangle_tweets.tweets WHERE `timestamp` > '$ts' AND `for_user` = '??' ",array($this->uid));
		
			// get all groups
			$groups = $this->getGroups();
			
			// groups_by_friend
			$groups_by_friend = array();
			
			foreach ( $groups as $gid ) {
				$g_users = $this->getGroupUsers($gid);
				
				// for for it
				foreach( $g_users as $u ) {
					$groups_by_friend[$u['friend_id']][] = $gid;
				}
				
			}
			
			// add to database
			foreach ( $tweets as $item ) {
				
				$groups = "";
				
				// yes
				if ( array_key_exists($item['user']['id'],$groups_by_friends) ) {
					$groups = implode(',',$groups_by_friends[$item['user']['id']]);
				}
				
				$this->query("
						INSERT INTO twittangle_tweets.tweets 
						SET `for_user` = '??', `id` = '??', `user` = '??', `name` = '??', `text` = '??', `groups` = '??', `data` = '??', `timestamp` = '??'
					",array(
					$this->uid,
					$item['id'],
					$item['user']['id'],
					$item['user']['screen_name'],
					$item['text'],
					$groups,
					json_encode($item),
					time()
				));
			}		
		
		}
	
		public function parseStatus($txt) {
		
			// strip tags from the stat 
			$txt = strip_tags( html_entity_decode($txt,ENT_QUOTES) );

			// add entities back
			$txt = htmlentities($txt,ENT_QUOTES,"utf-8");

			$types = $this->linkTypes;
			$typeTitles = $this->linkTypeTitles;
			
			// special
			$special = array();
				
			// pick up urls
			$hasLinks = preg_match_all("/\b(http\:\/\/[^\s]+)\b/",$txt,$links,PREG_PATTERN_ORDER);
			
				// go through each link and p	ing
				// to it
				if ( $hasLinks ) {
					foreach ( $links[1] as $lnk ) {
											
						// random name 
						$name = substr(md5(time()*rand(9,999)),0,5);
						$found = false;
						
						foreach ( $types as $t ) {
							if ( strpos($lnk,$t[0]) !== false ) {
								
								if ( $t[1] == 'expand' ) {
									$special[] = array('name'=>$name,'url'=>$lnk);									
								}
			
								$found = true;
								
								if ( $t[1] == 'expand' ) {
									$txt = str_replace($lnk," <a id='{$name}' class='ext-link-catch' href='$lnk'>$lnk</a> <a class='hide view-{$t[1]}' id='{$t[1]}|{$name}' href='$lnk'>({$typeTitles[$t[1]]})</a> ",$txt);									
								}
								else {
									$txt = str_replace($lnk," <a id='{$name}' class='ext-link-catch' href='$lnk'>$lnk</a> <a class='view-{$t[1]}' id='{$t[1]}|{$name}' href='$lnk'>({$typeTitles[$t[1]]})</a> ",$txt);
								}
							}
						}
						
						// 
						
						// not found
						if ( !$found ) {
							$txt = str_replace($lnk,"<a class='ext-link-catch' href='$lnk'>$lnk</a>",$txt); 					
						}
						
					}
		
				
				}
							
			// pick up at
			$txt = preg_replace("/@([a-zA-Z0-9\_]+)\b/i","@<a href='http://www.twittangle.com/user/$1'>$1</a>",$txt);				
		
				// add to links
				if ( $hasLinks AND !defined('IN_MOBILE') ) {
					$txt .= "<script type='text/javascript'> TT.addToQueue(function(){ TT.Global.addPreLink(".json_encode($special)."); }); </script>";
				}
				
			// check for network
			$hasNetwork = preg_match("/\#tt\:([0-9]+)/",$txt,$m);
			
				// see if it's a valid network
				if ( $hasNetwork ) {
					
					$id = $m[1];
					
					// check
					$network = $this->row("SELECT slug,title FROM `networks` WHERE `id` = '??' ",array($id));
					
					// yes
					if ( $network ) {
						$txt = str_replace($m[0],"<a class='b' href='http://www.twittangle.com/networks/network/{$network['slug']}'>in {$network['title']}</a>",$txt);
					}
					
				}
				
			// hashes
			$txt = preg_replace("/ \#([a-zA-Z0-9]+)/"," <a href='http://www.twittangle.com/search?q=%23$1'>#$1</a>",$txt);
						
			return $txt;
		
		}
	
	/************************************ 		 
	  Bits	
	 ************************************/
	 
	 	// display
		public function _bit_displayStatus($row,$live=false,$pic=false,$defer=true) {
			
			$txt = $this->parseStatus($row['text']);		
			
			// image
			$img = strip_tags($row['user']['profile_image_url']);
						
			$html = "
				<li class='status' id='status|{$row['id']}'>
				
			";
			
			if ( $this->beta ) {
				$html .= "
					<div class='hd'><a id='up|{$row['user']['id']}|{$row['user']['screen_name']}' class='user-panel' href='".$this->url('user',$row['user'])."'><img  class='".($defer?'defer':'')."' width='50' height='50' src='".BLANK."' style='background-image:url({$img});'></a></div>
				";
			}
			else {
				$html .= "
					<div class='hd'><img id='up|{$row['user']['id']}|{$row['user']['screen_name']}' class='user-panel ".($defer?'defer':'')."' width='50' height='50' src='".BLANK."' style='background-image:url({$img});'></div>
				";
			}
					
			$html .= "
					<div class='bd'>
						<a class='b' href='/user/{$row['user']['screen_name']}'>{$row['user']['screen_name']}</a> 
						{$txt}
			";
		
			
			$html .= "
					</div>
					<div class='ft'>
						<span>".$this->displayTimestamp( strtotime($row["created_at"]) )." from ".str_replace("<a ","<a class='ext-link-catch' ",$row['source'])."
			";
			
				// reply
				if ( $row['in_reply_to_status_id'] ) {
					$html .= "<a class='show-reply' id='reply|{$row['in_reply_to_status_id']}' target='_blank' href='http://twitter.com/{$row['in_reply_to_screen_name']}/status/{$row['in_reply_to_status_id']}'>in reply to {$row['in_reply_to_screen_name']}</a>";
				}
				
				$html .= "</span>";
			
				// loged in 
				if ( !$live AND $this->loged ) {
					
		
						$html .= "
							<span class='usr'>
								<a href='#reply' class='reply' id='reply|{$row['id']}'>Reply</a> &nbsp;
								<a href='#retweet' class='retweet' id='retweet|{$row['id']}'>Re-Tweet</a> &nbsp;
								".($row['favorited']?'<em class="b">Favorite</em>':"<a href='#fav' class='fav' id='fav|{$row['id']}'>Favorite</a>")."
							</span>						
						";
			
				
					
				}				
			
			$html .= "
					</div>
				</li>
			";
			
			return $html;
		
		}
	
	}

/*
 * Abraham Williams (abraham@abrah.am) http://abrah.am
 *
 * Basic lib to work with Twitter's OAuth beta. This is untested and should not
 * be used in production code. Twitter's beta could change at anytime.
 *
 * Code based on:
 * Fire Eagle code - http://github.com/myelin/fireeagle-php-lib
 * twitterlibphp - http://github.com/poseurtech/twitterlibphp
 */


/**
 * Twitter OAuth class
 */
class TwitterOAuth {/*{{{*/
  /* Contains the last HTTP status code returned */
  private $http_status;

  /* Contains the last API call */
  private $last_api_call;

  /* Set up the API root URL */
  public static $TO_API_ROOT = "http://twitter.com";

  /**
   * Set API URLS
   */
  function requestTokenURL() { return self::$TO_API_ROOT.'/oauth/request_token'; }
  function authorizeURL() { return self::$TO_API_ROOT.'/oauth/authorize'; }
  function accessTokenURL() { return self::$TO_API_ROOT.'/oauth/access_token'; }

  /**
   * Debug helpers
   */
  function lastStatusCode() { return $this->http_status; }
  function lastAPICall() { return $this->last_api_call; }

  /**
   * construct TwitterOAuth object
   */
  function __construct($consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL) {/*{{{*/
    $this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
    $this->consumer = new OAuthConsumer($consumer_key, $consumer_secret);
    if (!empty($oauth_token) && !empty($oauth_token_secret)) {
      $this->token = new OAuthConsumer($oauth_token, $oauth_token_secret);
    } else {
      $this->token = NULL;
    }
  }/*}}}*/


  /**
   * Get a request_token from Twitter
   *
   * @returns a key/value array containing oauth_token and oauth_token_secret
   */
  function getRequestToken() {/*{{{*/
    $r = $this->oAuthRequest($this->requestTokenURL());	
    $token = $this->oAuthParseResponse($r);
    $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
    return $token;
  }/*}}}*/

  /**
   * Parse a URL-encoded OAuth response
   *
   * @return a key/value array
   */
  function oAuthParseResponse($responseString) {
    $r = array();
    foreach (explode('&', $responseString) as $param) {
      $pair = explode('=', $param, 2);
      if (count($pair) != 2) continue;
      $r[urldecode($pair[0])] = urldecode($pair[1]);
    }
    return $r;
  }

  /**
   * Get the authorize URL
   *
   * @returns a string
   */
  function getAuthorizeURL($token) {/*{{{*/
    if (is_array($token)) $token = $token['oauth_token'];
    return $this->authorizeURL() . '?oauth_token=' . $token;
  }/*}}}*/

  /**
   * Exchange the request token and secret for an access token and
   * secret, to sign API calls.
   *
   * @returns array("oauth_token" => the access token,
   *                "oauth_token_secret" => the access secret)
   */
  function getAccessToken($token = NULL) {/*{{{*/
    $r = $this->oAuthRequest($this->accessTokenURL());
    $token = $this->oAuthParseResponse($r);
    $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
    return $token;
  }/*}}}*/

  /**
   * Format and sign an OAuth / API request
   */
  function oAuthRequest($url, $args = array(), $method = NULL) {/*{{{*/
    if (empty($method)) $method = empty($args) ? "GET" : "POST";
    $req = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $args);
    $req->sign_request($this->sha1_method, $this->consumer, $this->token);
    switch ($method) {
    case 'GET': return $this->http($req->to_url());
    case 'POST': return $this->http($req->get_normalized_http_url(), $req->to_postdata());
    }
  }/*}}}*/

  /**
   * Make an HTTP request
   *
   * @return API results
   */
  function http($url, $post_data = null) {/*{{{*/
    $ch = curl_init();
    if (defined("CURL_CA_BUNDLE_PATH")) curl_setopt($ch, CURLOPT_CAINFO, CURL_CA_BUNDLE_PATH);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //////////////////////////////////////////////////
    ///// Set to 1 to verify Twitter's SSL Cert //////
    //////////////////////////////////////////////////
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    if (isset($post_data)) {
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    }
    $response = curl_exec($ch);
    $this->http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $this->last_api_call = $url;
    curl_close ($ch);
    return $response;
  }/*}}}*/
}/*}}}*/


?>