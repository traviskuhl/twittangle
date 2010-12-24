<h1><?php echo $this->title; ?></h1>

<div class="yui-ge">
	<div class="yui-u first">
		<div class="module">
			<div class="bd">
			
				<?php  if ( isset($id) ) { ?>
					
	                <ul class="networks">
	                        <?php
                                foreach ( $networks as $n ) {
                                    echo "
                                            <li class='cf'>                                                                                            
                                                    <p>
	                                                    <a href='".$this->url('network',$n)."'>{$n['title']}</a>
                                                    	{$n['info']}
                                                    </p>
                                                    <div class='stats'>
                                                    	<b>".number_format($n['members'])." members /
                                                    	".number_format($n['posts'])." posts</b>
															<div class='gray small'>started by <span class='b'>{$n['user']}</span> ".date("m/d/Y",$n['timestp'])."</div>                                                    	
                                                    </div>
                                            </li>
                                    ";
                                }

	                        ?>
	                </ul> 	
	                
					<?php
					
						if ( $id == 'all' ) {
							echo "<ul class='clear cf pager'>";
							if ( $page != 1 ) {
								echo "<li><a href='".$this->url('net-cat',array('id'=>'all'),array('page'=>($page-1)))."'>Previous</a></li>";
							}
							if ( count($networks) == 30 ) {
								echo "<li><a href='".$this->url('net-cat',array('id'=>'all'),array('page'=>($page+1)))."'>Next</a></li>";
							}
							echo "</ul>";
						}					
					?>		                			
				
				<?php } else { ?>
				
					<ul class="top-list cf">
						<li class="box">	
							<h2>Popular</h2>
							<ul>
								<?php
									foreach ( $popular as $p ) {
										echo "
											<li>
												<a href='".$this->url('network',$p)."'>{$p['title']}</a> 
												<span>
													".number_format($p['members'])." members -  
													".number_format($p['posts'])." posts
												</span>
											</li>
										";
									}
								?>
							</ul>
							<b class="tl"></b>
							<b class="tr"></b>
							<b class="bl"></b>
							<b class="br"></b>												
						</li>
						<li class="box">	
							<h2>New</h2>
							<ul>
								<?php
									foreach ( $newest as $p ) {
										echo "
											<li>
												<a href='".$this->url('network',$p)."'>{$p['title']}</a> 
												<span>
													created ".$this->displayTimestamp($p['timestp'])."
												</span>
											</li>
										";
									}
								?>
							</ul>					
							<b class="tl"></b>
							<b class="tr"></b>
							<b class="bl"></b>
							<b class="br"></b>												
						</li>					
						<li class="box">	
							<h2>Featured</h2>
							<ul>
								<?php
									foreach ( $featured as $p ) {
										echo "
											<li>
												<a href='".$this->url('network',$p)."'>{$p['title']}</a> 
												<span>
													".number_format($p['members'])." members -  
													".number_format($p['posts'])." posts
												</span>
											</li>
										";
									}
								?>
							</ul>					
							<b class="tl"></b>
							<b class="tr"></b>
							<b class="bl"></b>
							<b class="br"></b>												
						</li>						
					</ul>
					
				<?php } ?>

			</div>
		</div>
		
		<h3>Recent Network Posts</h3>
		<ul class="timeline network-timeline">
			<?php
				foreach ( $recent as $r ) {
					echo $this->_bit_displayNetworkPost($r);
				}
			?>
		</ul>
		
	</div>
	<div class="yui-u">
		<div class="module side-menu">
			<ul class="side-menu">
			
			<?php if ( $this->loged ) { ?>
				<li class="side-menu-top open on first">
					<h4><a class='side-menu-expand' href="#">Your Networks</a></h4>
					<ul>
						<?php
							
							$networks = $this->getUserNetworks();
						
							foreach ( $networks as $n ) {
								echo "<li><a href='".$this->url('network',$n)."'>{$n['title']}</a></li>";
							}
						?>
						<li><a href='<?php echo $this->url('network-create'); ?>'>Create a Network</a></li>
					</ul>										
				</li>	
			<?php } ?>
		
				<li class="side-menu-top open on <?php echo ( $this->loged ? 'last' : 'first' );?>">
					<h4><a class='side-menu-expand' href="#">Categories</a></h4>				
					<ul>
						<?php
						
							foreach ( $categories as $cat ) {
								echo "<li class='".($this->pathParam(1)==$cat['id'] ? 'on' : '' )."'>";
								echo "<a href='".$this->url('network-cat',$cat)."'>{$cat['name']}</a>";													
								echo "</li>";
							}
						?>
					</ul>		
				</li>
			</ul>
		</div>
			
			<?php
				if ( !$this->loged ) {
					echo '
						<br>
						<div class="module"><div class="bd">
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
						</div></div></div>
					';
				}
			?>		
			
	</div>
</div>
