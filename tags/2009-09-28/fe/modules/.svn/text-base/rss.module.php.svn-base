<?php

class rss extends Tangle {

	// cahcing
	public $caching = true;
	public $ns = "http://rss.twittangle.com/ns";

	public function __construct() {
	
		// arent
		parent::__construct();	
		
		// path
		$path = explode('/',trim($this->param('path'),'/'));

		$user = $_SERVER['PHP_AUTH_USER'];
		$pass = $_SERVER['PHP_AUTH_PW'];
		
		// check for username and password
		if ( !$user AND !$pass ) {

			header("WWW-Authenticate: Basic realm=\"Enter your Twitter username and password to access your RSS feed.\"");
			header("HTTP/1.0 401 Unauthorized"); 		
			
		}
		
		// ttl
		$ttl = (60*5);
		
		// set user and password
		$this->user = array(
			'u' => $user,
			'p' => $pass
		);
		
		// verify the account
		$resp = $this->twitter('account/verify_credentials');		
		
			// bad response
			if ( !$resp ) {
				$his->error("Invalid Twitter Login");
			}
			
		// good
		$this->user = array(
			'id' => $resp['id'],
			'last_id' => 0
		);		
		
		// header
		header("Content-Type: application/xml");		
		
		// cid
		$cid = md5("rss:{$user}:{$path[0]}:{$path[1]}");
		
			// check for cache 
			if ( $this->caching AND ($cache = $this->getCache($cid,$ttl,'rss')) !== false ) {	
				exit($cache."\n<!-- {$cid} -->");			
			}			
		
		// get some data
		if ($path[0] == 'timeline') {

			// timeline
			$menu_timeline = array(
				'last-visit'	=> array( 'Since Last Visit', 'last' ),
				'two-hours'		=> array( 'Two Hours Ago', 'two' ),
				'eight-hours'	=> array( 'Eight Hours Ago', 'eight' ),
				'yesterday'		=> array( 'Yesterday', 'yesterday' )
			);		
			
				// is ogod
				if ( !array_key_exists($path[1],$menu_timeline) ) {
					$this->error("Invalid Request");
				}
						
			// get them 
			list($h,$timeline,$pager) = $this->getTimeline(array(
					'since' => $menu_timeline[$path[1]][1],
					'page' => $this->param('page')
				));					
		
			// title
			$title = " My Timeline / {$menu_timeline[$path[1]][0]} ";
			$link = "home/timeline/{$path[1]}";
		
		}		
		else if  ( $path[0] == 'group' ) {
		
			// get groups
			$groups = $this->getGroups();
			
			// goo group
			if ( !array_key_exists($path[1],$groups) ) {
				$this->error("Group does not exists");
			}

			// get them 
			list($h,$timeline,$pager) = $this->getTimeline(array(
					'groups' => $path[1],
					'page' => $this->param('page')
				));

			// title
			$title = "Group '" . $groups[$path[1]]['name']."'";
			
			// link
			$link = "home/my-groups/{$path[1]}";

		}	
		else {
			$this->error("Invalid Request");
		}
		
		// dom
		$dom = new DOMDocument('1.0', 'utf-8');	
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;	
	
			// add some stuff
			$root = $dom->createElement("rss");
			$root->setAttributeNode(new DOMAttr("version","2.0"));
	
			// channel
			$ch = $dom->createElement('channel');
			
				// add
				$root->appendChild($ch);
				
			// add other
			$ch->appendChild( new DOMElement("title","twitTangle / {$title}") );
			$ch->appendChild( new DOMElement("link",URI.$link) );
			$ch->appendChild( new DOMElement("pubDate",date('r')) );
			$ch->appendChild( new DOMElement("generator","twitTangle.com") );
			$ch->appendChild( new DOMElement("ttl", $ttl ) );			
			
		// foreach item
		foreach ( $timeline as $t ) {	
			
			// item
			$item = $dom->createElement('item');
			
				// stuff
				$item->appendChild( new DOMElement("title", $t['user']['name'] . " / " . $t['text'] ) );
				$item->appendChild( new DOMElement("link", $this->url('user-status',array('screen_name'=>$t['user']['screen_name'],'id'=>(string)$t['id'])) ) );
				$item->appendChild( new DOMElement("description", $t['text'] ) );				
				$item->appendChild( new DOMElement("pubDate", $t['created_at'] ) );
				
				// add some info using namespace
				foreach ( array('in_reply_to_screen_name','in_reply_to_user_id','source') as $i ){
					
					// create
					$el = $dom->createElementNS($this->ns,"tt:{$i}",$t[$i]);
				
					// append
					$item->appendChild( $el );
				
				}
				
			
			// append
			$ch->appendChild($item);
		
		}		
			
		// done
		$dom->appendChild($root);
		
		// save
		$xml = $dom->saveXML();
		
		// save it
		$this->saveCache($cid,$xml,$ttl,'rss');
	
		// done
		exit($xml);		
	
	}

	public function error($msg) {
		
		// header
		header("Content-Type: application/xml",true,500);
		exit('<?xml version="1.0" encoding="utf-8"?><error>'.htmlentities($msg,ENT_QUOTES,'utf-8').'</error>');
		
	}
	
}

?>