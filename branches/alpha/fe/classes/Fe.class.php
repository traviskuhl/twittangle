<?php

class Fe extends Framework {
 
 	public function __construct() {
 	
		// parent
		parent::__construct();
 
		// pages
		Config::set('pages',array(
			
			// index
			'index' => '/',
			'login' => 'index.php?module=index;act=login;do=auth',
			
			// user
			'user' => 'profile/{user}',
			
			// list
			'list' => 'list/{sn}/{slug}',
			
			// home
			'home-my'		=> 'home',
			'home-mentions'	=> 'home/mentions',
			'home-fav'		=> 'home/fav',
			'home-list'		=> 'home/list/{sn}/{slug}',
		
		));
	
	}

	// twitter
	public function twitter($endpoint,$params=array(),$method='GET') {
	
		// url
		$url = "http://api.twitter.com/1/{$endpoint}.json";
		
		// get form oaith
		$data = $this->oauth->OAuthRequest($url, $params, $method);		
		
		error_log($url);
		
		// resp
		$resp = json_decode($data,true);
	
		// what resp
		if ( !$resp OR array_key_exists('error',$resp) )  {		
			return false;
		}
	
		// good
		return $resp;
	
	}
	
	// get user
	public function getUser($id=false) {
		
		// no user, assume we want the logged in one
		if ( !$id ) {
			$id = $this->uid;
		}
		
		// ask twitter
		$data = $this->twitter("users/show/{$id}");
	
		// give back
		return $data;
	
	}


	// get user lists
	public function getUserLists($user=false) {
		
		// no user
		if ( !$user ) {
			$user = $this->user['user'];
		}
		
		// use oauth to make the request
		$lists = $this->twitter("{$user}/lists");

		// user	
		foreach ( $lists['lists'] as $i => $row ) {
			$lists['lists'][$i]['sn'] = $row['user']['screen_name']; 
		}

		// list
		return $lists['lists'];
	
	}

	// user follow lists
	public function getUserFollowLists($user=false) {
	
		// no user
		if ( !$user ) {
			$user = $this->user['user'];
		}
		
		// use oauth to make the request
		$lists = $this->twitter("{$user}/lists/subscriptions");
	
		// user	
		foreach ( $lists['lists'] as $i => $row ) {
			$lists['lists'][$i]['sn'] = $row['user']['screen_name']; 
		}	
	
		// list
		return $lists['lists'];	
	
	}

	// saved searches
	public function getUserSearches($user=false) {
	
		// no user
		if ( !$user ) {
			$user = $this->user['user'];
		}
		
		// use oauth to make the request
		$resp = $this->twitter("saved_searches");
	
		// list
		return $resp;	
	
	}

	public function getUserTimeline($args=array()) {
		
		// defaults
		$user = p('user',$this->user['user'],$args);
		$type = p('type',false,$args);
		$page = p('page',1,$args);
		
		// end
		$end = false;
		
		// params
		$params = array('page'=>$page);
		
		// swich based on tyoe
		switch($type) {
			
			// user
			case 'user':
				$end = "statuses/user_timeline/{$args['user']}"; break;
			
			// list
			case 'list':
				$end = "{$args['user']}/lists/{$args['id']}/statuses"; break;
			
			// mentions
			case 'mentions':
				$end = "statuses/mentions?id={$args['user']}"; break;
			
			// fav
			case 'fav':
				$end = "favorites"; $params['id'] = $args['user']; break;				
			
			// retweets of me
			case 'retweets-me':
				$end = "statuses/retweets_of_me"; break;
			
			// default
			default:
				$end = "statuses/home_timeline";
			
		};
		
		$resp = $this->twitter($end,$params);	
	
		// give back
		return new Tweets($resp);
	
	}
	
	public function getUserPic($sn,$tag=true,$size='n') {
		self::userPic($sn,$tag,$size);
	}
	
