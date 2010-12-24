<?php
	
	if ( isset($badgroup) ) {

		$this->extraHead = '<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">';
	
		echo "
			<h1>Could not Find Group</h1>
			<div class='module'>
				<div class='bd center' style='padding: 100px'>
					<div class='b'>We couldn't find the group you asked for</div>
					<div>The group you asked for doesn't seem to exist. It's either been deleted or the user has decided to make this group private</div>
				</div>	
			</div>
		";		
	
	
	
	}
	else {
	
		$this->extraHead = '<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">';
	
		echo "
			<h1>User Account Blocked</h1>
			<div class='module'>
				<div class='bd center' style='padding: 100px'>
					<div class='b'>{$id} has blocked their Account</div>
					<div>{$id} has decided to block their account from twitTangle. To view their profile, visit twitter directly at: <a class='ext-link-catch' href='http://twitter.com/{$id}'>http://twitter.com/{$id}</a></div>
				</div>	
			</div>
		";		
	
	
	}
?>