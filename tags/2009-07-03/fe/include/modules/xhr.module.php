<?php

class xhr extends Tangle {
	
	function __construct() {
	
		$this->whitelist = array('embed','links','getReply');
		
		/* construct */
		parent::__construct();
	
		// get an act
		$act = $this->param('act',false,false,'alpha');
	
		// header
		header("Content-Type: text/javascript");
	
		// figure if it's good
		if ( !method_exists($this,$act) ) {
			$this->bad("Invalid Request");
		}
		

		// req 
		// match a req
		if ( !in_array($act,$this->whitelist) AND $this->param('req') != $this->user['req'] ) {
			$this->bad("Invalid Request Token");
		}		

		if ( !in_array($act,$this->whitelist) AND !$this->loged ) {
			$this->bad("Invalid Request Token");
		}	
	
		// do it 
		call_user_func(array($this,$act));	
	
		// exit
		exit();
	
	}
	
	public function trending() {
	
		// check for cache
		$cid = md5($this->uid.'trending');
		$ttl = (60*2);
		
			// check
			if ( ($cache = $this->getCache($cid,$ttl)) ) {
				$this->good(array('html'=>$cache));
			}

		// get all text from 
		$sth = $this->query("SELECT text,data FROM twittangle_tweets.tweets WHERE `for_user` = '??' ", array($this->uid) );
	
		// counts
		$links = array();
		$at = array();
	
		// get them all
		while ( $row = $sth->fetch_assoc() ) {
		
			// text
			$text = strtolower(trim($row['text']));

			// d
			$d = json_decode($row['data'],true);
			
			// check for things users are talking about 
			if ( strpos($text,'@') !== false ) {
				if ( preg_match_all("/\@([a-z0-9\_]+)/",$text,$m,PREG_PATTERN_ORDER) ) {
					foreach ( $m[1] as $u ) {
						$at[] = $u;
					}
				}
			}
		
			// get links 
			if ( strpos($text,'http://') !== false ) {
				
				// get links
				if ( preg_match_all("/\b(http\:\/\/[^\s]+)\b/",$text,$m,PREG_PATTERN_ORDER) ) {
					foreach ( $m[1] as $l )	 {
						$links[] = $l;						
					}
				}
			
			}
		
		}
		
		// count
		$links = array_count_values($links);		
		$at = array_count_values($at);

		// sort
		arsort($links);
		arsort($at);

/*
		$links = array_slice($links,0,20,true);
		$at = array_slice($at,0,20,true);

		shuffle( $links );
		shuffle( $at );
*/
		
		// splice out the top 10
		$links = array_slice($links,0,10,true);
		$at = array_slice($at,0,10,true);
		
		$final = $this->getManyUrls(array_keys($links));
	
		// add it 
		$html = "
			<div class='hd'><h3>Trends From Your Timeline</h3></div>
			<div class='bd'>
				<h4>Popular Links</h4>
				<ul>
		";
		
			foreach ( $final as $l ) {
				
				// link
				$link = trim($l);
				
				// $l 
				$l = str_replace(array("http://www.","http://"),"",$link);
				
				// short	
				$short = ( strlen($l) > 20 ? substr($l,0,20).'...' : $l );
		
				// make it
				$html .= "<li><a class='bubble' title='{$link}' href='$link' target='_parent'>{$short}</a></li>";
	
			}
		
		$html .= "
				</ul>
				
				<h4>Popular @</a>
				<ul>
		";
		
			foreach ( $at as $usr => $cnt ) {
				$html .= "<li><a href='".$this->url('user',array('screen_name'=>$usr))."'>{$usr}</a></li>";
			}
		
		$html .= "		
				</ul>
			</div>
		";
		
		// save
		$this->saveCache($cid,$html,$ttl);
		
		// html
		$this->good(array('html'=>$html));
		
	}
	
