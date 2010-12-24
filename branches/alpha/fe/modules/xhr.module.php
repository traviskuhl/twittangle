<?php

class xhr extends Fe {

	public function __construct() {
	
		// parent
		parent::__construct();
		
		// act
		$act = p('act');
		
		// goo
		if ( !method_exists($this,$act) ) {
			$this->_bad("Invalid Request");
		}
		
		// call
		call_user_func(array($this,$act));
		
		// exit
		exit();
		
	}

	// timeline
	public function timeline() {
		
		// page
		$page = p('page',1);
		
		// get the timeline 
		$timeline = $this->getUserTimeline(false,$page);
	
		// now we need to get each one
		$html = array();
		
		// each
		foreach ( $timeline as $t ) {
			$html[] = $t->display();
		}
	
		// good
		$this->_good(array(
			'html'=>$html,
			'mentions' => $timeline->getMentions(),
			'links' => $timeline->getLinks(),
			'images' => $timeline->getImages()
		));
	
	}
	
	public function expand() {
	
		// links 
		$payload = json_decode(p_raw('payload'),true);
	
		// do it
		$links = array();
		$match = array();
		
		$l = array();		

		// expand them
		foreach ( $payload as $p ) {
			if ( $p['type'] == 'l' ) {
				$links[] = $p['url'];
				$match[md5($p['url'])] = $p;
			}
			else if ( $p['type'] == 'i' ) {
				
				// flickr
				if ( $p['m'][0] == 'flic' ) {
				
					// add it
					$resp = unserialize($this->curl("http://api.flickr.com/services/rest/?format=php_serial&method=flickr.photos.getSizes&api_key=524e00c8916e0196dc0b36b4a956e054&photo_id=".base_decode($p['m'][1])));
					
					// source
					if ( isset($resp['sizes']['size'][3]['source']) ) {
						
						// url
						$url = $resp['sizes']['size'][3]['source'];
						
						$l[] = array(
							'id' => 'image-link-'.md5($p['url']),
							'url' => $url,
							'html' => "<img src=".Fe::thumbUrl($url,'60x60').">"						
						);
					
					}
					
				}
				else if ( $p['m'][0] == 'yfrog' ) {
				
					// url
					$url = $p['url'].":iphone";
				
					// image
					$l[] = array(
						'id' => 'image-link-'.md5($p['url']),					
						'url' => $url,
						'expand' => false,
						'html' => "<img src=".Fe::thumbUrl($url,'60x60').">"
					);
					
				}
				else if ( $p['m'][0] == 'twitpic' ) {
				
					// url
					$url = "http://twitpic.com/show/thumb/".$p['m'][2];
				
					// image
					$l[] = array(
						'id' => 'image-link-'.md5($p['url']),					
						'url' => $url,
						'expand' => false,
						'html' => "<img src=".Fe::thumbUrl($url,'60x60').">"
					);	
					
				}				
				
				
			}
		}		
			
		// expan
		$expanded = $this->expandUrls($links);

		// go through and replace
		foreach ( $expanded as $id => $link ) {
			
			// match
			if ( $match[$id]['type'] == 'l' ) {
				
				// sjort		
				$short = str_replace(array('http://www.','http://'),'',$link);
				
					if ( mb_strlen($short) > 20 ) {
						$short = mb_substr($short,0,20)."...";
					}			
			
				$l[] = array(
					'id' => 'link-'.$id,
					'full' => $link,
					'shart' => $short,
					'html' => "<a class='{$id} bubble' title='{$link}' href='{$link}'>{$short}</a>"
				);		
				
			}
				
		}
	
		// give back
		$this->_good(array('expand'=>$l));
	
	}
	
	private function _good($resp) {
		exit(json_encode(array('stat'=>1,'resp'=>$resp)));
	}
	
	private function _bad($msg) {
		exit(json_encode(array('stat'=>0,'msg'=>$msg)));
	}
	
	
}

function base_decode($num) {
$alphabet = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
$decoded = 0;
$multi = 1;
while (strlen($num) > 0) {
$digit = $num[strlen($num)-1];
$decoded += $multi * strpos($alphabet, $digit);
$multi = $multi * strlen($alphabet);
$num = substr($num, 0, -1);
}

return $decoded;
}	

?>