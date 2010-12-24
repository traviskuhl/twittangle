<div class="module"><div class="bd">
<div class="yui-gc">
	<div class="yui-u first">
	
		<h2><?php echo $info['title'] ?></h2>	
	
		<ul class="timeline" id="timeline">
			<?php
				foreach ( $timeline as $t ) {
					echo $this->_bit_displayStatus($t);		
				}		
			?>
		</ul>	
	</div>
	<div class="yui-u side">
		<div class="box no-box-padd">
		
			<?php
				if ( $this->loged AND !array_key_exists($info['id'],$this->myNetworks) ) {
					echo "
						<ul class='side-menu'>
							<li><a class='join' href='/networks/network/{$info['slug']}/join'>Join this Network</a></li>
						</ul>
					";
				}
			?>
		
			<div class="padd10">
				
				<h3>Network Information</h3>
				<ul class="list">
					<li><em class="b">Description:</em> <?php echo $info['info']; ?></li>
					<?php if ( $info['url'] ) { ?>
						<li><em class="b">URL:</em> <?php echo "<a target='new' href='{$info['url']}'>".substr($info['url'],0,20)."</a>"; ?></li>
					<?php } ?>			
					<li>
						<em class="b">Categories:</em>
						<?php
							$sth = $this->query("SELECT name, id FROM network_cats WHERE FIND_IN_SET(id,'??') ",array($info['cats']));
							$list = array();
							while ( $row = $sth->fetch_assoc()) {
								$list[] = "<a href='".$this->url('net-cat',$row)."'>{$row['name']}</a>";
							}
							echo implode(", ",$list);
						?>	
					</li>				
				</ul>
		
				<br><br>			
				<h3>Members</h3>
				<ul class="mini-users">
					<?php
					
						$members = $this->getNetworkMembers($info['id']);
					
						foreach ( $members as $m ) {
			
							// mini
							$sn = $m['user'];
							
							// mini
							$mini = $this->getMiniPic($m['pic']);
							
							echo "<li><a href='/user/{$sn}' title='{$sn}'><img class='defer bubble' title='{$sn}' style='background-image: url({$mini});' src='".BLANK."' width='24' height='24'></a></li>";
						}
					
					
					?>
				</ul>		
			</div>		

			<?php
				if ( !$this->loged ) {
					echo '
						<br>
						<div align="center">
						<script type="text/javascript"><!--
						google_ad_client = "pub-8118251418695668";
						/* 120x240, created 3/1/09 */
						google_ad_slot = "6826186970";
						google_ad_width = 120;
						google_ad_height = 240;
						//-->
						</script>
						<script type="text/javascript"
						src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
						</script>
						</div>					
					';
				}
			?>
		
			<b class="tr"></b>
			<b class="tl"></b>
			<b class="br"></b>
			<b class="bl"></b>
		</div>
	</div>
</div>

</div></div>

<script type="text/javascript">
	TT.data.timeline = <?php echo $this->json($timeline); ?>;
</script>

<?php $max = (140-strlen("#tt:".$info['id'])); ?>
<script type="text/javascript">
	TT.addToQueue(function(){
		if ( $d.inDocument('update-status') ) {
			$d.addClass('update-status','write sticky');
			$('update-status-title').innerHTML = "Post to <?php echo $info['title']; ?>";
			$('update-status-network').value = "<?php echo $info['id']; ?>"; 
			$('update-status-chars').setAttribute('max',"<?php echo $max; ?>");
			$('update-status-chars').innerHTML = "<?php echo $max; ?>";		
		}
	});
	TT.addToUnLoadQueue(function(){
		if ( $d.inDocument('update-status') ) {
			$('update-status-title').innerHTML = "What are you doing?";
			$('update-status-network').value = "";	
			$('update-status-chars').setAttribute('max',"140");
			$('update-status-chars').innerHTML = "140";
			$d.removeClass('update-status','write sticky');
		}
	});
</script>