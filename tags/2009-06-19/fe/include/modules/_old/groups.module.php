<?php

// groups
class groups extends Tangle {

	/* constuct */
	public function __construct() {
	
		// parent
		parent::__construct();
	
	}
	
	/* groups */
	protected function groups() {
	
		// groups
		$this->bodyClass = "groups";
		$this->title = "Groups";	
		
		// verify
		$this->validate();	

		// groups
		$groups = $this->getGroups();
	
		// do 
		if ( $this->param('do') == 'create' ) {
		
			// name
			$name = $this->param('name');
		
			// no name
			if ( !$name ) {
				$this->go(URI."groups");
			}
			
			// clean
			$n = preg_replace("/[^a-zA-Z0-9\s]+/","",$name);
		
			// insert
			$r = $this->query("INSERT INTO `groups` SET `name` = '??', `user` = '??' ",array($name,$this->uid));
			
			// id
			$id = $this->dbh->insert_id;
			
			// clean groups
			$this->clearCache(md5('groups.'.$this->uid));
		
			// go
			$this->go(URI."groups?id=".$id);
		
		}
		else if ( $this->param('do') == 'delete' ) {
		
			// id 
			$id = $this->param('id');
			
				// valid group
				if ( !array_key_exists($id,$groups) ) {
					$this->go(URI.'groups');
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
			$this->go(URI.'groups');			
		
		}
		else {			
			
			// no groups 
			$id = false;
			
			// no groups
			// we should start them out with some
			if ( count($groups) < 1 ) {
			
				// do it 
				$this->query("INSERT INTO `groups` SET `user` = '??', `name` = 'Family' ",array($this->uid));
				$this->query("INSERT INTO `groups` SET `user` = '??', `name` = 'Co-Workers' ",array($this->uid));
				$this->query("INSERT INTO `groups` SET `user` = '??', `name` = 'Classmates' ",array($this->uid));
			
				// send back here
				$this->go("/groups");
			
			}
							
			// id 
			$id = $this->param('id',$groups[key($groups)]['id']);
		
			// friends
			$friends = $this->getGroupUsers($id);			
		
			// rss 
			$this->rssLink = $this->rss($this->user['rss'],$id);			
		
			// tmpl
			include( $this->tmpl('groups/groups') );
			
		}
	
	}	

	/* batch */
	protected function batch() {
		
		$this->validate();

		$this->bodyClass = 'full batch';
		$this->title = ' Batch Grouping ';
		
		// get page
		$page = $this->param('page',1);
		
		// cache
		$cid = md5("twitter.friends.{$page}.{$this->user['id']}");
		$ttl = (60*60*12);
		
		// check
		$data = $this->getCache($cid,$ttl);
		
			// no 
			if ( !$data ) {
			
				// data
				$d = $this->twitter('statuses/friends',array('page'=>$page));
				
				$data = array();
				
					// for each item
					foreach ( $d as $i ) {
						$data[$i['id']] = $i;
					}
				
				// save
				$this->saveCache($cid,$data,$ttl);
				
			}
		
		// more 
		$more = ( count($data) == 100 ? true : false );			
		
		// include
		include( $this->tmpl('groups/batch') );
	
	}




}

?>