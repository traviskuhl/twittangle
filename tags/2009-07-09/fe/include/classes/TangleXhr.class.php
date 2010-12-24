<?php

	class TangleXhr extends Tangle {
	
		// whitelist calls that don't need req
		
		/* construct */
		public function __construct() {
		
			$this->whitelist = array('xhr_embed','xhr_links');
			
			/* construct */
			parent::__construct();
		
			// get an act
			$act = 'xhr_'.$this->param('act',false,false,'alpha');
		
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

			if ( !in_array($act,$this->whitelist) AND  !$this->loged ) {
				$this->bad("Invalid Request Token");
			}	
		
			// do it 
			call_user_func(array($this,$act));
		
		}
	
		public function bad($msg) {
			exit( json_encode( array('stat'=>0,'msg'=>$msg)) );
		}
		
		public function good($resp) {
		
			// chec for html
			if ( is_array($resp) AND array_key_exists('html',$resp) ) {
				$resp['html'] = preg_replace("/\t|\s+/"," ",$resp['html']);
			}
		
			// exixt
			exit( json_encode(array('stat'=>1,'resp'=>$resp)) );
		}
		
		// update
		public function xhr_update() {
			
			//status
			$status = $this->param('status');
		
			// need it
			if (!$status) {
				$this->bad('Missing Status');
			}
			
			// satsu
			$status = urlencode(stripslashes($status));
		
			// post me up
			$p = "source=twittangle&status=".$status;
			
			$u = array(
				'u' => $this->user['u'],
				'p' => $this->user['p']
			);
			
			// twitter
			$r = $this->curl("http://twitter.com/statuses/update.json",$u,$p);			
		
			// what up
			if ( !$r ) {
				$this->bad("Error posting update!");
			}
			
			$j = json_decode($r,true);
			
			// error
			if ( array_key_exists('error',$j) ) {
				$this->bad($j['error']);
			}
			
			// clear
			$this->clearCache(md5('user.last.status.'.$this->uid));
		
			// html
			$r = array('twitter'=>$j,'html'=>$this->_bit_displayStatus($j));
			
			// json
			$this->good( $r );
		
		}
		
		// video
		public function xhr_embed() {
		
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
		
		// links
		public function xhr_links() {
			
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
		
		// batch
		public function xhr_batch() {
		
			// info
			$id = $this->param('id');
			$friends = $this->param('friends');
			
				// have everything
				if ( !$id OR !$friends ) {
					exit("Invalid Request");
				}
				
			// get my groups 
			$groups = $this->getGroups();
			
				// not in this group
				if ( !array_key_exists($id,$groups) ) {
					exit("Invalid Request: Not Your Group!");
				}
				
				
			// encode
			$j = json_decode( stripslashes($friends),true);
			
				// not json
				if ( !$j ) {
					exit("Invalid Request: Could not parse your list.");
				}
				
			// get current
			$current = $this->getGroupUsers($id);
		
			// acted
			$acted = array();
		
			// now loop through
			foreach ( $j as $user => $f ) {
			
				// do we need to add them
				if ( !$f[0] AND json_encode( $f[1]) ) {
					// insert them
					$this->query(
						"INSERT INTO group_map SET group_id = '??', friend_id = '??', `user` = '??' ",
						array($id,$user, json_encode($f[1]))
					);
				}
				
				// acted 
				$acted[] = $user;
			}
	
			// foreach one
			foreach ( $current as $uid => $user ) {
				if ( !in_array($uid,$acted) ) {
					$this->query(
						"DELETE FROM group_map WHERE group_id = '??' AND friend_id = '??' LIMIT 1 ",
						array($id,$uid)
					);				
				}
			}
		
			// clear cache 
			$this->clearCache(md5('group.'.$id));
			$this->clearCache(md5('groups.'.$this->uid));
			
			// good
			exit('true');
		
		}
		
		// fav
		public function xhr_fav() {
		
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
		
		// reply
		public function xhr_reply() {
			
			// msg 
			$msg = $this->param('msg');
			$to = $this->param('to');
		
				// not to
				if ( !$msg OR !$to ) {
					exit("There was an error. Try again!");
				}
				
			// params
			$p = "source=twittangle&status=".urlencode(stripslashes($msg))."&in_reply_to_status_id=".$to;
			
			$u = array(
				'u' => $this->user['u'],
				'p' => $this->user['p']
			);
			
			// twitter
			$r = $this->curl("http://twitter.com/statuses/update.json",$u,$p);
		
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
		
		
		// update
		function xhr_updategroup() {		
		
		
			// friend stuff
			$friend = $this->param('friend');
			$g = $this->param('g');
			$u = $this->param('u');
		
			// no 
			if ( !$friend AND !$g ) {
				$this->bad("Invalid properties");
			}			
			
			// parse 
			$group = json_decode( stripslashes($g),true);		
			
			// insert
			foreach ( $group as $i ) {			
			
				if ( $i[1] ) {
					$this->query(
						"INSERT INTO group_map SET group_id = '??', friend_id = '??', `user` = '??' ",
						array($i[0],$friend, str_replace("'","",$u))
					);
				}
				else {
					$this->query(
						"DELETE FROM group_map WHERE group_id = '??' AND friend_id = '??' LIMIT 1 ",
						array($i[0],$friend)
					);
				}
				
				$this->clearCache(md5('group.'.$i[0]));
			}
			
			// clean groups
			$this->clearCache(md5('groups.'.$this->uid));
			
			// good
			$this->good(true);
		
		}
		
		function xhr_group() {
			
			// friends
			$friend = $this->param('friend');
		
				// no firend
				if ( !$friend ) {
					$this->bad("No Freind!");
				}
				
			// get groups
			$groups = $this->getGroups();
			
			// figure out what groups they're in 
			$in = array();
			
				// all 
				foreach ( $groups as $gid => $i ) {
					
					// get group users
					$users = $this->getGroupUsers($gid);
					
					// find 
					if ( array_key_exists($friend,$users) ) {
						$in[] = $gid;
					}
					
				}
			
			// give back
			$this->good($in);
			
		}
		
		function xhr_timeline() {
		
			$since = $this->param('since','last');
			$page = $this->param('page',1);		
			$tag = $this->param('tag');
			$pager = $this->param('pager',true);
			$group = $this->param('group');		
			
			// get them 
			list($h,$r) = $this->getTweets($since,$page,$tag,20,$pager,$group);
		
			// return on the right number
			$this->good(array('html'=>$h,'raw'=>$r));		
		
		}
	
		/* rate */
		function xhr_rate() {
			
			// id 
			$id = $this->param('id');
			$n = (int)$this->param('n',false,$_POST);
		
			// just do it 
			$sql = "
				INSERT INTO
					friends
				SET
					`user` = '??',
					`friend` = '??',
					`rating` = '??'
				ON DUPLICATE KEY UPDATE
					`rating` = '??'
			";
			
			// go 
			$this->query($sql,array(
					$this->uid,
					$id,
					$n,
					$n
				));
				
			// clear cache 
			$this->clearCache('friends.'.$this->uid);				
				
			// good
			$this->good(true);	
		
		}
		
		/* tag */
		function xhr_tag() {
		
			// id 
			$id = $this->param('id');
			$n = preg_replace("/[^a-zA-Z0-9, ]+/","",$this->param('tags',false,$_POST));
		
			// just do it 
			$sql = "
				INSERT INTO
					friends
				SET
					`user` = '??',
					`friend` = '??',
					`tags` = '??'
				ON DUPLICATE KEY UPDATE
					`tags` = '??'
			";
			
			// go 
			$this->query($sql,array(
					$this->uid,
					$id,
					$n,
					$n
				));
				
			// clear cache 
			$this->clearCache('friends.'.$this->uid);				
				
			// good
			$this->good(true);		
		
		}
	
	
	} // END

?>