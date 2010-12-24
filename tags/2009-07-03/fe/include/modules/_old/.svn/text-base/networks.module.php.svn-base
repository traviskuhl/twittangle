<?php

// networks
class networks extends Tangle {

	// networks
	public $myNetworks = array();

	/* constuct */
	public function __construct() {
	
		// path
		$this->path = explode('/',trim($this->param('path'),'/'));
	
		// parent
		parent::__construct();

		// loged
		if ( $this->loged ) {
		
			// cache
			$cid = md5("networks.user.{$this->uid}");
			$ttl = (60*60*24);
			
			// check
			$this->myNetworks = $this->getCache($cid,$ttl);
			
				// n
				if ( !$this->myNetworks ) {
					
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
					$sth = $this->query($sql,array($this->uid));
					
					// array
					$this->myNetworks = array();
					
						while ( $row = $sth->fetch_assoc() ) {
							$this->myNetworks[$row['id']] = $row;
						}
					
					// save
					$this->saveCache($cid,$this->myNetworks,$ttl);
					
				}
						
		}	
	
	}
	
	/* main */
	public function main() {
	
		// slug
		$slug = $this->pathParam(0); 
	
			
		// if there's a network
		if ( $slug AND $slug == 'update' ) {
			$this->update();
		}
		else if ( $slug AND $slug == 'create' ) {
			$this->create();
		}
		else if ( $slug AND $slug != 'category' ) {
			$this->network($slug);
		}	
		else {
						
			// id 
			$id = $this->pathParam(1,'0');
			
			// check for cache
			$cid = md5("network.cats.{$id}");
			$ttl = (60*60);
			
			// cache
			$categories = $this->getCache($cid,$ttl);
			
				// nope
				if ( !$categories ) {
					
					// get them
					$sth = $this->query("SELECT * FROM `network_cats` WHERE `parent_id` = '??' AND id != 0 ORDER BY `name` ",array($id));
					
					// cat
					$categories = array();
					
					while ( $row = $sth->fetch_assoc() ) {
						$categories[$row['id']] = $row;
					}
				 
				 	// save
				 	$this->saveCache($cid,$categories,$ttl);
				 
				}
			
			
			// get networks
			$cid = md5("networks.{$id}");
			$ttl = (60*60);
			
			// get
			$networks = $this->getCache($cid,$ttl);
			
				// if 
				if ( !$networks ) {
					
					$sql = "
						SELECT 
							n.*,
							u.user
						FROM 
							networks as n
						LEFT JOIN users as u ON ( u.id = n.admin_id )
						WHERE 
							FIND_IN_SET('??',n.cats) 
						ORDER BY `timestp` DESC
					";
					
					// get
					$sth = $this->query($sql,array($id));
					
					// 
					$networs = array();
					
					// wh
					while ( $row = $sth->fetch_assoc() ) {
						$networks[$row['id']] = $row;
					}
					
					// save
					$this->saveCache($cid,$networks,$ttl);
					
				}
			
			
			// name
			$name = "";
			
			// no zero
			if ( $id != 0 ) {
			
				$row = $this->row("SELECT name FROM `network_cats` WHERE `id` = '??' ", array($id));
			
				// name
				$name = " > " . $row['name'];
				
			}
			
			// title
			$this->title = "Networks".$name;
			$this->bodyClass = 'networks';
			
			include( $this->tmpl('networks/index') );
			
		}
	
	}
	
