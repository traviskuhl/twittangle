<?php

	// set iot
//	ini_set("include_path",".:/usr/share/pear");

	// isbeta
	if ( getenv("TT_BETA") == 'true' ) {
		error_reporting(E_ALL);
		ini_set("display_errors",1);
		define("IN_BETA",true);		
	}
	else {
		error_reporting(0);
		ini_set("display_errors",0);	
		define("IN_BETA",false);		
	}

	// magic 
	set_magic_quotes_runtime(0);	


/*
 * Constants 
 */

	// site closed 
	define("SITE_CLOSED", false);
	
	// fn 
	$root = implode('/',array_slice(explode('/',$_SERVER['SCRIPT_FILENAME']),0,-1));
	
	// domain
	define("DOMAIN",'.twittangle.com');
	
	// cache it 
	define("CACHE_ID",md5($_SERVER['HTTP_HOST']));
	
	// urls
	define("ROOT",		"$root/");
	define("MODULE",	ROOT.'include/modules/');
	define("URI", 		'http://'.$_SERVER['HTTP_HOST'].'/');
	define("CONFIG_FILE","/home/twittangle.com/config.php");
	
	// versions
	define("VER_CSS",'3.1');
	define("VER_JS",'3.1');	
	
	// beta
	define("BETA_BUILD","");

	// bank
	define("BLANK","http://assets.ms-cdn.com/static/moviestring/images/v1/blank.gif");

	// set auth stuff
	define("AUTH_KEY", getenv('TT_AUTH_KEY') ); 
	define("AUTH_SECRET", getenv('TT_AUTH_SECRET') ); 

	// include 
	require("/usr/share/pear/Validate.php");				
	require("/usr/share/pear/Pager/Pager.php");	
	require("/usr/share/pear/Pager/Sliding.php");
	include(ROOT."include/classes/OAuth.class.php");	

/*
 *  Autoload
 */

	function __autoload($name) { 
		if ( defined('BETA') AND BETA == true ) {
			if ( file_exists( ROOT . 'include/classes/_beta/' . $name . '.class.php' ) ) {
				include_once( ROOT . 'include/classes/_beta/' . $name . '.class.php' ); 
			}		
			else if ( file_exists( ROOT . 'include/classes/' . $name . '.class.php' ) ) {
				include_once( ROOT . 'include/classes/' . $name . '.class.php' ); 
			}
			else if ( file_exists( ROOT . 'include/modules/_beta/' . $name . '.module.php' ) ) {
				include_once( ROOT . 'include/modules/_beta/' . $name . '.module.php' );
			}				
			else if ( file_exists( ROOT . 'include/modules/' . $name . '.module.php' ) ) {
				include_once( ROOT . 'include/modules/' . $name . '.module.php' );
			}					
		}
		else{		
			if ( file_exists( ROOT . 'include/classes/' . $name . '.class.php' ) ) {
				include_once( ROOT . 'include/classes/' . $name . '.class.php' ); 
			}
			else if ( file_exists( ROOT . 'include/modules/' . $name . '.module.php' ) ) {
				include_once( ROOT . 'include/modules/' . $name . '.module.php' );
			}				
		}
	}



/* config */
class Config  {

	// config 
	public $config = array();
	