	public function realtime($return=false) {
	
		// query
		$q = urldecode(strtolower($this->param('q')));
	
		// sql
		$tbl = "twittangle_tweets.tweets";
		$where = array( " `for_user` = '".$this->uid."'" );
		
			// from:{user}
			if ( preg_match("/\bfrom\:([a-zA-Z0-9\_]+)\b/i",$q,$m) ) {
			
				$q = str_replace("from:{$m[1]}","",$q);			
			
				// is it me
				if ( $m[1] == 'me' ) {
					$m[1] = $this->user['screen_name'];
				}
			
				// where
				$where[] = " LOWER(`name`) = '".$this->clean($m[1])."'";
				
			}
			
			
			// to:{user}
			if ( preg_match("/\bto\:([a-zA-Z0-9\_]+)\b/i",$q,$m) ) {
			
				$q = str_replace("to:{$m[1]}","",$q);			
			
				// is it me
				if ( $m[1] == 'me' ) {
					$m[1] = $this->user['info']['screen_name'];
				}			
			
				$where[] = " LOWER(`text`) LIKE '@".$this->clean($m[1])."%%' ";
				
			}		
				
				
			// @{user}
			if ( preg_match("/@([a-zA-Z0-9\_]+)/i",$q,$m) ) {
			
				$q = str_replace("@{$m[1]}","",$q);			
			
				// is it me
				if ( $m[1] == 'me' ) {
					$m[1] = $this->user['info']['screen_name'];
				}
						
				$where[] = " LOWER(`text`) LIKE '%@".$this->clean($m[1])."%' ";

	
			}
			
			
			// friend:{name}
			if ( preg_match("/\bfriend:([a-zA-Z0-9\_]+)\b/i",$q,$m) ) {
				// where
				$where[] = " LOWER(`name`) LIKE '%".$this->clean($m[1])."%'";
				$q = str_replace("friend:{$m[1]}","",$q);
			}					
			
			
			// has:link
			if ( strpos($q,"has:link") !== false ) {
				$where[] = " LOWER(`text`) LIKE '%http://%' ";
				$q = str_replace("has:link","",$q);
			}
			
			// has:pic
			if ( strpos($q,"has:pic") !== false ) {
				$where[] = " LOWER(`text`) LIKE '%twitpic.com%' ";
				$q = str_replace("has:pic","",$q);
			}

			// has:video
			if ( strpos($q,"has:video") !== false ) {
				$where[] = " LOWER(`text`) LIKE '%youtube.com%' ";
				$q = str_replace("has:video","",$q);
			}			
			
			
			// is there still text 
			if ( trim($q) != "" ) {
				$where[] = " LOWER(`text`) LIKE '%??%' ";
			}						
	
	
			// run it
			$sql = " SELECT * FROM {$tbl} WHERE " . implode(' AND ',$where) . " ORDER BY `id` DESC LIMIT 30 ";
	
		// run it 
		$sth = $this->query($sql,array($q));
		
		// html
		$html = "";
		$raw = array();
		$max = 0;
		
		// give back results
		while ( $row = $sth->fetch_assoc() ) {
			
			// make it
			$item = json_decode( gzuncompress( base64_decode( $row['data'] ) ),true);
			
			// raw
			$raw[$row['id']] = $item;
			
			// reset text
			$item['text'] = $row['text'];		
		
			// add
			$html .= $this->_bit_displayStatus($item,false,true,false); 			
			
			// max
			if ( $row['id'] > $max ) {
				$max = $row['id'];
			}
			
		}
		
		// get bootstarp
		list($html,$bs) = $this->parseXhrData($html);
		
		$this->good(array('html'=>$html,'raw'=>$raw,'max'=>$max,'bootstrap'=>$bs));
	
	}
	

	
	/* searchSuggest */
	public function searchSuggest() {
	
		// q
		$q = $this->param('q');
	
		// search for friends
		$friends = array();
		
			foreach ( $this->allFriends as $f ) {
				if (strpos($f['sn'],$q) !== false ) {
					$friends[] = $f;
				}
				if ( count($friends) == 10 ) {break;}
			}
			
		// st
		$r = 0;
		$html = "";
		
		// are friends
		if ( count($friends) > 0 ) {
		
			// html
			$html .= "<h3>Friends</h3><ul class='friends'>";
			
			// make nice
			foreach ( array_slice($friends,0,10) as $usr ) {
	
				// mini
				$mini = $this->getMiniPic($usr['img']);
			
				// echo 
				$html .= "
					<li>
						<a href='/home/friend/{$usr['id']}'>
							<img class='defer' src='".BLANK."' style='background-image: url($mini)'>
							{$usr['name']} <div>{$usr['sn']}</div>
						</a>
					</li>
				";
	
				$r++;
	
			}
			
			// end
			$html .= "</ul>";
			
		}
		
			// zero r
			if ( $r == 0 ) {
				$this->good(false);
			}
		
		// return
		$this->good(array('html'=>$html));
	
	}
	
	public function searchtwitter() {
	
		// q
		$q = $this->param('q');	

		// run it 
		$r = $this->curl("http://search.twitter.com/search.json?rpp=10&page={$page}&q=".urlencode($q));		
	
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
			
			// add
			$html .= $this->_bit_displayStatus($timeline[$item['id']],false,true,false); 			
										
		}		
	
