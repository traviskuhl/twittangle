<?php

class networks extends Tangle {

	public function __construct() {
	
		// arent
		parent::__construct();	
		
		// path
		$this->path = explode('/',trim($this->param('path'),'/'));
	
		// set act
		if ( method_exists($this,@$this->path[0]) ) {
			$_REQUEST['act'] = $this->path[0];
		}
	
		$this->myNetworks = $this->getUserNetworks();
	
		// body class
		$this->bodyClass = 'networks';
	
	}
	
	/* main */
	public function main() {

		// title
		$this->title = "Networks".$name;
		$this->bodyClass = 'networks';
		
		// id 
		$id = $this->pathParam(0,'0');
		
		$info = $this->row("SELECT * FROM network_cats WHERE id = '??' ",array($id));
		
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
				
					$row['networks'] = array();
				
					// get 2 nets in category
					$nets = $this->query("SELECT * FROM networks WHERE FIND_IN_SET('{$row['id']}',cats) ORDER BY timestp DESC LIMIT 2");
					
						while ( $r = $nets->fetch_assoc() ) {
							$row['networks'][] = $r;
						}
					
					// categories
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
		
	
		// networks
		include( $this->tmpl("networks/index") );
		
	}
	
	
	/* network */
	public function network() {
	
		// slug 
		$slug = $this->pathParam(1);
	
		// get info about the network
		$info = $this->row("SELECT * FROM networks WHERE slug = '??' ",array($slug));
		
			// bad
			if ( !$info ) {
				$this->go("/404");
			}	
	
			
		//
		// JOIN
		//
		if ( $this->pathParam(2) == 'join' ) {
		
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
				$this->go($this->url('network',$info));

			}
			else {
				$this->go( $this->url('login') );
			}
		
		}				
			
		// one page
		$page = $this->param('page',1);
			
		// get
		list($timeline,$next,$prev) = $this->getNetworkUpdates($info['id'],$page,true);
	
		// tmp
		include( $this->tmpl('networks/network') );
		
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