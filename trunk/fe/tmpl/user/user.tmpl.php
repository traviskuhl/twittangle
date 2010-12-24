<?php

	if ( $user['protected'] ) {
	
		
		$this->extraHead = '<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">';
	
		// set prodile 
		$this->customProfile = false;
		
		echo "
			<h1>User Account Protected</h1>
			<div class='module'>
				<div class='bd center' style='padding: 100px'>
					<div class='b'>{$user['name']}'s account on Twitter is Protected</div>
					<div>Because this user has selected to keep their Twitter account protected, we do not show it on twitTangle. To view their profile, visit twitter directly at: <a class='ext-link-catch' href='http://twitter.com/{$user['screen_name']}'>http://twitter.com/{$user['screen_name']}</a></div>
				</div>	
			</div>
		";
	
	}	
	else {

?>

<div class="yui-gc">
	<div class="yui-u first">
	
		<h1>
			<?php echo "<img id='up|{$user['id']}|{$user['screen_name']}' class='user-panel' src='{$user['profile_image_url']}' align='left' width='48' height='48'>"; ?>
			<?php echo $user['name'] ?>
			<div class="small gray"><?php echo $user['screen_name']; ?></div>
		</h1>			
	
<div style="position: absolute; top: 0; right: 0; width:300px; text-align:right">
<script type="text/javascript"><!--
google_ad_client = "pub-8118251418695668";
/* 234x60, created 3/1/10 */
google_ad_slot = "9773786699";
google_ad_width = 234;
google_ad_height = 60;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>	
</div>
		
		<?php
		
			if (isset($error))  {
				echo "<div class='red'>{$error}</div>";
			}
			
			if ( !$timeline AND $user["protected"] == 'true' ) {
				echo "			
					<br><br>	
					<div class='center gray'>
						<em class='b'>This Account Is Protected</em><br>
						We were unable to retreive this users timeline <br>
						as their account is protected
					</div>	
				";		
			}
			else {
				echo '<ul class="timeline timeline-no-pic" id="timeline">';
				
				$raw = array();
					foreach ( $timeline as $t ) {
						echo $this->_bit_displayStatus($t,false,false);
						$raw[$t['id']] = $t;
					}
				echo '</ul>';
			}	
		?>
				

	</div>
	<div class="yui-u side">
		<?php include( $this->tmpl('user/side') ); ?>
	</div>
</div>

<script type="text/javascript">
	// load timeline 
	TT.data.timeline = <?php echo $this->json($raw); ?>;
</script>


<?php } ?>

