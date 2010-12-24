<?php

class networks extends Tangle {

	public function __construct() {
	
		// arent
		parent::__construct();	
		
		// path
		$this->path = explode('/',trim($this->param('path'),'/'));
	
		// get user networks
		$this->myNetworks = $this->getUserNetworks();
	
		// body class
		$this->bodyClass = 'networks';
	
	}
	
	/* main */
	public function main() {

		// get the path 
		if ( $this->pathParam(0) == 'category' ) {
			$this->category();
		}
		else if ( $this->pathParam(0) == 'create' ) {
			$this->create();
		}
		else if ( $this->pathParam(0) != "" ) {
			$this->network();
		}
		else {
			$this->index();
		}	
	
	}
	
	
	public function index() {		
		
		// get network categories 
		$categories = $this->queryAndSave("SELECT * FROM network_cats ORDER BY `name`",(60*60*24));
	
		// popular 
		$sql = "
			SELECT 
				COUNT( network_id ) AS members, 
				(SELECT COUNT(id) FROM network_msg WHERE network = n.id ) as posts,
				n.*
			FROM 
				network_map AS m, networks AS n
			WHERE 
				m.network_id = n.id
			GROUP BY 
				m.network_id
			ORDER BY 
				members DESC
			LIMIT 5
		";
		
		// popular
		$popular = $this->queryAndSave($sql,(60*60));
	
		// popular 
		$sql = "
			SELECT 
				n.*
			FROM 
				networks AS n
			ORDER BY 
				n.timestp DESC
			LIMIT 5
		";
			
		// newest
		$newest = $this->queryAndSave($sql,(60*60));	
		
		// popular 
		$sql = "
			SELECT 
				COUNT( network_id ) AS members, 
				(SELECT COUNT(id) FROM network_msg WHERE network = n.id ) as posts,
				n.*
			FROM 
				network_map AS m, networks AS n
			WHERE 
				m.network_id = n.id AND 
				FIND_IN_SET('0',n.cats)
			GROUP BY 
				m.network_id
			ORDER BY 
				n.timestp DESC
			LIMIT 5
		";
		
		// popular
		$featured = $this->queryAndSave($sql,(60*60));		
		
		// recent posts		
		$sql = "
			SELECT 
				m.*,
				n.title as network_title,
				n.id as network_id,
				n.slug as network_slug,
				u.id as user_id,
				u.user as user,
				u.pic as user_pic			
			FROM 
				network_msg as m,
				networks AS n,
				users AS u
			WHERE 
				m.network = n.id AND 
				m.user = u.id
			ORDER BY 
				m.timestamp DESC
			LIMIT 20
		";
		
		// popular
		$recent = $this->queryAndSave($sql,(60*3));		
	
		$this->title = "Networks";
	
		// networks
		include( $this->tmpl("networks/index") );	
	
	}
	
