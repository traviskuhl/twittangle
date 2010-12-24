<?php

// api
class api extends Tangle {

	/* constuct */
	public function __construct() {
	
		// parent
		parent::__construct();
		
	}

	
	/* api */
	public function api() {
		
		// sub
		$sub = $this->param('sub');
		
		// reg
		if ( $sub == 'register' ) {
			$this->register();
		}
		else {
				
			//not bea
			if ( !$this->beta ) {
//				$this->go(URI);
			}
			
			// title
			$this->title = "API";
			$this->bodyClass = "api full";
			
			// tmp;
			include( $this->tmpl('api/api') );
			
		}
		
	}
	
	/* register */
	public function register() {
	
		// verify
		$this->validate('register');
	
		// title
		$this->title = "Register Your App";
		$this->bodyClass = "full";	
	
		// tm
		include( $this->tmpl('api/register') );
		
	}

	/* auth */
	public function auth() {
	
		// title
		$this->title = 'Authenticate';
		$this->bodyClass = 'auth full';
	
		// make sure they're a key 
		$key = $this->param('key');
		$sig = $this->param('sig');
	
		// need them to be loged in
		if ( !$this->loged ) {
			$r = base64_encode("/auth?key={$key};sig={$sig}");
			$this->go(URI.'login?r='.$r);
		}
	
		// no key 
		if ( !$key OR !$sig) {
			$this->authError("No API Key or Signature was provided");
		}
		else {
			
			// sql
			$sql = "
				SELECT 
					k.*,
					a.id as auth
				FROM 
					twittangle_api.keys as k
				LEFT JOIN twittangle_api.auth as a ON ( a.key = k.key AND a.user_id = '??' )
				WHERE
					k.key = '??'
				LIMIT 1
			";
			
			// check for a good id 
			$app = $this->row($sql,array($this->uid,$key));
			
				// no app
				if ( !$app ) {
					$this->authError("Invalid API Key");
				}
				
				// need a good sig
				if ( $sig != md5("{$key}.{$app['secret']}.auth") ) {
					$this->authError("Invalid Signature");
				}
				
				// no callback
				if ( !$app['callback'] ) {
					$this->authError("This App does not have a callback defined!");
				}
				
			// check if we're suppose to auth
			if ( $this->param('do') == 'auth' )	{
				
				// make sure the token is good
				if ( $this->param('token') != $this->md5($key.$sig.date("mdY")) ) {
					$this->authError("Invalid request token. Please try again.");
				}
				
				$sql = "
					INSERT INTO twittangle_api.auth 
					SET 
						`key` = '??',
						`user_id` = '??',
						`perm` = '??',
						`timestp` = '??'
				";
	
				// add them
				$r = $this->query($sql,array(
						$app['key'],
						$this->uid,
						'1',
						time()
					));
			
				// no r
				if ( !$r ) {
					_error("Fatal Error: Please contact system admin.");
				}
				
				// app no longer p
				$app['auth'] = $this->dbh->insert_id;
			
			}
				
				
			// have they already authed this app
			if ( $app['auth'] ) {
				
				// remove any current tokens 
				$this->query("DELETE FROM twittangle_api.tokens WHERE `key` = '??' AND `user_id` = '??' ",array($app['key'],$this->uid));
				
				// create a frob for them
				$frob = substr(rand(9,9999999)+round(time()*rand(9,99999)),rand(0,5),10);
				
				// delete any existing frobs fro this app/user combo
				$this->query("DELETE FROM twittangle_api.frobs WHERE `key` = '??' AND `user_id` = '??' ",array($app['key'],$this->uid));
				
				// sql
				$sql = "
					INSERT INTO twittangle_api.frobs
					SET 
						`frob` = '??',
						`key` = '??',
						`user_id` = '??',
						`perm` = '??',
						`expires` = '??',
						`auth_id` = '??'
						
				";
				
				// insert frob into db 
				$r = $this->query($sql,array(
						$frob,
						$app['key'],
						$this->uid,
						'1',
						time()+(60*60),
						$app['auth']
					));
					
				// no r
				if ( !$r ) {
					$this->authError("Fatal Error: Please contact system admin");
				}
				
				// forward to the callback url
				if ( strpos( $app['callbac'],'?') !== false ) {
					$url = $app['callback'] . '&frob='.$frob;
				}
				else {
					$url = $app['callback'] . '?frob='.$frob;
				}
				
				// go there
				header("Location:$url");
				exit;
				
			}
				
		}	
	
		// tmpl
		include( $this->tmpl('api/auth') );
	
	}
	
	public function authError($msg) {
		die($msg);		
	}

}

?>