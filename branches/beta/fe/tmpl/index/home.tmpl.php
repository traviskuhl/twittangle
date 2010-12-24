
<div class="yui-ge">
	<div class="yui-u first">
		
		<div class="module <?php echo ($path[0]=='columns'?'hide':''); ?>">
			<div class="bd update">
				<?php if ( $this->loged ) { ?>										
				<form class="update" id="update-status" name="<?php echo time(); ?>">
				<input type="hidden" id="update-status-reply">
				<input type="hidden" id="update-status-network">											
				
					<fieldset>
						<div id="update-status-title">What are you doing?</div>
						<div class="act">
							<a class="update-status-dm bubble" title="Send Direct Message" href='#dm'>DM</a> |
							<a class="update-status-upload bubble" title="Upload a Photo" href="#upload">Upload Photo</a> | 
							<a class="update-status-tiny bubble" title="Make a Tiny Link" href="#tiny">Tiny Link</a>
						</div>
						<div id="update-status-cur" class='current'></div>
						<textarea onfocus="this.className='focus';$d.addClass('update-status','focused');" onblur="this.className='';" id="update-status-txt"></textarea>
						<a class="update-status-btn" href="#">Update</a>
						<div id="update-status-chars" max="140">140</div>						
					</fieldset>																
				</form>
				
				<div id="update-status-o-photo">
					<form id="update-status-photo-frm" enctype="multipart/form-data" method="post" action="/xhr/upload" target="postback" onsubmit="TT.Global.uploadPhotoStart(this);">
						<input type="hidden" name="req" value="<?php echo $this->req; ?>">
						<input id="update-status-photo-file" type="file" name="file"> <button type="submit">Upload</button>
						<div>JPEG, GIF or PNG less than 4mb. Powered by <a href='http://twitpic.com'>TwitPic</a></div>
					</form>
				</div>				
				<div id="update-status-o-tiny">
					<form id="update-status-tiny-frm">
						<input type="text" id="update-status-tiny-txt"> <button class="update-status-tiny-btn">Tiny!</button>
						<div>One URL per line. Powered by <a href='http://tinyurl.com'>TinyURL</a></div>
					</form>
				</div>						
				
				<script type="text/javascript">
					TT.addToQueue(function(){
					
						// check for status box
						$e.on('update-status-txt','keydown',function(){			
							
							// len
							var len = $('update-status-txt').value.length;
							
							var max = parseInt($('update-status-chars').getAttribute('max'),10);
							
							// what up 
							$('update-status-chars').innerHTML = max - len;
							
						});
						$e.on('update-status-txt','keyup',function(){
							
							// len
							var len = $('update-status-txt').value.length;
							
							var max = parseInt($('update-status-chars').getAttribute('max'),10);
							
							// what up 
							$('update-status-chars').innerHTML = max - len;
							
						});					
				
					});
				</script>
				
				<?php } ?>				
			</div>
		</div>
		
		<h1><?php echo $title; ?>
		<?php
			if ( isset($this->rssLink) ) {
				echo "<a class='rss ignore' href='{$this->rssLink}'><img class='ignore' src='http://ms-cdn.com/static/tangle/images/v3/rss.png'></a>";
			}
		?>
		</h1>
			
				<?php
					if ( $path[1] == 'columns' ) {
						echo "<div id='timeline-loading' class='timeline-loading'><em>Loading Columns</em>";
						echo "<div> </div></div>";					
					}
					else if ( !$loadnow ) {
						echo "<div id='timeline-loading' class='timeline-loading'><em>Loading Your Timeline</em>";
						echo "<div>We're loading your timeline from Twitter. Because we have to connect to Twitter";
						echo " this may take up to 60 seconds. We promise we'll make this as quick as possible </div></div>";					
					}
				?>
							
				<ul class='timeline <?php echo (($path[1]=='dm' OR $path[1]=='dmsent')?'dm':''); ?>' id='timeline'>
				<?php
					foreach ( $timeline as $t ) {
						echo $this->_bit_displayStatus($t);
					}
					
					if ( count($timeline) == 0 AND $loadnow ) {
						echo "<li class='empty center gray'><br><br><br> <b>This timeline is empty</b><br> You should probably try something different</li>";
					}
					
				?>
				</ul>
				
				<?php
				
					if ( isset($pager) AND $pager->_totalPages > 0 ) {
						echo "<ul class='clear cf pager'><li class='h'>Pages:</li>";
						for ($i=1;$i <= $pager->_totalPages; $i++) {
							echo "<li  class='".($this->param('page',1)==$i?'b on':'')."'><a href='/home/{$pageUrl}?page={$i}'>{$i}</a></li>";
						}
						echo "</ul>";
					}					
				?>

	</div>
	<div class="yui-u side">
	
		<?php
			if ( count($groups) == 0 ) {
				echo "
					<div class='module'>
						<div class='bd'>
							It looks like you have not created any groups. You should give that a try!.
							<div><a class='b' href='".$this->url('my-groups')."'>Go To My Groups</a></div>
						</div>
					</div>
					<br>
				";
			}
		?>
		
		<div class="module">
			<div class="bd">
				
				<div class="user-card cf">
					<img src="<?php echo $this->getUserPic( $this->user['pic'] ); ?>">
					<h2>
						Hello
						<em><?php echo $this->user['info']['name']; ?></em>
					</h2>
				</div>
				
				<div id='side-menu'>
				<ul  class="side-menu">
					<li class="side-menu-top first <?php echo ( ($path[0]=='timeline' OR $path[0] =='columns')?'open':''); ?>">
						<h4><a class='side-menu-expand' href='#'>My Timeline</a></h4>
						<ul>
							<?php
								foreach ( $menu_timeline as $k => $t ) {
									echo "<li class='".($path[1]==$k?'on':'')."' id='link-".md5("/home/timeline/{$k}")."'><a href='/home/timeline/{$k}'>{$t[0]}</a></li>";
								}
							?>
						</ul>					
					</li>
					<li class="side-menu-top open <?php echo ( ($path[0]=='my-groups' OR $path[0] =='columns')?'open':''); ?>">
						<h4><a class='side-menu-expand' href='#'>My Groups</a></h4>
						<ul>
							<?php
								if ( is_array($groups) ) {
									foreach ( $groups as $g ) {										
									
										// echo 
										echo "
											<li class='".($path[1]==$g['id']?'on':'')."' id='link-".md5("/home/my-groups/{$g['id']}")."'>
												<a href='/home/my-groups/{$g['id']}'>{$g['name']}</a>
												<div id='group-count-{$g['id']}' class='box hide'>
													<span>0</span>
													<b class='tl'></b>
													<b class='tr'></b>
													<b class='bl'></b>
													<b class='br'></b>
												</div>
											</li>
										";	
									}
								}	
							?>
						</ul>
					</li>				
					<li class="side-menu-top <?php echo ( ($path[0]=='my-networks' OR $path[0] =='columns')?'open':''); ?>">
						<h4><a class='side-menu-expand' href='#'>Networks</a></h4>
						<ul>
							<?php
								foreach ( $networks as $n ) {
									echo "<li class='".($path[1]==$n['id']?'on':'')."' id='link-".md5("/home/my-networks/{$n['id']}")."'><a href='/home/my-networks/{$n['id']}'>{$n['title']}</a></li>";
								}
							?>
						</ul>					
					</li>
					<li class="side-menu-top <?php echo ( ($path[0]=='my-searches' OR $path[0] =='columns')?'open':''); ?>">
						<h4><a class='side-menu-expand' href='#'>Favorite Groups</a></h4>
						<ul>
							<?php
								foreach ( $this->user['favGroups'] as $s ) {
									echo "<li class='".($path[1]==$s['id']?'on':'')."' id='link-".md5("/home/fav-group/{$s['id']}")."'><a href='/home/fav-group/{$s['id']}'>{$s['name']}</a></li>";
								}
							?>
						</ul>					
					</li>	
					<li class="side-menu-top <?php echo ( ($path[0]=='my-searches' OR $path[0] =='columns')?'open':''); ?>">
						<h4><a class='side-menu-expand' href='#'>Saved Searches</a></h4>
						<ul>
							<?php
								foreach ( $this->savedSearches as $k => $s ) {
									echo "<li class='".($path[1]==$k?'on':'')."' id='link-".md5("/home/my-searches/{$k}")."'><a href='/home/my-searches/{$k}'>".htmlentities($s['q'],ENT_QUOTES,'utf-8')."</a></li>";
								}
							?>
						</ul>					
					</li>		
					<li class="side-menu-top last <?php echo ( ($path[0]=='messages' OR $path[0] =='columns')?'open':''); ?>">
						<h4><a class='side-menu-expand' href='#'>Messages</a></h4>
						<ul>
							<?php
								foreach ( $menu_messages as $k => $m ) {
									echo "<li class='".($path[1]==$k?'on':'')."' id='link-".md5("/home/messages/{$k}")."'><a href='/home/messages/{$k}'>{$m[0]}</a></li>";
								}
							?>
						</ul>
					</li>							
				</ul>
				</div>				
			
			</div>
		</div>
		
		
		<div id='trending' class='hide module'></div>		
		
	</div>