	public function category() {

		// id 
		$id = $this->pathParam(1,'0');
		
		// all 
		if ( $id != 'all' ) {
			
			// get network
			$sth = $this->queryAndSave(array("SELECT * FROM network_cats WHERE id = '??' ",array($id)),0);
			
				// no category
				if ( !$sth ) {
					$this->go("/404");
				}		
			
			$info = $sth[0];
				
			// sql
			$sql = "
				SELECT 
					n.*,
					u.user,
					(SELECT COUNT(network_id) FROM network_map WHERE network_id = n.id ) as members,				
					(SELECT COUNT(id) FROM network_msg WHERE network = n.id ) as posts
				FROM 
					networks as n
				LEFT JOIN users as u ON ( u.id = n.admin_id )
				WHERE 
					FIND_IN_SET('??',n.cats) 
				ORDER BY `timestp` DESC
			";					
				
			// do it 
			$networks = $this->queryAndSave(array($sql,array($id)),0);			
			
		}
		else {
		
			// page
			$page = $this->param('page',1);
			
			// start
			$start = ($page-1)*30;
		
			// sql
			$sql = "
				SELECT 
					n.*,
					u.user,
					(SELECT COUNT(network_id) FROM network_map WHERE network_id = n.id ) as members,				
					(SELECT COUNT(id) FROM network_msg WHERE network = n.id ) as posts
				FROM 
					networks as n
				LEFT JOIN users as u ON ( u.id = n.admin_id )
				ORDER BY n.title
				LIMIT {$start},30
			";					
				
			// do it 
			$networks = $this->queryAndSave($sql,(60*30));			
		
			// all
			$info = array(
				'name' => 'All'
			);
			
		}

		// get network categories 
		$categories = $this->queryAndSave("SELECT * FROM network_cats ORDER BY `name`",0);	
	
		// title
		$this->title = " <a href='".$this->url('networks')."'>Networks</a> / {$info['name']}";
	
		// networks
		include( $this->tmpl("networks/index") );	
	
	}
		
	
	/* network */
	public function network() {
	
		// slug 
		$slug = $this->pathParam(0);
	
		// get info about the network
		$sth = $this->queryAndSave(array("SELECT * FROM networks WHERE slug = '??' ",array($slug)),0);	
			
			// bad
			if ( !$sth ) {
				$this->go("/404");
			}		
	
		// info
		$info = $sth[0];	
			
		$this->title = "Network / " . $info['title'];	
			
		// get members	
		$sql = "
			SELECT 
				u.id,
				u.id as user_id,
				u.user,
				u.pic
			FROM
				network_map as m,
				users as u
			WHERE
				m.network_id = '{$info['id']}' AND 
				u.id = m.user_id
			ORDER BY 
				m.timestmp			
		";
			
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
				
				// clear save cache
				$this->mem->delete(md5($sql));
				
				// go 
				$this->go($this->url('network',$info));

			}
			else {
				$this->go( $this->url('login') );
			}
		
		}		
		else if ( $this->pathParam(1) == 'post' ) {
		
			// text
			$text = substr(stripslashes(strip_tags(html_entity_decode($this->param('text'),ENT_QUOTES,'utf-8'))),0,140);
			$twitter = $this->param('twitter');
			
			// uid
			if ( $this->loged AND $this->param('token') == $this->md5('post-'.$this->uid.$info['id']) ) {
				
				// twit
				$twit = 0;
				
				// post to twitter 
				if ( $twitter == 'yes' ) {
				
					// params
					$p = "source=twittangle&status=".urlencode($text);
								
					$u = array(
						'u' => $this->user['u'],
						'p' => $this->user['p']
					);					
					
					// twitter
					$r = $this->curl("http://twitter.com/statuses/update.json",$u,$p);				
				
					// resp
					$resp = json_decode($r,true);
				
					// twit
					$twit = $resp['id'];
					
					// add to twitter network post
					$tnp = $this->mem->get('twitter:network:posts');
				
					// add
					$tnp[$twit] = array($info['title'],$info['slug']);
				
					// save
					$this->mem->set('twitter:network:posts',$tnp,0);
				
				}				
				
				// post 
				$sql = "
					INSERT INTO
						network_msg
					SET 
						network = '{$info['id']}',
						timestamp = '??',
						user = '??',
						text = '??',
						twitter = '??'
				";
				
				// do it 
				$this->query($sql,array(time(),$this->uid,$text,$twit));

				// clear save cache
				$this->mem->delete("network:post:{$info['id']}:1");
				
				// go 
				$this->go($this->url('network',$info));

			}
			else {
				$this->go( $this->url('login') );
			}
			
		
		}	
			
		// go for it
		$members = $this->queryAndSave($sql,0);
		
			// loged in lets get friends that are the same
			if ( $this->loged ) {
			
				// myFriends
				$myFriends = array();
			
				foreach ( $members as $m ) {
					if ( array_key_exists($m['user_id'],$this->allFriends) ) {
						$myFriends[] = $m;
					}
				}
				
			}
			
		// one page
		$page = $this->param('page',1);
		
		// cache 
		$cid = "network:post:{$info['id']}:$page";
		
		// check
		if ( !($content = $this->mem->get($cid)) ) {
							
			// get some network
			$sql = "
				SELECT 
					m.*,
					n.title as network_title,
					n.id as network_id,
					n.slug as network_slug,
					u.id as user_id,
					u.user as user,
					u.pic as user_pic			
				FROM 
					network_msg as m,
					networks AS n,
					users AS u
				WHERE 
					n.id = '{$info['id']}' AND 
					m.network = n.id AND 
					m.user = u.id
				ORDER BY 
					m.timestamp DESC
			";
			
			// pager
			$pager = array(
				'page' => $page,
				'per' => 30,
				'url' => $this->url('network',$info) . '?page=%d'
			);
		
			// get the 
			$sth = $this->query($sql,false,$pager);
		
			// posts
			$post = array();
			
				// do it
				while ( $row = $sth->fetch_assoc() ) {
					$post[] = $row;
				}
	
			$content = array($post,$pager->_totalPages);
		
			// save
			$this->mem->set($cid,$content,MEMCACHE_COMPRESSED,60);
	
		}
		
		// get it 
		$posts = $content[0];
		$pages = $content[1];
	
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
					
						$this->clearCache(md5("networks.user.{$this->uid}"));
						$this->mem->delete('2087fb142ce1f5f3fef877d82182cfe8');
					
						$id = $this->dbh->insert_id;
					
						// add them
						$this->query("INSERT INTO `network_map` SET `network_id` = '??', `user_id` = '??', timestmp = '??' ",array($id,$this->uid,time()));
					
						foreach ( $fcats as $c ) {
							$this->clearCache("networks.{$c}");
						}
					
						// go 
						$this->go($this->url('network',array('slug'=>$slug)));
					
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