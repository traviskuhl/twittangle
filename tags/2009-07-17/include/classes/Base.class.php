<?php

	// moviestring
	class Base extends Config {
	
		/* public */
		public $module = "";
		public $globalTmpl = "page";
		public $bodyClass = "";
		public $title = null;
		public $rateLimit = "";
		public $defaultPage = "main";
		
		/* protected */
		protected $restrict = false;
		public $dbh;		
			
		private $toggles = array();		
	
		/**
		 * @method	construct 
		 */
		function __construct($module='none') {
			
			// module 
			$this->module = $module;
			
			// construct 
			config::__construct();
			
			// get db
			$config = include(CONFIG_FILE);			
						
			// connect 
			$this->connect($config['db']);	

			

		}
		
			// destroy 
			public function __destruct() {
								

			
			}
		
		public function mod_header() {}
		public function mod_footer() {}
	
		/** Connect **/
		protected function connect($info=null) {

			// no info 
			if ( !$info ) {
				$info = $this->db;
			}
			
			// already sht 
			if ( isset($this->dbh) ) {
				$this->dbh->close();
			}

			// try and connect
			$this->dbh = new mysqli('localhost',$info['user'],$info['pass'],$info['db']);

			// charset
			$this->dbh->set_charset('utf8');

			/* check connection */
			if (mysqli_connect_errno() OR !$this->dbh) {
			    printf("Connect failed: %s\n", mysqli_connect_error());
			    exit();
			}

		}		
	
		/**
		 * @method	dispatch
		 */
		public function dispatch() {
			
			// get page param
			$this->page = preg_replace('/[^a-zA-Z0-9\_]+/','',$this->param('act','main'));
		

			// make sure the method exists 
			// in the allowed modules thing
			if ( !method_exists($this,$this->page) ) {
				$this->page = $this->defaultPage;
			}		
			
					
			// start 
			$__start = $this->mtime();					
								
			// get contents 
			ob_start();
		
				// call it into buffer			
				call_user_func(array($this,$this->page));
				
				// get 
				$body = ob_get_contents();
			
			// clean
			ob_clean();
								
				
				// figure out what to show
				if ( is_array($this->customProfile) AND strpos($this->customProfile['profile_background_image_url'],'static.twitter.com') === false ) {
					foreach ( $this->customProfile as $key => $val ) {
						if ( $val ) {
							switch($key) {									
								case 'profile_background_image_url':
									$this->profileCss .= " 
										body { 
											background-image: url($val); 
											background-repeat: no-repeat; 
											background-attachment: fixed;
										} 
										body #doc4 { background:none; } 
										body #doc4 #hd .wrap { background-color: transparent; }
										body #doc4 #hd form.search { background: transparent; border: none; }  
										body #doc4 #hd form.search fieldset { border: solid 1px #fff; }  
										body #doc4 #hd a.logo { 
											-moz-box-shadow: #CCC 0px 0px 6px 1px;	
											-webkit-box-shadow: 0px 0px 6px #CCC;													
										}
									"; 
									break;
									
								case 'profile_background_tile':									
									$this->profileCss .= " body { background-repeat: repeat; } "; break;																		
									
								case 'profile_background_color':
									$this->profileCss .= " html, body { background-color: #{$val}; } "; 
									break;
									
								case 'profile_text_color':
									$this->profileCss .= " #bd #page-content ul.timeline { color: #{$val}; } "; break;
									
								case 'profile_link_color':
									$this->profileCss .= " #bd #page-content ul.timeline a { color: #{$val}; } "; break;
									
							}
						}
					}
				}	
				
			// check on rate limit 
			if ( $this->loged ) {
			
				// rate
				$rate = $this->twitter('account/rate_limit_status');			
				
				if ( $rate ) {
					
					// reset
					$reset = $rate['reset_time_in_seconds'] + (int)$this->user['info']['utc_offset'];				
				
					// rate
					$this->rateLimit = " - Requests: {$rate['remaining_hits']}/{$rate['hourly_limit']} (".date('g:ia',$reset).")";
					
				}
				
			}
			
			// check for content
			if ($this->param('_ctx') == 'xhr') {
				$this->xhrTemplate($body);
			} 
								
			// utf8
			header('Content-type: text/html; charset=UTF-8');
										
			// include the header 
			include( $this->tmpl($this->globalTmpl) );
			
			// exit 
			exit();
			
		}	

		
		/**
		 * xhr template
		 */
		public function xhrTemplate($body) {
		
			// expires 
			$expires = 60 * 3;			
		
			header("Content-Type:text/javascript");		
		
			if (!$this->alpha) {
		
			// header
			header( 'Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
			header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', time()-$expires ) . ' GMT' );
			header( "Cache-Control: max-age={$expires}, must-revalidate" );
			header( "Pragma:");
			
			}
			
			// need to remove comments 
			$body = preg_replace(array("/\/\/[a-zA-Z0-9\s\&\?\.]+\n/","/\/\*(.*)\*\//","/\n+|\t+/","/\s+/")," ",$body);
			
			// javascript 		
			$jsInPage = preg_match_all("/((<[\s\/]*script\b[^>]*>)([^>]*)(<\/script>))/i",$body,$js);		
			
		
				// if yes remove 
				if ( $jsInPage ) {
					$body = preg_replace("/((<[\s\/]*script\b[^>]*>)([^>]*)(<\/script>))/i","",$body);
				}						
				
			// print it 
			exit( json_encode( array( 
				'stat' => 1,
				'html' => $body, 
				'bootstrap' => array( 'js' => @$js[3] ),
				'c' => $this->bodyClass,
				't' => html_entity_decode( strip_tags($this->title) . ' on twitTangle | untangling the mess of too many friends ',ENT_QUOTES,"utf-8"),
				'r' => (isset($this->rssLink)?$this->rssLink:''),
				'p' => $this->profileCss,
				'l' => ""
			) ));		
		
		}
		
		public function parseXhrData($body) {
			
			// need to remove comments 
			$body = preg_replace(array("/\/\/[a-zA-Z0-9\s\&\?\.]+\n/","/\/\*(.*)\*\//","/\n+|\t+/","/\s+/")," ",$body);
			
			// javascript 		
			$jsInPage = preg_match_all("/((<[\s\/]*script\b[^>]*>)([^>]*)(<\/script>))/i",$body,$js);		
			
		
				// if yes remove 
				if ( $jsInPage ) {
					$body = preg_replace("/((<[\s\/]*script\b[^>]*>)([^>]*)(<\/script>))/i","",$body);
				}				
				
			return array($body, $js[3]);
			
		}
		
		public function json($obj) {
			return preg_replace("/>/","&gt;",json_encode($obj));
		}
	
		/** query **/
		public function query( $sql, $params=0, &$pagerArgs=null ) {

			// need a query
			if ( !$sql ) die('no sql');

			// params
			if ( is_array($params) ) {
				foreach ( $params as $p ) {
					$sql = preg_replace("/\?\?/i",$this->clean($p),$sql,1);
				}
			}
			
			if ( isset($this->__dieOnQuery) ) {
				die($sql);
			}
			
			
			// if pager does not eqal null 
			if ( is_array($pagerArgs) ) {
				
				// sql 
				$parts = $this->_parse_sql($sql);												
																							
				// now do our sql 
				$count = $this->row(" SELECT COUNT(*) as `c` FROM {$parts['from']} WHERE {$parts['where']} ");
				
				// page 
				if ( !array_key_exists('page',$pagerArgs) AND $this->param('page') ) {
					$pagerArgs['page'] = $this->param('page');
				}
				else if ( !array_key_exists('page',$pagerArgs) ) {
					$pagerArgs['page'] = 1;
				}
				
				// defaults 
				if ( !array_key_exists('per',$pagerArgs) ) {
					$pagerArgs['per'] = 10;
				}
				
				if ( !array_key_exists('url',$pagerArgs) ) {
					$pagerArgs['url'] = '#%d';
				}

				if ( !array_key_exists('delta',$pagerArgs) ) {
					$pagerArgs['delta'] = 10;
				}
				
				$per = $pagerArgs['per'];
				
				// figure start 
				$start = ( $pagerArgs['page'] - 1 ) * $pagerArgs['per'];				
				
				// now start pager 
				$pagerArgs = Pager::factory( array(
						'mode'			=> 'Sliding',
						'perPage'		=> $pagerArgs['per'],
						'delta'			=> $pagerArgs['delta'],		
						'totalItems'	=> $count['c'],
						'fileName'		=> $pagerArgs['url'],
						'spacesBeforeSeparator' => 0,
						'spacesAfterSeparator' => 0,
						'separator' 	=> "",
						'append'		=> false,
						'path'			=> "",
						'currentPage'	=> $pagerArgs['page'],
						'linkClass'		=> 'pager',
						'prevImg'		=> '<',
						'nextImg'		=> '>'
					));
					

				
				// give it to sql 
				$sql = $sql . " LIMIT {$start}," . $per;			

				
			}
			
			// run sql
			$sth = $this->dbh->query($sql);

			// die
			if ( $this->dbh->error ) {
				error_log("\n\n[SQL ERROR] {$this->dbh->error}\n\n");
			}

			return $sth;

		}

		/** query_a **/
		public function query_a( $sql, $params=0 ) {

			// get reuslts
			$sth = $this->query($sql,$params);

			// hilder
			$array = array();

			while ( $row = $sth->fetch_assoc() ) {
				$array[] = $row;
			}

			return $array;
		
		}


		/** row **/
		public function row( $sql, $params=0 ) {
		
			// run sql
			$sth = $this->query($sql,$params);

			// return
			return $sth->fetch_assoc();
			
		}

		/** clean **/
		public function clean($str) {
			return $this->dbh->real_escape_string($str );
		}
		
		public function getCacheFile($key,$type='fe') {
			return $this->config['dir']['cache'] . $type . '/' . $key{0} . '/' . $key;
		}
		
		/* getCache */
		public function getCache($key,$ttl=null,$type='fe',$override=false) {
		
			// no ttl 
			if ( !$ttl ) {
				$ttl = (60*60);
			}
			
			// file 
			$file = $this->config['dir']['cache'] . $type . '/' . $key{0} . '/' . $key;
					
			// file exists 
			if ( file_exists( $file ) AND ( $override === true OR time()-filemtime($file) < $ttl ) ) {
			
				// if override 
				if ( $override === true ) {
					touch($file,time());
				}
			
				// get the file 
				include($file);
			
				// get data 
				$data = unserialize( gzuncompress( base64_decode( $cache['data'] ) ) );
				
				// give it 
				return $data;
				
			}
			
			// false 
			return false;
					
		}
		
		public function saveCache($key,$data,$ttl=0,$type='fe') {		
		
			// make d 
			$d = base64_encode( gzcompress( serialize($data) ) );
		
			// sub dir 
			$sub = $key{0};
			
			// check if the sub dir exists 
			if ( !file_exists( $this->config['dir']['cache'] . $type . '/' . $sub ) ) {
				mkdir( $this->config['dir']['cache'] . $type . '/' . $sub,0777, true );
				chmod($this->config['dir']['cache'] . $type . '/' . $sub,0777);
			}		
		
			// file 
			$file = $this->config['dir']['cache'] . $type . '/' . $sub .'/'. $key;
			
			// get the data 
			$data = "<?php \$cache = array( 'ttl' => {$ttl}, 'data' => '{$d}' ); ?>";
			
			// save it 
			file_put_contents($file,$data);
		
		}
		
		public function clearCache($key,$type='fe') {
		
			// sub dir 
			$sub = $key{0};
			
			// file 
			$file = $this->config['dir']['cache'] . $type . '/' . $sub .'/'. $key;			
			
			// check if the sub dir exists 
			if ( file_exists( $file ) ) {
				unlink( $file );
			}				
		
		}
		
		private function _parse_sql($sql) {
		
			// array with parts 
			$parts = array(
				"select" => null,
				"from" => null,
				"where"	=> 1,
				"order"	=> null,
				"join" => null
			);
			
			// clean sql 
			$sql = preg_replace( array("/\t|\n/","/\s+/"), array(" "," "),$sql);
		
			// figure out what's there 
			if ( strpos($sql,'WHERE') AND strpos($sql,'ORDER BY') ) {
				
				// run a preg for order 
				preg_match("/(.*)FROM(.*)WHERE(.*)ORDER BY(.*)/i",$sql,$matched);
							
				// set the matches
				$parts['select'] = trim(str_replace('SELECT','',$matched[1]));
				$parts['from'] = trim($matched[2]);
				$parts['where'] = trim($matched[3]);
				$parts['order'] = trim($matched[4]);
				
			}		
			else if ( strpos($sql,'ORDER BY') ) {
				
				// run a preg for order 
				preg_match("/(.*)FROM(.*)ORDER BY(.*)/i",$sql,$matched);
				
				// set the matches
				$parts['select'] = trim(str_replace('SELECT','',$matched[1]));
				$parts['from'] = trim($matched[2]);
				$parts['where'] = 1;
				$parts['order'] = trim($matched[3]);
				
			}
			else {
			
				// run a preg for order 
				preg_match("/(.*)FROM(.*)WHERE(.*)/i",$sql,$matched);
				
				// set the matches
				$parts['select'] = trim(str_replace('SELECT','',$matched[1]));
				$parts['from'] = trim($matched[2]);
				$parts['where'] = trim($matched[3]);		
			
			}
			
			// lefthoun 
			if ( strpos($parts['from'],"LEFT JOIN") !== false ) {
						
				// get it 
				preg_match("/(.*)LEFT JOIN(.*)/i",$parts['from'],$match);
						
				// match 
				$parts['from'] = $match[1];
				$parts['join'] = $match[2];
						
			}
			
			// srip out limit 
			$parts['order'] = preg_replace("/LIMIT [0-9]{1,},? ?([0-9]{1,})?/","",$parts['order']);
			
			// return the parts
			return $parts;		
		
		}
		
		// go 
		public function go($url) {
			exit(header("Location:".$url));
		}
		
		public function toggle($id='default') {
		
			if ( !array_key_exists($id,$this->toggles) ) {
				$this->toggles[$id] = 'odd';
			}
		
			// switch
			if ( $this->toggles[$id] == 'even' ) {
				$this->toggles[$id] = 'odd';
				return 'odd';
			} 
			else {
				$this->toggles[$id] = 'even';
				return 'even';			
			}
		
		}
		
		public function message($message,$url,$timeout=5) {
		
			// include the header 
			include_once( $this->tmpl('global/header') );

			// message
			include_once( $this->tmpl('global/message') );		
			
			// footer 
			include_once( $this->tmpl('global/footer') );
			
			// exit
			exit(0);
		
		}
	
		public function error_page($message,$fatal=false) {
			if ($fatal) {
				die("Fatal Error: {$message}");
			}
			else {
				
				// include the header 
				include_once( $this->tmpl('global/header') );
	
				// message
				include_once( $this->tmpl('global/error') );		
				
				// footer 
				include_once( $this->tmpl('global/footer') );
				
				// exit
				exit(0);			
			
			}
			
		}		
		
		/* array to list */
		public function arrayToList($array) {
			$list = array();
			foreach ( $array as $key => $val ) {
				$list[] = "'$val'";
			}
			return implode(', ',$list);
		}
	
		/* newcurl */
		public function newCurl($url,$userinfo=null,$params=null,$headers=null) {
			
			return $this->curl($url,$userinfo,$params,$headers);
			
			// add headers
			$headers[] = 'Expect:';			
			
			// new req
			$req = new HTTP_Request2();
			
			if ( is_array($headers) ) {
				foreach ( $headers as $h) {
					$req->setHeader($h);
				}
			}
			
			// atuh
			if ( $userinfo ) {
				$req->setAuth($userinfo['u'],$userinfo['p'],HTTP_Request2::AUTH_BASIC);
			}
			
			// params
			if ( $params ) {
				$req->setMethod(HTTP_Request2::METHOD_POST);
				$req->addPostParameter($params);			
			}
			
			// url
			$req->setURL($url);

			// give back
			$resp = $req->send();
			
			return $resp->getBody();
			
		}
		
	
		/* curl */
		public function curl($url,$userinfo=null,$params=null,$headers=null) {
			
			// oauth
			if ( isset($this->oauth) AND $this->oauth !== false AND strpos($url,'twitter.com') !== false AND strpos($url,'search.twitter.com') === false ) {
				
				if ( $params ) {
								
					// nice params
					$p = array();
				
					foreach ( explode('&',$params) as $i ) {
						list($k,$v) = explode('=',$i);
						$p[$k] = urldecode($v);
					}
					
					// params
					$params = $p;
					
				}
				
				
				$_params = $params;				
				
				// parse out the url 
				if ( !$params ) {
					
					// parse
					$parsed = parse_url($url,PHP_URL_QUERY);
					
					// xplode
					if ( $parsed ) {
						foreach ( explode('&',$parsed) as $q ) {
							list($k,$v) = explode('=',$q);
							$_params[$k] = $v;
						}
					}
					
				}				
			
				return $this->oauth->OAuthRequest($url, $_params, ($params?'POST':'GET'));
			}			
			
			// add headers
			$headers[] = 'Expect:';
		
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
	
		/* encrypt */
		public function encrypt($str) {	
			return $str;
		}
		
		/* decrypt */
		public function decrypt($str) {
			return $str;
		}
	
		// array to set
		public function arrayToSet($array) {

		}
	
	}


?>