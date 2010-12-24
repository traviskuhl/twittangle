<?php

// tt
include("/var/www/vhosts/twittangle.com/include/Config.class.php");

/* updates extends tangle */
class updates extends Tangle {

	/* construct */
	public function __construct() {
	
		// construct
		parent::__construct('cron');
	
		// get the group
		$group = substr(date('m'),1,1);
		
		// get some users
		$sql = "
			SELECT 
				u.id,
				u.user,
				u.password2,
				u.last_timestp,
				(SELECT COUNT(user) FROM friends WHERE user = u.id) as count
			FROM
				users as u
			WHERE
				u.update_group = '??' AND 
				u.password2 != ''
		";
		
		// get htem
		$sth = $this->query($sql,array($group));
		
		// user
		$users = array();
		
			// place them
			while ( $row = $sth->fetch_assoc() ) {
				if ( $row['count'] > 0 AND time()-$row['last_timestp'] < (60*60*24*7) ) {
					$users[] = $row;
				}
			}

		// check
		$this->check($users);
		
	}
	
	/* users */
	public function check($users) {
		
		// first get a list of my friends
		foreach ( array_slice($users,0,200,true	) as $u ) {
		
			echo "{$u['id']}\n";
		
			// updates
			$updates = array();
			
			// set user
			$this->user = array(
				'u' => $u['user'],
				'p' => base64_decode(base64_decode($u['password2']))
			);		
			
			// cid
			$cid = md5($u['id'].'profiles');
			$ttl = (60*60*24*10);
			$cache = array();

			// get my last one
			$last = $this->getCache($cid,$ttl,'updates');
			
			// for 
			for ( $i = 0; $i < 2; $i++ ) {

				// req
				$friends = $this->twitter("statuses/friends/{$u['id']}.json?page={$i}");			
				
				// none
				if ( !$friends OR !is_array($friends) ) {
					break;
				}
				
				// figure out what is different 
				foreach ( $friends as $f ) {
				
					// don't need a status
					unset($f['status']);
				
					// save for cache
					$cache[$f['id']] = $f;
				
					// now lets compare 
					if ( $last AND array_key_exists($f['id'],$last) ) {
					
						// diff 
						$diff = $this->getDiff($f,$last[$f['id']]);
						
						// updates
						if ( count($diff) > 0 ) {
							$updates[] = array( 'user' => $u['id'], 'friend' => $f, 'diff' => $diff );
						}
						
					}
				
				}
				
				// no more pages
				if ( count($friends) < 100 ) {
					break;
				}
			
			}
			
			// save this cache 
			$this->saveCache($cid,$cache,$ttl,'updates');	
						
		}
		
		// save updates
		$this->saveUpdates($updates);
	
	}
	
	/* save updates */
	public function saveUpdates($updates) {
	
		// if none
		if ( count($updates) == '0' ) {
			return;
		}
	
		// save 
		foreach ( $updates as $u ) {
			$this->query("INSERT INTO updates.updates SET `user` = '??', `timestmp` = '??', `data` = '??' ",array(
				$u['user'],
				time(),
				json_encode($u)
			));		
		}
	
	}
	
	/* get diff */
	public function getDiff($new,$old) {
		
		// diff
		$diff = array();
	
		// go for it
		foreach ( $new as $k => $v ) {
			if ( !is_array($v) ) {
				if ( array_key_exists($k,$old) AND $old[$k] != $v ) {
					$diff[$k] = array('new' => $v, 'old' => $old[$k]);
				}
			}
		}
		
		// give back diff
		return $diff;
	
	}
	
}

// new
$u = new updates();

?>