		// get bootstarp
		list($html,$bs) = $this->parseXhrData($html);	
	
		$this->good(array('html'=>$html,'raw'=>$timeline[$item['id']],'bootstrap'=>$bs));
	
	}
	
	/* suggestNetworks */
	public function suggestNetworks() {
		
		// is it cached
		$cid = md5('suggest.networks.'.$this->uid);
		$ttl = (60*30);
		
		// check cache
		$cache = $this->getCache($cid,$ttl);
	
		// get my top friends
		if ( !$cache ) { 
	
				// get some high rated friends
				$friends = $this->getFriends();
				
				// hold
				$high = array();
			
				// get highest rated 
				foreach ( $friends as $id => $i ) {
					if ( $i['rating'] > 100 ) {
						$high[] = $id;
					}
				}
			
				// now take a rand sample of 20 
				shuffle($high);	
								
					// not enoguh
					if ( count($high) < 10 ) {
						$this->good(false);
					}
					
			// what
			$networks = array();
			
			// go for it
			foreach ( array_slice($high,0,50) as $id ) {
			
				// get networks
				$net = $this->getUserNetworks($id);
				
				// push them
				foreach ( $new as $n ) {
					$networks[$n['id']] = $n;
				}
				
			}
			
			// save
			$this->saveCache($cid,$networks,$ttl);
					
			// smae
			$cache = $networks;		
					
		}
		
			// none
			if ( count($cache) == 0 ) {
			
				// cache
				$cache = array();
			
				// get featured
				$sth = $this->query("SELECT * FROM `networks` WHERE FIND_IN_SET('0',cats)");
				
					while ( $row = $sth->fetch_assoc() ) {
						$cache[] = $row;
					}
		
				// save these in cache		
				$this->saveCache($cid,$cache,$ttl);			
			
			}		
				
		// my networks
		$my = $this->getUserNetworks();		
								
		// suffle
		shuffle($cache);		
		
		// html
		$html = "<ul class='networks'>";
		
			foreach ( array_slice($cache,0,10) as $n ) {
				if ( !array_key_exists($n['id'],$my) ) {
					$html .= "<li><a href='".$this->url('network',$n)."'>{$n['title']}</a></li>";
				}
			}
		
		// end
		$html .= "</ul>";
		
		// good
		$this->good(array('html'=>$html));
	
	}
	
	/* suggestUsers */
	public function suggestUsers() {
		
		// if it's already saved in cache don't get it again
		$cid = md5('suggest.friends.'.$this->uid);
		$ttl = (60*30);
		
		$sugg = $this->getCache($cid,$ttl);
		
		// if yes we're done
		if ( $sugg ) {
			$this->good(array('html'=>$sugg,'cached'=>true));
		}
	
	
		// check for cache
		$cid = md5('suggest.friends.top.'.$this->uid);
		$ttl = (60*60*12);
	
		// get 
		$cache = $this->getCache($cid,$ttl);
		
			// no cahce
			if ( !$cache ) { 
		
				// get some high rated friends
				$friends = $this->getFriends();
				
				// hold
				$high = array();
			
				// get highest rated 
				foreach ( $friends as $id => $i ) {
					if ( $i['rating'] > 100 ) {
						$high[] = $id;
					}
				}
			
				// now take a rand sample of 20 
				shuffle($high);	
								
					// not enoguh
					if ( count($high) < 10 ) {
						$this->good(false);
					}
			
				// $all
				$top = array();					
			
				// get a list of their friends
				foreach ( array_slice($high,0,10) as $id ) {
				
					// do it 
					$friends = $this->twitter("friends/ids/{$id}",false,true);
				
					// go
					foreach ( $friends as $uid ) {
						if ( !array_key_exists($uid,$this->allFriends) AND $uid != $this->uid ) {
							$top[$uid]++;
						}
					}
				
				}
				
				// sort by value
				arsort($top);
								
				// top
				$cache = array_keys($top);
				
				// save this list
				$this->saveCache($cid,$cache,$ttl);
				
			}
			
		// cache = 0
		if ( count($cache) == 0 ) {
			$this->good(false);
		}
		
		// html
		$html = "<ul class='mini-users'>";
	
		// now get the top 10 and get their 
		// info
		foreach ( array_slice($cache,0,18) as $id ) {
		
			// get 
			$usr = $this->getTwitterUser($id);
			
				// error
				if ( !$usr ) { continue; }
					
			// mini
			$mini = $this->getMiniPic($usr['profile_image_url']);
		
			// echo 
			$html .= "<li><a href='/user/{$usr['screen_name']}'><img title='{$usr['name']} ({$usr['screen_name']})' class='defer bubble' src='".BLANK."' style='background-image: url($mini)'></a></li>";
		
		}
	
		// end
		$html .= "</ul>";
	
		// save 
		$this->saveCache(md5('suggest.friends.'.$this->uid),$html,(60*30));
		
		// print 
		$this->good(array('html'=>$html));
	
	}
	
	/* getTinyUrl */
	public function getTinyUrl() {
	
		// url
		$url = $this->param('url');
		
		// go
		$r = $this->curl("http://tinyurl.com/api-create.php?url=".$url);
	
		// good
		exit($r);
	
	}
	
	/* upload */
	public function upload() {
		
		// header
		header("Content-Type: text/html",true);

		// figure it out 
		// make sure it's  the right type of file 
		$f = $_FILES['file'];
	
		// need 
		if ( !$f ) {
			unlink($f['tmp_name']);
			exit("<html><head><script type='text/javascript'> parent.document.getElementById('update-status-photo-frm').style.visibility = 'visible'; parent.alert('You did not select a file'); </script></head></html>");
		}

		// need 
		if ( $f['type'] != 'image/jpeg' AND $f['type'] != 'image/gif' AND $f['type'] != 'image/png' ) {
			unlink($f['tmp_name']);		
			exit("<html><head><script type='text/javascript'> parent.alert('Not an Image File'); </script></head></html>");
		}
			
		// params
		$params = array("username"=>$this->user['u'],"password"=>$this->user['p'],"media"=>"@".$f['tmp_name']);
		
		// send it 
		$ch = curl_init("http://twitpic.com/api/upload");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$output = curl_exec($ch);      				
		curl_close($ch);
	
		// figure out 	
		$xml = simplexml_load_string($output);
		
		// unlink
		@unlink($f['tmp_name']);		
		
		if ( $xml['stat'] == 'ok' ) {
			$url = (string)$xml->mediaurl;
			exit("<html><head><script type='text/javascript'> parent.TT.Global.uploadPhotoDone('{$url}'); </script></head></html>");
		}
		else {
			exit("<html><head><script type='text/javascript'> parent.document.getElementById('update-status-photo-frm').style.visibility = 'visible'; parent.alert(".(string)$xml->err['msg']."); </script></head></html>");
		}			
	
	}	
	
	/* timelineUpdate */
	public function timelineUpdate() {		
		
		// need info
		$type = $this->param('type');
		$id = $this->param('id');
		$since = $this->param('max');
	
			// need them all
			if ( !$type OR !$id OR !$since ) {
				$this->bad("not enoguht data");
			}
	
		// raw 
		$raw = array();
		$max = false;
	
		// timeline
		$html = "<ul>";
		
			// switch
			switch($type) {
			
				// Groups
				case 'my-groups':
				
					$this->buildTimeline();
					
					// get them 
					list($h,$raw,$pager) = $this->getTimeline(array(
						'groups' => $id,
						'since_id' => $since
					));				
				
					// rw a
					foreach ( $raw as $i ) {
					
						// html 
						$html .= $this->_bit_displayStatus($i);
						
						// max 
						if ( $i['id'] > $max ) {
							$max = $i['id'];
						}
												
					}
				
				break;
				
				// Timeline
				case 'timeline':
				
					// get them 
					list($h,$raw,$pager) = $this->getTimeline(array(
						'since_id' => $since
					));	
				
					// rw a
					foreach ( $raw as $i ) {
					
						// html 
						$html .= $this->_bit_displayStatus($i);
						
						// max 
						if ( $i['id'] > $max ) {
							$max = $i['id'];
						}
												
					}				
										
				
				break;
				
				// Networks
				case 'my-networks':
								
					// run it 
					$r = $this->curl("http://search.twitter.com/search.json?since_id={$since}&q=".urlencode("#tt:".$id));		
				
					// get j
					$r = json_decode($r,true);
				
					// go for it 
					if ( is_array($r) AND count($r) > 0 ) {				
					
						// results
						foreach ( array_reverse($r['results']) as $item ) {
							$raw[$item['id']] = array(
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
							
							// add to html
							$html .= $this->_bit_displayStatus($item);	
							
							// max
							$max = $item['id'];					
													
						}						
						
					}
				
				break;
				
				// search
				case 'my-searches':
				
					$q = $this->savedSearches[$id]['q'];
					
					// run it 
					$r = $this->curl("http://search.twitter.com/search.json?since_id={$since}&q=".urlencode($q));		
				
					// get j
					$r = json_decode($r,true);
				
					// go for it 
					if ( is_array($r) AND count($r) > 0 ) {				
					
						// results
						foreach ( array_reverse($r['results']) as $item ) {
							$raw[$item['id']] = array(
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
							
							// add to html
							$html .= $this->_bit_displayStatus($raw[$item['id']]);	
							
							// max
							$max = $item['id'];					
													
						}						
						
					}
				
				
				break;
				
			}
			
		$html .= "</ul>";
					
		// g 
		$groups = array();
			
			// foreach
			foreach ( $this->getGroups() as $g ) {
			
				// get them 
				$n = $this->getTimeline(array(
					'countOnly' => true,
					'groups' => $g['id'],
					'since_id' => $since
				));	
				
				$groups[$g['id']] = $n;
			
			}
	
		// get bootstarp
		list($html,$bs) = $this->parseXhrData($html);
	
		// set last 
		setrawcookie("L",time(),time()+(60*60*24*365),'/',DOMAIN);		
	
		// we're all good
		$this->good(array('html'=>$html,'bootstrap'=>$bs,'raw'=>$raw,'max'=>$max,'groups'=>$groups));
	
	}
	
	/* get converstaion */
	public function getReply() {
		
		// get reply
		$id = $this->param('id');
		
		// get response	
		$r = $this->twitter('statuses/show/'.$id);
		
		// ?
		if ( $r ) {
		
			// parse me out
			$this->good( array( 'html' => $this->_bit_displayStatus($r), 'raw' => $r ) );
			
		}
		else {
			$this->bad("Could not get message");
		}
	
		
	}
	
	/* removeSavedSearch */
	public function removeSavedSearch() {
		
		// id
		$id = $this->param('id');
	
		// do it 
		unset($this->savedSearches[$id]);
		
		// save them 
		// update
		$this->query("UPDATE `users` SET `searches` = '??' WHERE `id` = '??' ",array(
			serialize($this->savedSearches),
			$this->uid
		));	
	
		// good
		$this->good(true);
	
	}
	
	/*fav */
	public function fav() {
			
		// id 
		$id = $this->param('id');
		
			// no id
			if ( !$id ) {
				exit("Invalid Request");
			}
		
		// params
		$p = "id=".$id;
		
		$u = array(
			'u' => $this->user['u'],
			'p' => $this->user['p']
		);
		
		// twitter
		$r = $this->curl("http://twitter.com/favorites/create/{$id}.json",$u,$p);
	
			// what ip 
			if ( !$r ) {
				exit("Problem contacting Twitter. Please try again");
			}
	
		// parse 
		$j = json_decode($r,true);
	
			// error
			if ( array_key_exists('error',$j) ) {
				exit($j['error']);
			}
		
		// all good
		exit('true');	
	
	}
	
	/* addUserToGroup */
	public function addUserToGroup() {
	
		// info
		$gid = $this->param('gid');
		$uid = $this->param('uid');
		
		// no
		if ( !$gid OR !$uid ) {
			$this->bad("Missing Params");
		}
	
		// query		
		$this->query(
			"INSERT INTO group_map SET group_id = '??', friend_id = '??' ",
			array($gid,$uid)
		);		
		
		// clear	
		$this->clearCache(md5("group.{$gid}"));
		$this->clearCache(md5("groups.".$this->uid));		
		
		// what
		$this->good(true);
	
	}
	
	/* friendSearch */
	public function friendSearch() {
	
		// expires
		$expires = 60;
		
		header( 'Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . 'GMT');
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', time()-$expires ) . ' GMT' );
		header( "Cache-Control: max-age={$expires}, must-revalidate" );
		header( "Pragma:");		
		
		// query
		$q = $this->param('query');
	
		// results
		$r = array();
		
		// saerch
		foreach ( $this->allFriends as $f ) {
			if ( preg_match("/^".preg_quote($q)."/i",$f['name']) ) {
				$f['img'] = $this->getMiniPic($f['img']);
				$r[] = $f;
			}
		}
	
		// json
		$this->good($r);
	
	}
	
	/* updateStatus */
	public function updateStatus() {
		
		// get some stuff
		$status = $this->param('status');
		$reply = $this->param('reply');
		$network = $this->param('network');
				
		if ( !$status ){
			$this->bad("No Status!");
		}
			
		// params
		$p = "source=twittangle&status=".urlencode(stripslashes($status));
		
			// if reply
			if ( $reply ) {
				$p .= "&in_reply_to_status_id=".$reply;
			}
		
		$u = array(
			'u' => $this->user['u'],
			'p' => $this->user['p']
		);
		
		// twitter
		$r = $this->curl("http://twitter.com/statuses/update.json",$u,$p);
	
			// what ip 
			if ( !$r ) {
				$this->bad("Problem contacting Twitter. Please try again");
			}
	
		// parse 
		$j = json_decode($r,true);
	
			// error
			if ( array_key_exists('error',$j) ) {
				exit($j['error']);
			}
		
		// all good
		$this->good( array('full' => $this->_bit_displayStatus($j), 'parsed' => $this->parseStatus($j['text']), 'raw' => $j ) );	
	
	}
	
	/* saveSearch*/
	public function saveSearch() {
	
		// q
		$q = $this->param('q');
		
		// need q 
		if ( !$q ) {
			$this->bad('No Query');
		}
	
		// what are their current updates
		$id = max( array_keys($this->savedSearches) );
	
			// no id make it one
			if ( !$id ) {
				$id = 0;
			}
	
			
		// run it for the first time and get a coup
		$r = json_decode($this->curl("http://search.twitter.com/search.json?rpp=100&q=".urlencode($q)),true);	
	
		// save it 
		$this->savedSearches[++$id] = array(
			'q' => $q,
			'since' => $r['max_id'],
			'num' => count($r['results'])
		);
	
		// update
		$this->query("UPDATE `users` SET `searches` = '??' WHERE `id` = '??' ",array(
			serialize($this->savedSearches),
			$this->uid
		));
		
		// add good
		$this->good($this->savedSearches);
	
	}
	
	/* submit user panel */
	public function submitUserPanel() {
	
		// id
		$id = $this->param('id');
		
		// params
		$rate = $this->param('rate');
		$tags = $this->param('tags');
		$group = $this->param('group');
	
		// insert
		if ( is_array($group) ) {
			foreach ( $group as $k => $i ) {			
			
				if ( $i == 'true' ) {
					$this->query(
						"INSERT INTO group_map SET group_id = '??', friend_id = '??' ",
						array($k,$id)
					);
				}
				else {
					$this->query(
						"DELETE FROM group_map WHERE group_id = '??' AND friend_id = '??' LIMIT 1 ",
						array($k,$id)
					);
				}
				
				$this->clearCache(md5('group.'.$k));
			}	
		}

		// just do it 
		$sql = "
			INSERT INTO
				friends
			SET
				`user` = '??',
				`friend` = '??',
				`rating` = '??',
				`tags` = '??'
			ON DUPLICATE KEY UPDATE
				`rating` = '??',
				`tags` = '??'
		";
		
		// go 
		$this->query($sql,array(
				$this->uid,
				$id,
				$rate,
				$tags,
				$rate,
				$tags
			));
			
		// clear cache 
		$this->clearCache('friends.'.$this->uid);				
			
		// good
		$this->good(true);	
	
	}

	/* userPanel */
	public function userPanel() {
		
		// id
		$id = $this->param('id');
		
		// get some stuff
		$groups = $this->getGroups();
		$friends = $this->getFriends();		
		
		// what up 
		if ( $this->param("_name") == 'userpanel' )  {
		
			// check for cahce
			$cid = md5("userpanel.{$id}.{$this->uid}");
			$ttl = (60*10);
			
				// yes
				if ( $cache = $this->getCache($cid,$ttl) ) {
					$html = $cache;
				}
				else {
					
					// get some info about this user
					$user = $this->twitter("users/show/{$id}",false,true);
					
					// not a million
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
				
			// make it
			$html = "
				<div class='yui-gd userpanel-overlay'>
					<div class='yui-u first'>
						<div class='box'>
							
							<img src='{$user['profile_image_url']}' width='48' height='48'>
							<h3>
								{$user['name']}
								<em>{$user['screen_name']}</em>
								
							</h3>
										
							<ul class='user-stats cf'>
								<li>
									<em>".number_format($user['friends_count'])."</em>
									following
								</li>
								<li>
									<em>".number_format($user['followers_count'])."</em>
									followers
								</li>
								<li>
									<em>".number_format($user['statuses_count'])."</em>
									updates
								</li>								
							</ul>								
							
			";
			
					// some suser info
					$html .= "<ul class='user-info'>";
					
					$html .= "<li><em>Full Profile:</em> <a href='/user/{$user['screen_name']}'>view</a></li>";
						
						// go for it 
						foreach ( $user as $k => $v ) {
							switch($k) {
								case 'description': $html .= "<li><em>Description:</em> {$v}</li>"; break;
								case 'location': $html .= "<li><em>Location:</em> {$v}</li>"; break;
								case 'url':
									$url = str_replace(array("http://www.","http://"),"",$v);
									if ( strlen($v) > 20 ) {
										$url = substr($v,0,20).'...';
									}
									$html .= "<li><em>URL:</em> <a href='{$v}'>{$url}</a></li>";
									break;
							};
						}
					
					$html .= "
						</ul>						
					";				
			
					if ( $this->loged AND isset($friends) AND count($friends) > 0 ) {
					
						$html .= "<h4>Friends In Common</h4>";
						$html .= "<ul class='mini-users'>";
					
						// i
						$i = 0;
					
						foreach ( $friends as $usr ) {
						
							if ( $i++ > 90 ) { break; }
						
							// mini
							$mini = $this->getMiniPic($usr['img']);
						
							// echo 
							$html .= "<li><a href='/user/{$usr['sn']}'><img title='{$usr['name']} ({$usr['sn']})' class='bubble' src='".BLANK."' style='background-image: url($mini)'></a></li>";

						}		
						
						$html .= "</ul>";		
					
					}
					
				if ( array_key_exists($id,$friends) ) {
					$rate = $friends[$id]['rating'];
				}
				else {
					$rate = 0;
				}
			
			$html .= "						
							<b class='tl'></b>
							<b class='tr'></b>
							<b class='bl'></b>
							<b class='br'></b>
						</div>
					</div>
					<div class='yui-u'>
						<form id='user-panel-form'>
						<h4>Rating</h4>
						<div class='rate-header'>
							<b>Just Friends</b>
							<em>I love them</em>
						</div>
						<div id='slider-bg-new' class='yui-h-slider' tabindex='-1' >
						    <div id='slider-thumb-new' class='yui-slider-thumb'><img src='http://yui.yahooapis.com/2.6.0/build/slider/assets/thumb-n.gif'></div>
						</div>
						<input type='text' name='rate' id='user-panel-rate-new' value='{$rate}'>					
						
						<br><br>
						<h4>Groups</h4>
						<ul class='groups cf'>
			";
			
				foreach ( $groups as $g ) {
					
					$gu = $this->getGroupUsers($g['id']);
				
					// are they in this group
					$html .= "<li><input class='group' type='checkbox' ".(array_key_exists($id,$gu)?'checked':'')." name='group[{$g['id']}]' value='1'> {$g['name']} </li>";
					
				}
				
				// tags
				if ( array_key_exists($id,$friends) ) {
					$tags = implode(', ',$friends[$id]['tags']);
				}
				else {
					$tags = "";
				}				
			
			$html .= "			
						</ul>			
						
						<br><br>
						<h4>Tags</h4>						
						<textarea>{$tags}</textarea>
						<div class='small gray'>Seperate tags with a comma</div>
						
						<button type='button' id='urs|{$id}' class='user-panel-submit'>Update</button>		
						</form>
					</div>
				</div>			
			";
					
		}
		else {		
		
			
			// rate
			$rate = ( array_key_exists($id,$friends) ? $friends[$id]['rating'] : 0 );
			$tags = ( array_key_exists($id,$friends) ? implode(", ",$friends[$id]['tags']) : "" );
				
			// html;
			$html = "		
				<h3></h3>					
				<form id='user-panel-form'>
				<button type='button' id='urs|{$id}' class='user-panel-submit'>Update</button>
				<div class='yui-gb'>
					<div class='yui-u first'>
						<h4>Rating</h4>
						<div class='rate-header'>
							<b>Just Friends</b>
							<em>I love them</em>
						</div>
						<div id='slider-bg' class='yui-h-slider' tabindex='-1' >
						    <div id='slider-thumb' class='yui-slider-thumb'><img src='http://yui.yahooapis.com/2.6.0/build/slider/assets/thumb-n.gif'></div>
						</div>
						<input type='text' name='rate' id='user-panel-rate' value='{$rate}'>					
					</div>
					<div class='yui-u'>
						<h4>Tags</h4>
						<textarea name='tags'>{$tags}</textarea>
					</div>
					<div class='yui-u'>
						<h4>Groups</h4>
						<ul>
			";
			
				foreach ( $groups as $g ) {
					
					$gu = $this->getGroupUsers($g['id']);
				
					// are they in this group
					$html .= "<li><input class='group' type='checkbox' ".(array_key_exists($id,$gu)?'checked':'')." name='group[{$g['id']}]' value='1'> {$g['name']} </li>";
					
				}
			
			$html .= "			
						</ul>
					</div>
				</div>
			
			";
			
		}
	
		// return 
		$this->good(array('html'=>$html));
	
	}

	/* searchCountUpdate */
	public function searchCountUpdate() {
		
		// get each search
		$query = json_decode( stripslashes($this->param('q')), true );
		
		// results
		$results = array();
		$total = 0;
	
		// do it 
		foreach ( $query as $id => $q ) {
			
			// since
			$since = "";
		
			// since
			if ( array_key_exists('since',$q) ) {
				$since = "since_id={$q['since']}";
			}
		
			// make the query
			$r = json_decode($this->curl("http://search.twitter.com/search.json?{$since}&rpp=100&q=".urlencode($q['q'])),true);
			
			// get r 
			if ( $r AND array_key_exists('results',$r) ) {
			
				// n
				$n = count($r['results']);
				
				// results
				$results[$id] = array( 'num' => $n, 'since' => $r['max_id'] );
				
				// total
				$total += $n;
				
			}
		
		}
		
		// show 
		$this->good(array($results,$total));
	
	}
	
	
	/*  video */
	public function embed() {
	
		// get url 
		$url = urldecode($this->param('url'));
	
			// no url
			if ( !$url ) {
				exit("<span class='loading'>Could Not Load Video</span>");
			}
	
		// what is it
		if ( strpos($url,'qik.com') !== false ) {
			
			// get it 
			$page = $this->curl($url);
		
				// no page
				if ( !$page ) {
					exit("<span class='loading'>Could Not Load Video</span>");
				}
		
			// get the link tag
			$f = preg_match('/<link rel="video_src" href="(.*)" \/>/i',$page,$m);
			
				// found 
				if ( !$f ) {
					exit("<span class='loading'>Could Not Load Video</span>");
				}				
			
			// emebed
			$embed = str_replace("http://qik.com/swfs/qikPlayer4.swf?","",$m[1]);
			
			// return 
			exit('<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="425" height="319" id="qikPlayer" align="middle"><param name="allowScriptAccess" value="sameDomain" /><param name="allowFullScreen" value="true" /><param name="movie" value="http://qik.com/swfs/qikPlayer4.swf" /><param name="quality" value="high" /><param name="bgcolor" value="#333333" /><param name="FlashVars" value="'.$embed.'"><embed src="http://qik.com/swfs/qikPlayer4.swf" quality="high" bgcolor="#333333" width="425" height="319" name="qikPlayer" align="middle" allowScriptAccess="sameDomain" allowFullScreen="true" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" FlashVars="'.$embed.'"/></object>');
			
		
		}
	
	}
	
	/* links */
	public function links() {
		
		// links
		$links = json_decode(stripslashes($this->param('links')),true);
		
	
			// no links
			if ( !$links ) {
				$this->bad("No Links Given");
			}
		
		// fianl
		$final = array();
	
		// foreach link
		foreach ($links as $info) {
			
			// link and name
			$lnk = $info['url'];
			$name = $info['name'];
			
			// final
			$f = $s = $this->getFinalUrl($lnk);
			
			// not false
			if ( $f ) {
				
				// small
				if ( strlen($s) > 30 ) {
					$s = substr($s,0,30).'...';
				}
				
				// loop it up
				if ( $this->beta AND ( strpos($f,'twitpic.com') !== false OR strpos($f,'flickr.com') !== false ) ) {
					// twitpic
	//				$final[$name] = array('type'=>'pic','url'=>$f,'short'=>$s);
				}
				else if ( strpos($f,'youtube.com') !== false ) {
	//				$final[$name] = array('type'=>'video','url'=>$f,'short'=>$s);
				}
				else {
					$final[$name] = array('type'=>'expand', 'url'=>$f,'short'=>$s);
				}
				
			}
		
		}
		
		// give back
		$this->good($final);
	
	}	
	
	
	


	/* bad */	
	public function bad($msg) {
		exit( json_encode( array('stat'=>0,'msg'=>$msg)) );
	}
	
	/* good */
	public function good($resp) {

		if (!$this->alpha) {
	
/*
			// 60 maxage
			$expires = (5*$this->param('ttl',1));
		
			header( 'Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
			header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', time()-$expires ) . ' GMT' );
			header( "Cache-Control: max-age={$expires}, must-revalidate" );
			header( "Pragma:");	
*/
			
		}
			
		// chec for html
		if ( is_array($resp) AND array_key_exists('html',$resp) ) {
			$resp['html'] = preg_replace("/(\t|\s)+/"," ",$resp['html']);
		}
	
		// exixt
		exit( json_encode(array('stat'=>1,'resp'=>$resp)) );
	}
	

}

?>