	public static function userPic($sn,$tag=true,$size='n') {
		if ( $tag ) {
			return "<img class='user-pic' src='".BLANK."' style='background-image: url(http://img.tweetimag.es/i/{$sn}_{$size})'>";
		}
		else {
			return "http://img.tweetimag.es/i/{$sn}_{$size}";
		}	
	}
	
	public static function thumbUrl($url,$size='50x50') {
	
		list($w,$h) = explode('x',$size);
	
		return URI."image.php?url=".rawurlencode($url)."&size=$size";
	}
	

	public function expandUrls($urls) {
	
		// mh			
		$mh = curl_multi_init();
		
		// url
		$curl = array();
		$map = array();			

		// go for it 
		foreach ( $urls as $url ) {
		
			$id = md5($url);
			
			// curl
			$curl[$id] = curl_init($url);
		
			// set opn
			curl_setopt($curl[$id], CURLOPT_HEADER, 1);
			curl_setopt($curl[$id], CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl[$id], CURLOPT_NOBODY, 1);
			curl_setopt($curl[$id], CURLOPT_TIMEOUT, 5);
		
			// add
			curl_multi_add_handle($mh, $curl[$id]);
					
		}
		
		// not running			
		$running = null;
		
		// start
		do {
			curl_multi_exec($mh, $running);
		} while($running > 0);
		
		// links
		$final = array();
		
		// loop
		foreach ( $curl as $id => $c ) {
		
			// $output 
			$output = curl_multi_getcontent($c);
			
			// remove
			curl_multi_remove_handle($mh, $c);
		
			// match
			if ( preg_match("/Location:([^\n]+)/",$output,$m) ) {
				$final[$id] = $m[1];
			}			
		
		}
		
		// end;
		curl_multi_close($mh);

		
		// give back
		return $final;
	
	}	
	
}


class Tweets implements Iterator {
	
	private $position = 0;
	private $raw = array();
	
	private $links = array();
	private $mentions = array();
	
	public function __construct($raw) {

		// links
		$links = array();
		
		// no
		if ( !$raw ) {
			return;
		}
		
		// flip through and get all inks
		foreach ( $raw as $r ) {
		
			// add to raw
			$t = new Tweet($r);
			
			// links
			$this->links = array_merge($this->links,$t->getLinks());
			$this->mentions = array_merge($this->mentions,$t->getMentions());
			
			// set
			$this->raw[] = $t;
			
 		}
		
	}

	public function getImages() {
		
		// domains
		$domains = Config::get('imageDomains');
		
		// images
		$images = array();
		
		// loop through images and check
		// for link types we know
		foreach ( $this->links as $id => $link ) {
			foreach ( $domains as $d ) {
				if ( preg_match($d,$link,$match) ) {
								
					// based on d 					
					$images[] = array(
						'id' => 'image-'.$id,
						'url' => $link,
						'html' => "<img class='hide image-{$id}' src=".BLANK." >",
						'expand' => true,
						'm' => array($match[1],$match[2])
					);
				}		
			}
		}
	
		// give images
		return $images;
	
	}
	
	public function getMentions() {
	
	
	
		// return unique metions
		$o =  array_count_values($this->mentions);
		
		// return
		$return = array();
		
		foreach ( $o as $u => $n ) {
			$return[] = array(
				'user' => $u,
				'num' => $n,
				'html' => "<a class='bubble user-{$u}' title='{$u}' href='' style='background-image: url(".FE::userPic($u,false,'m')."'>{$u}</a>"
			);
		}
		
		return $return;
		
	}
	
	public function getLinks() {
	
		// domains
		$domains = Config::get('expandDomains');
		
		// images
		$expand = array();
		
		// loop through images and check
		// for link types we know
		foreach ( $this->links as $id => $link ) {
			foreach ( $domains as $d ) {
				if ( preg_match($d,$link,$match) ) {
				
					$short = str_replace(array('http://www.','http://'),'',$link);
					
						if ( mb_strlen($short) > 20 ) {
							$short = mb_substr($short,0,20)."...";
						}				
					
					$expand[] = array(
						'id' => $id,
						'url' => $link,
						'shart' => $short,
						'html' => "<a class='{$id}' href='{$link}'>{$short}</a>"
					);
				}		
			}
		}
	
		// give images
		return $expand;	
	
	}

