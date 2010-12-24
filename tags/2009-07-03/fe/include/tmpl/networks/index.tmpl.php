<div class="module"><div class="bd">

<div class="yui-gc">
	<div class="yui-u first">

		<h2>Networks <?php echo ($id!=0?" &#187; {$info['name']} ":""); ?></h2>
		
		<?php if ( $id == 0 ) { ?>
		<ul class="categories cf">
			<?php
			
				foreach ( $categories as $cat ) {
					echo "<li>";
					echo "<a href='".$this->url('network-cat',$cat)."'>{$cat['name']}</a>";
					
					// networks
					if ( count($cat['networks']) ) {
						echo "<div>"; $l = array();
							foreach ( $cat['networks'] as $n ) {
								$l[] = "<a href='".$this->url('network',$n)	."'>{$n['title']}</a>";
							}
							echo implode(', ',$l);
						echo "</div>";
					}
			
					echo "</li>";
				}
			?>
		</ul>
		
		<br>
		<br>
		<h2>Featured</h2>
		
		<?php } ?>
		
		<ul class="networks">
			<?php
				if ($networks) {
					foreach ( $networks as $n ) {
						echo "
							<li class='cf'>						
								<h4><a href='/networks/network/{$n['slug']}'>{$n['title']}</a></h4>
								<div class='gray small'>started by <span class='b'>{$n['user']}</span> ".$this->displayTimestamp($n['timestp'])."</div>
								<div>{$n['info']}</div>
						";
							if ( !array_key_exists($n['id'],$this->myNetworks) AND $this->loged ) {
								echo "<a class='join' href='/networks/network/{$n['slug']}/join'>Join this Network</a>";
							}
							
						echo "
							</li>
						";
					}
				}
			?>
		</ul>		
	
	</div>
	<div class="yui-u">
		<div class="box no-box-padd">

			<?php if ( $this->loged ) { ?>
			<ul class="side-menu">
				<li class="side-menu-top open on">
					<a href="#"><em>&#187;</em> Your Networks</a>
					<ul>
						<?php
							
							$networks = $this->getUserNetworks();
						
							foreach ( $networks as $n ) {
								echo "<li><a href='".$this->url('network',$n)."'>{$n['title']}</a></li>";
							}
						?>
					</ul>										
				</li>
				<li>
					<a href="/networks/create"><em>&#187;</em> Create a Network</a>				
				</li>
			</ul>		
			<?php } ?>

			
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
			
			<b class="tl"></b>
			<b class="tr"></b>
			<b class="bl"></b>
			<b class="br"></b>
		</div>	
	</div>
</div>

</div></div>
