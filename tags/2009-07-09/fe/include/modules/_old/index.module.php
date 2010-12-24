<?php

// index
class index extends Tangle {

	/* constuct */
	public function __construct() {
	
		// parent
		parent::__construct();
	
	}

	/* main */
	protected function main() {
		
		// call
		$this->bodyClass = 'index full';
	
		// loged
		if ( $this->loged ) {
			$this->go($this->url('home'));
		}
		
		/* */
		include( $this->tmpl('index/index') ); 
		
	}
	
	/* home */
	protected function home() {
		
		// body class
		$this->bodyClass = 'home';
		$this->title = "What Your Friends Are Doing";
		
		$this->validate();
		
		// check for rss key
		if ( !$this->user['rss'] ) {
			
			$key = md5(time()*rand(9,9999));
		
			// set it 
			$this->query("UPDATE users SET `rss` = '??' WHERE `id` = '??' ",array($key,$this->uid));
		
			// set it 
			$this->user['rss'] = $key;
		
		}
		
		// rss 
		$this->rssLink = $this->rss($this->user['rss']);
		
		// tmpl
		include( $this->tmpl('index/home') );
		
	}

	/* search */
	protected function search() {
	
		// title
		$this->title = "Search";
		$this->bodyClass = 'search';
		
		// include
		include( $this->tmpl('index/search') );
	
	}
	
	/* terms */
	protected function terms() {
	
		// body class
		$this->bodyClass = 'full terms';
		$this->title = ' Privacy &amp; Terms of Service ';
		
		// inclide
		include( $this->tmpl('index/terms') );
	
	}
	
	// beta 
	protected function iambeta() {
		setcookie("beta",true,time()+(60*60*24*365),'/',DOMAIN);
		$this->go('/home');
	}
	
	// upload
	protected function upload() {
	
		// figure it out 
		// make sure it's  the right type of file 
		$f = $_FILES['file'];
	
		// need 
		if ( !$f ) {
			unlink($f['tmp_name']);
			exit("<html><head><script type='text/javascript'> parent.alert('You did not select a file'); </script></head></html>");
		}

		// need 
		if ( $f['type'] != 'image/jpeg' AND $f['type'] != 'image/gif' AND $f['type'] != 'image/png' ) {
			unlink($f['tmp_name']);		
			exit("<html><head><script type='text/javascript'> parent.alert('Not an Image File'); </script></head></html>");
		}
			
		// params
		$params = array("username"=>$this->user['u'],"password"=>$this->user['p'],"media"=>"@".$f['tmp_name']);
		
		// send it 
		$ch = curl_init("http://twitpic.com/api/upload");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$output = curl_exec($ch);      				
		curl_close($ch);
	
		// figure out 	
		$xml = simplexml_load_string($output);
		
		// unlink
		@unlink($f['tmp_name']);		
		
		if ( $xml['stat'] == 'ok' ) {
			$url = (string)$xml->mediaurl;
			exit("<html><head><script type='text/javascript'> parent.document.getElementById('update-txt').value += ' {$url}'; parent.document.getElementById('pic-upload').className = 'box'; </script></head></html>");
		}
		else {
			exit("<html><head><script type='text/javascript'> parent.alert(".(string)$xml->err['msg']."); </script></head></html>");
		}		
	
	}

}

?>