    function rewind() {
        $this->position = 0;
    }

    function current() {
        return $this->raw[$this->position];
    }

    function key() {
        return $this->position;
    }

    function next() {
        ++$this->position;
    }

    function valid() {
        return isset($this->raw[$this->position]);
    }

}

class Tweet {

	private $raw = array();
	public $links = array();
	public $metions = array();

	public function __construct($raw) {
	
		// raw
		$this->raw = $raw;
		
		// parse it
		$this->parse();
		
	}

	public function __get($name) {
		if ( array_key_exists($name,$this->raw) ) {
			return $this->raw[$name];
		}
	}

	public function getLinks() {
		return $this->links;
	}
	
	public function getMentions() {
		return $this->metions;
	}

	public function parse() {
		
		// simple parse
		list($text,$links,$mentions) = self::parseTweet($this->text,$this->id);
		
		// set links
		$this->links = $links;
		$this->metions = $mentions;
		
		// parsed
		$this->parsed = $text;
		
	}
	
	public static function parseTweet($txt,$id) {
		
		// strip tags from the stat 
		$txt = strip_tags( html_entity_decode($txt,ENT_QUOTES) );

		// add entities back
		$txt = htmlentities($txt,ENT_QUOTES,"utf-8");
		
		// special
		$links = array();
		$mentions = array();
			
		// pick up urls
		$hasLinks = preg_match_all("/\b(http\:\/\/[^\s]+)\b/",$txt,$matches,PREG_PATTERN_ORDER);
		
			// go through each link and p	ing
			// to it
			if ( $hasLinks ) {
				foreach ( $matches[1] as $lnk ) {
										
					// give it an id
					$_id = 'link-'.md5($lnk);
					
					// links
					$links[$_id] = $lnk;
			
					// text
					$txt = str_replace($lnk,"<a class='{$_id}' target='_blank' href='$lnk'>$lnk</a>",$txt); 					
					
				}
			}
		
		// match all metions
		$hasMentions = preg_match_all("/(@[a-zA-Z0-9\_]+)\b/i",$txt,$matches,PREG_PATTERN_ORDER);
		
			// go through each link and p	ing
			// to it
			if ( $hasMentions ) {
				foreach ( $matches[1] as $lnk ) {
										
					// links
					$usr = str_replace('@','',$lnk);
					
					// user
					$mentions[] = $usr;
			
					// text
					$txt = str_replace($lnk,"<a class='user-overlay' href='".Config::url('user',array('user'=>$usr))."'>$lnk</a>",$txt); 					
					
				}
			}	
		
		// hashes
		$txt = preg_replace("/ \#([a-zA-Z0-9]+)/"," <a class='search-overlay' href='http://www.twittangle.com/search?q=%23$1'>#$1</a>",$txt);
	
		// return links
		return array($txt,$links,$mentions);
	
	}

	public function display() {
		$html = "
			<li id='tweet-{$this->id}' class='tweet'>
				<div class='hd'><img class='user-pic' src='".BLANK."' style='background-image: url(http://img.tweetimag.es/i/{$this->user['screen_name']}_n)'></div>
				<div class='bd'><div class='text'><a class='user-overlay user' href='".Config::url('user',array('user'=>$this->user['screen_name']))."'>{$this->user['screen_name']}</a> ".$this->parsed."</div></div>
				<div class='ft'>".$this->_footer()."</div>
			</li>
		";
		return $html;		
	}

	private function _footer() {
	
		// ago
		$html = ago(strtotime($this->created_at));
		
		// html
		if ( $this->in_reply_to_status_id ) {
			$html .= " <a href=''>in reply to {$this->in_reply_to_screen_name}</a>";
		}
		
		// give back
		return $html;
		
	}

}

?>