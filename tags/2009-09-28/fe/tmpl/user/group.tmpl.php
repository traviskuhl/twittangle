

<div class="yui-gc">
	<div class="yui-u first">
	
		<h1>
			<?php echo "<a href='".$this->url('user',$user)."'>".$user['name'] . '</a> / ' . $g['name'] ?>
			<div class='gray small'><?php echo (array_key_exists('desc',$g['settings'])?$g['settings']['desc']:''); ?></div>
		</h1>
		
		<?php if ( $this->loged ) { ?>
		<div class='fav-group'>
			<a href='#' class='do-fav-group' id='g|<?php echo $g['id']; ?>|<?php echo $this->md5($this->uid.$g['id']); ?>'><?php echo (isset($this->user['favGroups'][$g['id']])?'My Favorite':'Favorite'); ?></a>
		</div>
		<?php } ?>

		<ul class="timeline" id="timeline">
			<?php
				foreach ( $timeline as $t ) {
					echo $this->_bit_displayStatus($t,false,false);
				}
			?>
		</ul>
				
	</div>
	<div class="yui-u side">
		<h3 class='in-group'>In This Group</h3>
			<ul class='mini-users in-group' id='user-in-groups'>
				<?php
				
					// get users
					$users = $this->getGroupUsers($g['id']);

					// get users firends
					$friends = $this->getCloudCache($user['id'],'tt-friends',true);
				
					$i = 0;
				
					foreach ( $users as $u ) {
						if ( array_key_exists($u['friend_id'],$friends) ) {
					
							// $
							$usr = $friends[$u['friend_id']];
						
							// mini
							$mini = $this->getMiniPic($usr['img']);
						
							// echo 
							echo "<li class='".($i>29?'hide':'')."'><a href='/user/{$usr['sn']}'><img title='{$usr['name']} ({$usr['sn']})' class='defer bubble' src='".BLANK."' style='background-image: url($mini)'></a></li>";
							
							$i++;
						}
					}				
				
					// more
					if ( $i > 30 ) {
						echo "<li class='more'><a class='unhide' id='uh|user-in-groups' href='#'>more</a></li>";
					}
					
				?>
			</ul>
		
		<?php include( $this->tmpl('user/side') ); ?>
	
	</div>
</div>