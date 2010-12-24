<?php

	define("IN_MOBILE",true);
	define("MOBILE_URI","http://m.twittangle.com/");

	class TangleMobile extends Tangle {
		
		/* construct */
		public function __construct() {
			
			/* construct */
			parent::__construct('mobile');
			
			// global template
			$this->globalTmpl = 'mobile/m.page';
			$this->defaultPage = 'm_main';
			
			// path
			$path = $this->param('path','main');
			
			// explode
			$this->path = explode('/',trim($path,'/'));
			
			// do it
			$_REQUEST['act'] = 'm_'.$this->path[0];						
					
		}
		
		/* main */	
		public function m_main() {	
	
		
			// loged
			if ( $this->loged ) {
				$this->go(MOBILE_URI.'home');
			}
		
			// index
			include($this->tmpl('mobile/m.index'));
			
		}	
		
		public function m_logout() {
		
			// valudate
			$this->validate();
			
			// delete session stuff
			setcookie("TT","",time()+1,'/',DOMAIN);
			setcookie("L","",time()+1,'/',DOMAIN);
			setcookie("TA","",time()+1,'/',DOMAIN);							
		
			// no stid
			$this->query("UPDATE `users` SET `sid` = '0', `password` = '', `password2` = '' WHERE `id` = '??' ",array($this->uid));
			
			$this->go('index');		
		
		}
		
		public function m_home() {
		
			// validate
			$this->validate();

			// index
			include($this->tmpl('mobile/m.home'));
		
		}
		
		public function m_groups() {
			
			// validate
			$this->validate();			
			
			// just list out groups.
			// no template needed
			$groups = $this->getGroups();
			
			// start
			echo "
				<strong>Groups</strong>
				<ol class='groups'>
			";
			
				// list
				foreach  ( $groups as $g) {
					echo "<li><a href='/group/{$g['id']}'>{$g['name']}</a> ({$g['count']} friends)</li>";
				}
			
			// end
			echo "
				</ol>
			";
						
			
		}
		
		public function m_group() {
		
			// validate
			$this->validate();		
		
			// what group
			$id = $this->path[1];
		
			// is a good group
			$groups = $this->getGroups();
		
				// nope
				if ( !array_key_exists($id,$groups) ) {
					$this->go('index');
				}
		
			$group = $groups[$id];
		
			// all good. show header
			echo "
				<strong>{$group['name']}</strong> 
				<a class='back' href='/groups'>Back To Groups</a>
			";
			
			// page
			$page = $this->param('page',1);
		
			// get timeline 
			list($h,$r) = $this->getTweets('yesterday',$page,null,20,true,$id);		
				
			// print 
			echo $h;
		
		}
		
	}
	
?>