</div>

<script type="text/javascript">

	
	TT.data.columnSettings = <?php echo ($this->user['cols']?$this->user['cols']:'{"columns":{"length":0}}'); ?>;
	
	<?php if ( $path[0] == 'columns' ) { ?>
		
		// get
		TT.addToQueue(function(){		
			YAHOO.util.Get.script("<?php echo $this->asset('js','columns','1.0'); ?>",{
				'onSuccess': function() {
				
					// coluns 
					TT.data.columns = new $Y.twitTangle.Columns({});
					
					// load
					TT.data.columns.setup();
					
				}
			});			
		});
		
	<?php } else if ( !$loadnow ) {  ?>
	
		TT.addToQueue(function(){
			TT.Global.loadXhrPage({'href':"<?php echo URI . 'home/'. $pageUrl; ?>"},false,true);
		});				
		
	<?php } else { ?>
	
		<?php if ( $this->user['bits'] != '1' ) { ?>
				
			TT.addToQueue(function(){	

				// notify
				if ( YAHOO.util.Cookie.get('notify') != 'true' ) {
					
					// load it 
					var msgpanel = new $Y.twitTangle.Panel({
						'name': 'msgpanel',
						'overlay': true,
						'noOpen': true
					});		
					
					msgpanel.load({
						'url': TT.Global.xhrUrl('notify',{'.rand': Math.random()}),
						'open': true			
					});
					
					// once 
					YAHOO.util.Cookie.set("notify", "true", {'path':'/'});
					
				}
					
			});
	
		<?php } ?>
	
		// get
		TT.addToQueue(function(){
			YAHOO.util.Get.script("<?php echo $this->asset('js','columns','1.0'); ?>",{
				'onSuccess': function() {
					TT.data.columns = new $Y.twitTangle.Columns({});
				}
			});	
		});
	
		// load timeline 
		TT.data.timeline = <?php echo $this->json($timeline); ?>;
	
		// load
		TT.addToQueue(function(){
		
			// open and load 
			TT.Global.loadAndOpen("trending",{'ttl':2},'trending');	
	
		});
	
		// timeline 	
		<?php if ( count($timeline) > 0 AND $reload ) { ?>
			
			// load live
			TT.addToQueue(function(){
				TT.Global.startTimelineWatch({
					'type': "<?php echo $path[0]; ?>",
					'id': "<?php echo $path[1]; ?>",
					'max': "<?php $max = array_slice($timeline,0,1); echo $max[0]['id']; ?>"
				});
			});
			
			// stop whenwe exit
			TT.addToUnLoadQueue(function(){
				TT.Global.endTimelineWatch();
			});
			
		<?php } } ?>
		
</script>