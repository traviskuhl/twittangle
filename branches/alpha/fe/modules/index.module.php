<?php

class index extends Fe {

	public function __construct() {
		parent::__construct();
	}
	
	// main 
	public function main() {
	
		// template
		include( $this->tmpl('index/main') );
	
	}

	public function profile() {
	
		// sn
		$id = p('user');
	
		// get some user info
		$user = $this->getUser($id);
		
		// timeline
		$timeline = $this->getUserTimeline(array(
			'type' => 'user',
			'user' => $id
		));
	
		// include
		include( $this->tmpl("index/profile") );
	
	}

	// login
	public function login() {
	
		// get cookie
		$cookie = $this->getCookie('oauth');

		// save the token in the db
		if ( p('do') == 'auth' ) {
	
			// delete any current cookies
			$this->setCookie('session',false,time()+1);
			$this->setCookie('auth',false,time()+1);
	
			// oath
			$to = new TwitterOAuth(AUTH_KEY, AUTH_SECRET);  
		
			// get token 
			$tok = $to->getRequestToken();
			
			// ip
			$ip = $_SERVER['REMOTE_ADDR'];			
			
			// make an id
			$id = md5( time() * rand(9,time()) - time() * 9 . $ip );
			
			// what to save
			$d = array( 'ip' => $ip, 'token' => $tok);
						
			// data
			$data = json_encode( $d );
						
			// save
			$this->query("INSERT INTO oauth SET `id` = '??', `data` = '??' ", array($id,$data) );
			
			// value
			$val = base64_encode( serialize( array('id' => $id, 'ip' => $ip, 'sig' => md5($data) ) ) );
			
			// set a cookie
			$this->setCookie('oauth',$val);

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
					header("Location:/?badcookie");
				}

			// get from the db
			$row = $this->row("SELECT * FROM oauth WHERE `id` = ? ",array($c['id']));
			
			// data
			$data = @json_decode($row['data'],true);	
			
			// we good 
			if ( $row AND is_array($data) AND $c['ip'] == $_SERVER['REMOTE_ADDR'] AND $c['sig'] == md5($row['data']) ) {
			
				// delete oauth
				$this->query("DELETE FROM oauth WHERE id = '??' ",array($c['id']));		
			
				// no cookie
				$this->setCookie("oauth",false,time()+1);
			
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
				
					// what should our ttl be 
					$ttl = time()+(60*60*24*14);
																
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
							`id` = ?,
						 	`user` = ?,
						 	`password` = '',
						 	`password2` = '',
						 	`reg_timestp` = UNIX_TIMESTAMP(),
						 	`last_timestp` = UNIX_TIMESTAMP(),
						 	`ttl` = '{$ttl}',
						 	`pic` = ?,
						 	`info` = ?,
						 	`sid` = '{$sid}',
						 	`oauth` = ?
						 ON DUPLICATE KEY UPDATE
						 	`password` = '',
						 	`password2` = '',
						 	`ttl` = '{$ttl}',
						 	`pic` = ?,
						 	`info` = ?,
						 	`sid` = '{$sid}',
						 	`oauth` = ?
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
					if ( $db !== false ) {
					
						// get them
						$usr = $this->row("SELECT * FROM users WHERE id = ? ",array($resp['id']));
						
						// save me some session
						$this->cache->set($sid,$usr,$ttl,'session');
						
						// generate a session
						$session = base64_encode( serialize(array( 'user' => $resp['id'], 'sid' => $sid, 'ttl' => $ttl, 'req' => base64_encode(rand(9,99)*time()) )) );
					
						// set it 
						$this->setCookie('session',$session,$ttl);
						$this->setCookie('auth', $this->md5($resp['id']), $ttl );					
					
						// go			
						$this->go( $this->url('home') );
					
					}
				
				}
				else {
					header("Location:/?badtwitter");
				}
			
			
			}			
			else {
				header("Location:/?badsig");				
			}
			
		
		}
		
		// send index
		header("Location:/?nothing");		
	
	}

}

?>