	/**
	 * @method	construct
	 */
	function __construct() { 
	
		/* config */
		$this->config = array(
			
			// dir 
			'dir' => array(
				'classes' => ROOT . 'include/',
				'tmpl'	=> ROOT . 'include/tmpl/',	
				'cache' => '/tmp/cache/'.CACHE_ID."/"			
			),
			
			// assets 
			'assets' => array(			
				'js'		=> 'http://'.getenv('TT_ASSETS_URL').'/',
				'css'		=> 'http://'.getenv('TT_ASSETS_URL').'/',
				'images'	=> 'http://static.ms-cdn.com/tangle/images/',				
			),
			
			// urls
			'url' => array(
				'index' 	=> '',
				'home'		=> 'home',
				'login'		=> 'login',
				'logout'	=> 'logout',
				'blog'		=> 'http://blog.twittangle.com/',
				
				'dir' => 'directory',
				'search' => 'search',
				
				'networks'	=> "networks",
				'network-cat' => "networks/category/{id}/{name}",
				'net-cat' => "networks/category/{id}/{name}",				
				'network' => "networks/{slug}",
				'network-post' => "networks/{slug}/post",				
				'network-create' => "networks/create",
				'network-join' => "networks/{slug}/join",				
				
				'friends-import' => "my/friends/import",
				
				'my-groups' => "my/groups",
				'my-friends' => "my/friends",
				
				'user' => 'user/{screen_name}',
				'user-status'	=> 'user/{screen_name}/status/{id}',
			),
			
			
			// md5 sig 
			'md5salt' => 'UH9as&AS(*D&*(S&7y2weijoiA0939)(AS()DU&8ye3029ioa_)A_',
			
			// key
			'key' => '*H(J)OKUHY#jfiue93'
		
		);	
		
		// check for cache folger
		if ( !file_exists($this->config['dir']['cache']) ) {
			mkdir($this->config['dir']['cache'],777);
		}
	
	}
	
	
	public function rss($key,$group=false) {
	
		// base
		$url = "http://rss.twittangle.com/{$key}";
		
		// group
		if ( $group ) {
			$url .= "/group-{$group}";
		}
		
		// give back
		return $url;
		
	}
	
	public function asset($type,$file,$version=false) {
	
		if ( !$version AND $type == 'js' ) {
			$version = VER_JS;
		}
		else if ( !$version ) {
			$version = VER_CSS;
		}
			
		// file
		$f =  ROOT."assets/{$type}/{$file}-$version.{$type}";
		
		// url
		$url = $this->config['assets'][$type] . "{$type}/{$file}/$version?m=".filemtime($f);
	
		// return	
		return $url;
	
	
	}
	
	protected function md5($str) {
		return md5( $this->config['md5salt'] . $str );
	}

	/**
	 * @method	tmpl 
	 */
	protected function tmpl($tmpl) { 
	
		// no tmpl 
		$tmpl = str_replace('.tmpl.php','',$tmpl);
		
		// the file 
		$file = $this->config['dir']['tmpl'] . $tmpl . '.tmpl.php';
		$oFile = $file;
		
		if ( defined('BETA') AND BETA === true AND file_exists($this->config['dir']['tmpl']."/_beta/{$tmpl}.tmpl.php") ) {
			$file = $this->config['dir']['tmpl']."/_beta/{$tmpl}.tmpl.php";
		}
		
		// check for dev
		if ( file_exists( $file . '-dev' ) AND $this->beta == true ) {
			$file = $file . '-dev';
		}

		if ( file_exists( $oFile . '-alpha' ) AND $this->alpha == true ) {
			$file = $oFile . '-alpha';
		}
								
		// return file name 
		return $file;
		
	}	
	
