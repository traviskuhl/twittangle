<?php

class groups extends Tangle {
	

	public function __construct() {
			
		// arent
		parent::__construct();	
		
		// tags
		$cid = md5('pub.groups.tags');
		
			// tags
			if ( true OR !($tags = $this->getCache($cid)) ) {
			
				// get network categories 
				$sth = $this->query("SELECT tags FROM groups WHERE tags != '' AND pub = 1 LIMIT 1000");	
			
				// tags
				$tags = array();
			
				// get the tags
				while ( $row = $sth->fetch_assoc() ) {
					
					// add each
					foreach ( explode(',',$row['tags']) as $t ) {
						if ( array_key_exists($t,$tags) ) {
							$tags[$t] += 1;
						}
						else {
							$tags[$t] = 1;
						}
					}
					
				}
				
				// sort
				arsort($tags);			
				
				// save
				$this->saveCache($cid,$tags,(60*60));
				
			}
		
		// tags
		$this->_tags = $tags;		
		
		// body
		$this->bodyClass = 'pubgroups';
		
	}
	
	public function main() {
	

		
		// popular
		$cid =  md5('pub.groups.popular');
		
			// get the 
			if ( true OR !($popular = $this->getCache($cid)) ) {
				
				// get them
				$sql = "
					SELECT 
						COUNT(f.user) as count,
						g.id,
						g.name,
						g.settings,
						g.tags,
						u.user,
						u.pic
					FROM 
						group_favs as f,
						groups as g 
						LEFT JOIN users as u ON ( g.user = u.id ) 
					WHERE 
						f.group = g.id AND 
						g.pub = 1
					GROUP BY f.group
					ORDER BY 
						count DESC
					LIMIT 10
				";
			
				// do it 
				$sth = $this->query($sql);
				
				// popular
				$popular = array();
			
				// do that shit
				while ( $row = $sth->fetch_assoc() ) {
					$popular[] = $row;
				}
				
				// save
				$this->saveCache($cid,$popular,(60*60));
				
			}
					
		// sql
		$sql = "
			SELECT 
				g.*,
				u.user,
				u.pic
			FROM 
				groups as g
				LEFT JOIN users as u ON ( g.user = u.id ) 
			WHERE
				g.pub = 1
			ORDER BY g.created DESC
			LIMIT 10
		";
	
		// get network categories 
		$newest = $this->queryAndSave($sql,(60*60*24),true);		
		
		// by tag
		$cid = md5('pub.groups.bytag');
	
			// get it 
			if ( true OR !($byTag = $this->getCache($cid)) ) {
			
				$byTag = array();
			
				// each tag
				foreach ( array_slice($this->_tags,0,10) as $tag => $count ) {
					
					// get groups with that tag
					$sql = "
						SELECT 
							g.*,
							u.user,
							u.pic,
							( SELECT COUNT(*) FROM group_favs WHERE `group` = g.id ) as `favs`
						FROM 
							groups as g,
							users as u
						WHERE 
							g.pub = 1 AND
							FIND_IN_SET( '??', LOWER(g.tags) ) AND 
							g.user = u.id
						ORDER BY 
							`favs` DESC
						LIMIT 10
					";
				
					// query 
					$sth = $this->query($sql,array( strtolower($tag) ));
					
					// groups
					$groups = array();
					
					while ( $row = $sth->fetch_assoc() ) {
						$groups[] = $row;
					}
					
					// by tags
					$byTag[$tag] = $groups;
					
				}
				
				// save
				$this->saveCache($cid,$byTag,60);
			
			}
	
		// include
		include( $this->tmpl('groups/main') );
	
	}
	
	public function tag() {
	
		// tag 
		$tag = str_replace('-',' ',$this->pathParam(0));

		// get groups with that tag
		$sql = "
			SELECT 
				g.*,
				u.user,
				u.pic,
				( SELECT COUNT(*) FROM group_map WHERE `group_id` = g.id ) as count,
				( SELECT COUNT(*) FROM group_favs WHERE `group` = g.id ) as `favs`
			FROM 
				groups as g,
				users as u
			WHERE 
				g.pub = 1 AND
				FIND_IN_SET( '??', LOWER(g.tags) ) AND 
				g.user = u.id
			ORDER BY 
				`favs` DESC
			LIMIT 10
		";	
		
		// tag
		$sth = $this->query($sql,array($tag));
		
		// include
		include( $this->tmpl('groups/tag') );
	
	}
	
	
	public function _getDesc($g) {
		if ( $g['settings'] ) {
			$s = json_decode($g['settings'],true);
			if ( isset($s['desc']) AND !empty($s['desc']) ) {
				return $s['desc'];
			}
		}
	}
	
}

?>