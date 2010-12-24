<?php


	/////////////////////////
	/// Framework
	/////////////////////////	
	class Framework  {
			
		// holders
		public $title = "";
		public $bodyClass = "";
		public $mem = false;
        public $dbh = false;
		public $loged = false;
		public $user = false;
		public $uid = false;
		
		/* __construct */
		public function __construct($module=false) {
				
			// module
			$this->module = $module;	
			
			// build our config
			$this->config = array(
				
				// dir
				'dir' => array(
					'tmp' => '/tmp/',				
					'tmpl' => ROOT . 'tmpl/'
				),									
				
				// cookies
				'cookie' => array(
					'session'	=> 'TTa',
					'auth'		=> 'TTb',
					'oauth'		=> 'TTx',
				)
				
			); 
			
			// get db
			$db = include(CONFIG_FILE);						
			
			// set it
			$this->config['db'] = $db['db'];
			
			// add in our host
			$this->config['db']['host'] = 'localhost';
			$this->config['db']['name'] = 'twittangle';
			
			// cache
			$this->cache = Cache::singleton();
							
			// context
			$this->context = p('_context','html');
			
			// try to get a session
			$this->getSession();

		}
		
		function __destruct() {
			
		}
		
		public function md5($str) {
			return md5("jf89pohij2;3'damiufj".$str."84$89adfaw349408 43a4 038w4r awef aweufh7ao38rhuanwk/ mef");
		}
		
		public function requireSession() {
		
			if ( !$this->loged ) {
				$this->go($this->url('login'));
			}
		
		}
		
		public function getCookie($name) {
			$cname = $this->config['cookie'][$name];
			return p($cname,false,$_COOKIE);
		}
		
		public function setCookie($name,$val,$exp=false) {
			$cname = $this->config['cookie'][$name];		
			setcookie($cname,$val,$exp,'/');
		}
		
		public function getSession() {
		
			// session
			$sess = $this->getCookie('session');
			$user = $this->getCookie('auth');		
			
				// no session
				if ( !$sess OR !$user ) {	
					return false;
				}
		
			// get session info
			$info = unserialize( base64_decode( $sess ) );
		
				// user match
				if ( $this->md5($info['user']) != $user ) {
					return false;
				}

			// get session from database
			$row = $this->cache->get($info['sid'],"session");
		
				// no row or row and no password
				// or row and ttl is reached
				// invalid session. force to login
				// again
				if ( !$row OR ( $row AND (time() > $row['ttl']) ) ) {
				
					// looks like a bad session
					// so we need to unset the session cookie
					$this->setCookie('session',false,time()+1);
					$this->setCookie('auth',false,time()+1);

					// go to login
					$this->go( $this->url('index'));
	
				}			
		 
		 	// save user 
		 	$this->user = $row;
		 	
		 	// fix info
		 	$this->user['info'] = @unserialize($row['info']);
		 			 			 	
		 	// scrern
		 	$this->user['user'] = $this->user['info']['screen_name'];
		 	
		 	// add u and p for curl
		 	$this->user['u'] = $row['user'];
	 	
	 		// get
	 		$tok = json_decode($row['oauth'],true);
	 	
			// reset oauth with the access tokens 
			$this->oauth = new TwitterOAuth(AUTH_KEY, AUTH_SECRET, $tok['oauth_token'], $tok['oauth_token_secret']);		 	
						 	
	 			// no oauth
	 			if ( !$this->oauth ) {
	 				
					// looks like a bad session
					// so we need to unset the session cookie
					setcookie($this->ttCookie,false,time()+1,'/',DOMAIN);

					// go to login
					$this->go( $this->url('index'));		 				
	 				
	 			} 
		 	// add reque id
		 	$this->user['req'] = $info['req'];
		 	
		 	$this->uid = $row['id'];
		 	$this->req = $info['req'];
		 	$this->session = $info;
		 	
		 	// loged
		 	$this->loged = true;		 		

		}		
		
		public function url($key,$data=false,$params=false) {
			return Config::url($key,$data,$params);
		}
		
		/**
		 * does the actual page creation
		 * @method	build
		 */
		public function dispatch($act=false,$tmpl=false) {
		
			// act
			if ( !$act ) {
				$act = str_replace('-','',p('act','main'));
			}
		
			// exists
			if ( !method_exists($this,$act) ) {
				$act = 'main';
			}							
			
			// start ob
			ob_start(); ob_clean();

				// tmpl
				if ( $tmpl ) {
					include($tmpl);
				}
				else {
					call_user_func(array($this,$act));
				}
				
			// get
			$Body = ob_get_contents();
			
			// end
			ob_end_clean();
		
			// what context 
			if ( $this->context == 'xhr' ) {
			
			    // header
	            header("Content-Type: text/javascript");
	        
				// need to remove comments 
				list($body,$js) = $this->parseHtmlForXhr($Body);
						        
	        
	            // make it nice
	            exit( json_encode( array( 
	            	'stat' => '1', 
	            	'html' => $body, 
	            	'bootstrap' => array('c'=>$this->bodyClass,'js'=> $js,'t'=>$this->title) 
	            )) );
	            
	        }
		
			// include the header
			include( $this->tmpl('page') );
		
		}
		
		public function parseHtmlForXhr($body) {
		
			// need to remove comments 
			$body = preg_replace(array("/\/\/[a-zA-Z0-9\s\&\?\.]+\n/","/\/\*(.*)\*\//")," ",$body);
			
			// javascript 		
			$jsInPage = preg_match_all("/((<[\s\/]*script\b[^>]*>)([^>]*)(<\/script>))/i",$body,$js);		
			
		
				// if yes remove 
				if ( $jsInPage ) {
					$body = preg_replace("/((<[\s\/]*script\b[^>]*>)([^>]*)(<\/script>))/i","",$body);
				}	
			
			// give back
			return array($body,@$js[3]);
		
		}
	
		/**
		 * get a template path
		 * @method	tmpl
		 */
		public function tmpl($file) {
		
			// check for ending
			if ( strpos($file,'.tmpl.php') === false ) {
				$file .= '.tmpl.php';
			}
		
			// return
			return $this->config['dir']['tmpl'] . $file;
		
		}
		
		public function setCache($cid,$data,$ttl) {		
			if ( $this->mem ) {
				return $this->mem->set($cid,$data,MEMCACHE_COMPRESSED,$ttl);
			}
			return false;
		}

		public function getCache($cid) {
			if ( $this->mem ) {
				return $this->mem->get($cid);
			}
			return false;
		}
		
		public function connectToDb() {
		  
            if ( $this->dbh ) {
                return;
            }
            
            // connect
            try {
                $this->dbh = new PDO("mysql:host={$this->config['db']['host']};dbname={$this->config['db']['name']}",$this->config['db']['user'],$this->config['db']['pass']);
            }
            catch ( PDOException $e ) {
                error_log( $e->getMessage() );
                exit("Could not connect to database ".$e->getMessage());
            }
		
		}
	

		/**
		 * query the db
		 * @method	query
		 * @param	{string} 		sql
		 * @param	{array}			params
		 * @param	{ref:array}		pager args
		 * @return	{object}		mysqli resuls object
		 */
		public function query( $sql, $params=array(), &$total=false ) {

			// connect to db
			// don't worry this is only done once
			$this->connectToDb();

			// need a query
			if ( !$sql ) die('no sql');
			
			// some cleanup
			$sql = str_replace("'??'","?",$sql);
			
			// if selct and total
			if ( $total !== false AND stripos($sql,"SELECT") !== false ) {
                $sql = preg_replace("/^SELECT/i","SELECT SQL_CALC_FOUND_ROWS",$sql);
			}

			// run sql
			$sth = $this->dbh->prepare($sql);
			
			// eexecite
			$r = $sth->execute($params);

			// die
			if ( !$r OR !$sth OR ( $this->dbh->errorCode() != '00000' ) ) {
			
                // get
                $er = $this->dbh->errorInfo();
			
                // log
                error_log("[SQL ERROR] {$sql}\n\n {$er[2]}\n");
                
			}
			
			// return
			$r = array();
			
			// now lets see what happend
			if ( stripos($sql,'INSERT INTO') !== false ) {											
				$r = $this->dbh->lastInsertId();				
			}
			else if ( stripos($sql,'UPDATE') !== false ) {											
                $r = false;
			}			
			else if ( $sth AND $sth->rowCount() > 0 ) {
                $r = $sth->fetchAll(PDO::FETCH_ASSOC);
			}
			
			// totoal
			if ( $total !== false ) {
			 
                // get total
                $t = $this->row("SELECT FOUND_ROWS() as t ");
                
                // set it 
                $total = $t['t'];
                
			}			

			// give back r;
			return $r;

		}
		

		/**
		 * perform a query an return the first row
		 * @method	row
		 * @param	{string}	sql
		 * @param	{array}		params
		 * @return	{array}		results array
		 */
		public function row( $sql, $params=array() ) {
		
			// run sql
			$sth = $this->query($sql,$params);

			// return
			if ( count($sth) > 0 ) {
				return $sth[0];
			}
			else {
				return array();	
			}
			
		}


		/**
		 * clean a string for mysql
		 * @method	clean
		 * @param	{string}	dirty string
		 * @return	{string}	clean string 
		 */
		public function clean($str) {
			return $this->dbh->real_escape_string($str );
		}		
		
		
		public function go($url) {
			exit( header("Location:".$url) );
		}
		
		public function validateStr($str,$as,$return=false) {
		
			// what up
			if ( $as == 'hosturl' ) {
				
				// parse
				$host = parse_url($str,PHP_URL_HOST);
				$local = parse_url(HOST,PHP_URL_HOST);
				
				if ( $host == $local ) {
					return $str;
				}
				
			}
		
			// bad
			return $return;
		
		}
		
				
				/**
		Validate an email address.
		Provide email address (raw input)
		Returns true if the email address has the email 
		address format and the domain exists.
		*/
		function validateEmail($email)
		{
		   $isValid = true;
		   $atIndex = strrpos($email, "@");
		   if (is_bool($atIndex) && !$atIndex)
		   {
		      $isValid = false;
		   }
		   else
		   {
		      $domain = substr($email, $atIndex+1);
		      $local = substr($email, 0, $atIndex);
		      $localLen = strlen($local);
		      $domainLen = strlen($domain);
		      if ($localLen < 1 || $localLen > 64)
		      {
		         // local part length exceeded
		         $isValid = false;
		      }
		      else if ($domainLen < 1 || $domainLen > 255)
		      {
		         // domain part length exceeded
		         $isValid = false;
		      }
		      else if ($local[0] == '.' || $local[$localLen-1] == '.')
		      {
		         // local part starts or ends with '.'
		         $isValid = false;
		      }
		      else if (preg_match('/\\.\\./', $local))
		      {
		         // local part has two consecutive dots
		         $isValid = false;
		      }
		      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
		      {
		         // character not valid in domain part
		         $isValid = false;
		      }
		      else if (preg_match('/\\.\\./', $domain))
		      {
		         // domain part has two consecutive dots
		         $isValid = false;
		      }
		      else if
		(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
		                 str_replace("\\\\","",$local)))
		      {
		         // character not valid in local part unless 
		         // local part is quoted
		         if (!preg_match('/^"(\\\\"|[^"])+"$/',
		             str_replace("\\\\","",$local)))
		         {
		            $isValid = false;
		         }
		      }
		   }
		   return $isValid;
		}
	
		public function curl($url,$params=false,$headers=false,$userinfo=false) {
		
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		
			if ( is_array($userinfo) ) {
				curl_setopt($ch, CURLOPT_HTTPAUTH,CURLAUTH_BASIC);
				curl_setopt($ch, CURLOPT_USERPWD, $userinfo['u'].':'.$userinfo['p']);
			}
			if ( $params ) {
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
			}
			if ( $headers ) {
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			}
			
			$output = curl_exec($ch);  		
			
			curl_close($ch);
			
			return $output;
		
		}
	
		
	}	


?>