	/**
	 * @method	url 
	 */
	public function url($page,$tokens=false,$params=false,$server=URI) {

		if ( defined('BETA') AND BETA == true ) {
			$server = 'http://beta.twittangle.com/';
		}

		// no page this page by default
		if ( !$page ) $page = $this->page;
		
		// check for module 
		if ( $this->module == 'developer' ) {
			$server = "http://developer.moviestring.com/";
		}
		 
		// url 
		$url;
		
			// module 
			if ( $this->module == 'fb' ) {
				$params['.src'] = 'facebook';
			}			
			
		// uri
		$uri = $this->config['url'][$page];			
			
		// check for facebook
		if ( defined('FACEBOOK') AND FACEBOOK === true AND array_key_exists($page,$this->config['facebook']) ) {
			
			// server 
			$server = "http://apps.new.facebook.com/moviestring/";
		
			// uri 
			$uri = $this->config['facebook'][$page];
			
		}

		// are their tokens
		if ( is_array($tokens) ) {
		
			// replace all the tokens
			foreach ( $tokens as $key => $val ) {
				if ( is_string($val) ) {
					$uri = str_replace('{'.$key.'}',$this->cleanForUrl(strtolower($val)),$uri);					
				}
			}
			
			// remove any lingering 
			$uri = preg_replace("/(\&|\;)[a-zA-Z0-9]+\=\{[a-zA-Z0-9]+\}/","",$uri);
			$uri = preg_replace("/\{[a-zA-Z0-9]+\}\/?/","",$uri);

			// return it
			$url = $this->addParams($uri, $params);

		}
		else {

			// check if we have the var we need
			if ( array_key_exists($page,$this->config['url']) ) {
				$url = $this->addParams($this->config['url'][$page], $params);
			}
			else {
				$url = $this->addParams(URI,$params);
			}

		}
		
		// add uri?
		if ( $server == -1) {
			return $url;
		}
		else if ( strpos($url,'http://') !== false OR strpos($url,'https://') !== false ) {
			return $url;
		}
		else {
			return $server . $url;
		}
		
	}		
	
	// cleanForUrl
	public function cleanForUrl($str) {
		// remove possessive 
		$str = str_replace("'s","s", html_entity_decode($str,ENT_QUOTES) );
		
		$rep = array('/[^a-zA-Z0-9]+/','/\-+/');
		$with = array('-','-');
		$new = preg_replace($rep,$with, $str );
		if ( substr($new,0,1) == '-' ) {
			$new = substr($new,1);
		}
		if ( substr($new,-1) == '-' ) {
			$new = substr($new,0,-1);
		}
		return $new;
	}	

	// addParams
	public function addParams($url,$p) {
		if ( is_array($p) ) {

			// is there already a ?
			if ( strpos($url,'?') === false ) {
				$url .= '?';
			}
			else {
				$url .= '&';
			}

			// arry
			$arry = array();

			// put them in an array
			foreach ( $p as $k => $v ) {
				$arry[] = $k."=".rawurlencode($v);
			}

			// return with query
			return $url . implode('&',$arry);
			
		}
		else {
			return $url;
		}
	}			
		
	
	/**
	 * @method	param 
	 */
	public function param($var,$default=false,$array=null,$filter=null) {
		return self::getParam($var,$default,$array,$filter);
	}

	/** 
	 * @method	getParam 
	 */		 
	static function getParam($var,$default=false,$array=null,$filter=null) {
	
		// fliters 
		$filters = array(
			'alpha'		=> '/[^a-zA-Z]+/',
			'alphanum'	=> '/[^a-zA-Z0-9]+/',
			'num'		=> '/[^0-9]+/',
			'url'		=> '/[^a-zA-Z0-9\:\;\?\&-_\/\.]+/',
			'search'	=> '/[^a-zA-Z0-9\@\# ]/'
		);

	
		// default array 
		if ( !is_array($array) ) {
			$array = $_REQUEST;
		}
		
		// what is it 
		if ( array_key_exists($var,$array) AND $array[$var] != '' ) {
		
			// val 
			$val = $array[$var];
		
			// fliter 
			if ( $filter ) {
				$val = preg_replace($filters[$filter],'',$val);
			}
			
			// giver back
			return $val;
		
		}
		else {
			return $default;
		}
	
	}
	
	/* pathParam */
	public function pathParam($key,$default=false) {		
		if ( count($this->path) > $key AND $this->path[$key] != "" ) {
			return $this->path[$key];				
		}
		else {
			return $default;
		}
	}

	
	/** getPath **/
	public function getPath() {
	
		// get it as a param 
		$path = $this->param('path');	
		
		// return it exploded
		return explode('/',trim($path,'/'));
	
	}	

	/**
	 * @method	mtime 
	 */
	static function mtime() {
		list ($msec, $sec) = explode(' ', microtime());
		$microtime = (float)$msec + (float)$sec;
		return $microtime;
	} 		

}

?>