	/* network */
	public function network($slug) {
		
		// get info about the network
		$info = $this->row("SELECT * FROM networks WHERE slug = '??' ",array($slug));
		
			// bad
			if ( !$info ) {
				$this->go("/404");
			}
			
		// title
		$this->title = "{$info['title']} Network ";
	
		//
		// JOIN
		//
		if ( $this->pathParam(1) == 'join' ) {
		
			// uid
			if ( $this->loged ) {
		
				// join em
				$this->query(
					"INSERT INTO `network_map` SET `network_id` = '??', `user_id` = '??', `timestmp` = '??' ",
					array($info['id'],$this->uid,time()) 
				);
				
				// clear 
				$this->clearCache(md5("networks.user.{$this->uid}"));
				$this->clearCache(md5("network.members.hp.{$info['id']}"));
				
				// go 
				$this->go("http://networks.twittangle.com/{$info['slug']}");

			}
			else {
				$this->go( $this->url('login') );
			}
		
		}	
	
		// page
		$page = $this->param('page',1);
	
		// get latest updates
		$cid = md5("network.updates.{$info['id']}.{$page}");
		$ttl = 30;
	
		// get 
		$updates = $this->getCache($cid,$ttl);
		
			// nope
			if ( !$updates ) {
				
				// go for it on search
				$resp = $this->curl("http://search.twitter.com/search.json?page={$page}&rpp=20&q=".urlencode("#tt:".$info['id']));
			
				// bad
				if ( !$resp ) {
					$this->go("/500");	
				}
				
				// set
				$updates = json_decode($resp,true);
			
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
		
		// members
		$cid = md5("network.members.hp.{$info['id']}");
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
				$sth = $this->query($sql,array($info['id']));
			
				// put 
				$members = array();
				
				// what 
				while ( $row = $sth->fetch_assoc()) {
					$members[] = $row;
				}
			
				// save
				$this->saveCache($cid,$members,$ttl);
			
			}
		
		// tmpl
		include( $this->tmpl("networks/network"));	
	
	}
	
	/* update */
	public function update() {
	
		//status
		$status = $this->param('status');
		$tag = $this->param('tag');
		$token = $this->param('token');
	
		// need it
		if (!$status OR !$tag OR !$token) {
			$this->go("http://networks.twittangle.com/{$this->path[1]}?status=fail");
		}
	
		// token
		if ( $token != $this->md5($tag.$this->uid) ) {
			$this->go("http://networks.twittangle.com/{$this->path[1]}?status=fail");
		}
		
		// post me up
		$p = "status=".urlencode(stripslashes($status))." ".$tag;
		
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
		
		// get an id 
		$id = trim(explode(":",$tag));
		
		// clear
		$this->clearCache(md5('user.last.status.'.$this->uid));
		$this->clearCache(md5("network.updates.{$id[1]}.1"));

		// send back
		$this->go("http://networks.twittangle.com/{$this->path[1]}");
	
	}

	/* create */
	public function create() {
	
		// login
		$this->validate();
	
		// do it 
		if ( $this->param('do') == 'submit' ) {
		
			// stuff
			$title = strip_tags($this->param('title'));
			$info = strip_tags($this->param('info'));
			$slug = preg_replace("/[^a-zA-Z0-9]/","",$this->param('slug'));
			$cats = (array)$this->param('cats');
		
			// need it all
			if ( $title AND $info AND $slug AND $cats ) {
			
				// check slug
				$row = $this->row("SELECT * FROM `networks` WHERE slug = '??' ",array($slug));
				
				// row
				if ( !$row ) {
					
					// go for it 
					$fcats = array();
					
						foreach ( array_slice($cats,0,5) as $c ) {
							if ( $c ) {
								$fcats[] = $c;
							}
						}
										
					// add
					$sql = "
						INSERT INTO
							networks
						SET 
							title = '??',
							slug = '??',
							info = '??',
							admin_id = '??',
							timestp = UNIX_TIMESTAMP(),
							cats = '??'
					";
				
					// add them
					$r = $this->query($sql,array(
							$title,
							$slug,
							$info,
							$this->uid,
							implode(',',$fcats)
						));
				
					// what up 
					if ( $r ) {
					
						$id = $this->dbh->insert_id;
					
						// add them
						$this->query("INSERT INTO `network_map` SET `network_id` = '??', `user_id` = '??', timestmp = '??' ",array($id,$this->uid,time()));
					
						foreach ( $fcats as $c ) {
							$this->clearCache("networks.{$c}");
						}
					
						// go 
						$this->go("http://networks.twittangle.com/{$slug}");
					
					}
					else {
						$error = "Something bad happened. Try again!";
					}
				
				}
				else {
					$error = "The Network URL {$slug} is already taken!";
				}
			 
			}
			else {
				$error = "We need everything!";
			}
		
		}
		
		// title
		$this->title = "Create A Network";
		$this->bodyClass = 'full networks';

		// page
		include( $this->tmpl('networks/create') );
	
	}
	
}

?>