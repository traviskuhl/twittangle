<?php
	
	// sort by name
	$sort = array();
	
	// make key
	foreach ( $groups as $g ) {
		$sort[$g['name']] = $g;
	}

	// sort
	ksort($sort);

	// do it 
	$groups = array();
	
		// go 
		foreach ( $sort as $s ) {
			$groups[$s['id']] = $s;
		}

	// what 
	$on = $this->pathParam(0,$groups[key($groups)]['id']);

	// if it's orphans
	if ( $on == 'orphans' ) {
		
		// holder
		$thisGroupUsers = array();
		
		// loo through each group 
		foreach ( $groups as $g ) {
		
			// get
			$t = $this->getGroupUsers($g['id']);
			
			// merge
			foreach ( $t as $u ) {
				$thisGroupUsers[$u['friend_id']] = $u;
			}
			
		}
	
	}
	else {
	
		// get users
		$thisGroupUsers = $this->getGroupUsers($on);
		
	}
	
	// missing
	$missing = array();

?>

<h1>My Groups</h1>

<div id="groups-wrap" class="hide">
	
	<div class="yui-gb">
		<div class="yui-u first">
			
			<div class="module side-menu">				
			<ul class="side-menu">
				<li class="first side-menu-top open">
				<h4><a class='side-menu-expand' href='#'>Groups</a></h4>
				<ul class="groups-menu">
				<?php
				
					// each
					foreach ( $groups as $g ) {			
						echo "
							<li class='".($g['id']==$on?'on':'')."'>
								<a href='/my/groups/{$g['id']}'>{$g['name']}</a>
								<a class='delete' onclick=\" if ( confirm('Are your sure? YOU CAN NOT UNDO') ) { location.href = '/my/groups/delete?id={$g['id']}'; return; } \" href='javascript:void(0);'>(delete)</a>									
							</li>
						";
					}
					
				?>
				<li class='<?php echo ($on=='orphans'?'on':''); ?>'><a href='/my/groups/orphans'>Orphaned Friends</a></li>				
				<li>
					<form method="post" name="<?php echo time(); ?>" action="/my/groups/create" class="group-create">
						<input type="text" onfocus="this.className = 'on'; $d.setStyle('create-btn','display','inline'); this.value='';" name="name" value="Create a Group">
						<button type="submit" id='create-btn'>Create</button>
					</form>							
				</li>
			</ul></li></ul>
			</div>
	
		</div>	
		<div class="yui-u">		
			<div class="module">
				<div class="bd">				
					<?php 
						
						if ( count($groups) == 0 ) {
							echo "
								<div class='none'>
									<h3>You Don't Have Any Groups</h3>
									<div>You need to create some groups. It's simple! Just pick a name for your group, click the link on the left labeled  'Create a Group' and enter 
									in the text box.</div>
								</div>
							";
						}				
						else if ( $on == 'orphans' ) {
							
							// start
							echo "<ul id='all-groups-list' class='groups big-groups'>";
							
							// i
							$i = 0;
							
							// each
							foreach ( $groups as $g ) {
								
								// get users
								$users = $this->getGroupUsers($g['id']);
								
								// title
								echo "
									<li id='group-{$g['id']}' class='group-top ".($i++==0?'open':'')."'> 
										<div class='wrap-hd'><b class='l'></b><b class='r'></b></div>
										<div class='wrap-bd'> 									
											<div class='cnt'><div class='add-notice'><span></span></div>
												<h3>{$g['name']}</h3>
												<div class='in-group-wrap'>
													<ul class='mini-users cf' id='group-list-{$g['id']}'>
								";
								
									// add it 
									foreach ( $users as $u ) {
															
										if ( array_key_exists($u['friend_id'],$this->allFriends) )	{
																
											// find user in all friends
											$usr = $this->allFriends[$u['friend_id']];						
										
											// mini
											$mini = $this->getMiniPic($usr['img']);
										
											// echo 
											echo "
												<li>
													<img class='defer-pic bubble' title='{$usr['name']} ({$usr['sn']})' src='".BLANK."' style='background-image: url($mini);'>
												</li>
											";
											
										}
										
									}
								
								echo "
													</ul>
												</div>
											</div>
											<b class='cnt'></b>
										</div>
										<div class='wrap-ft'><b class='l'></b><b class='r'></b></div>
									
									
									</li>					
									
								";
							
							} 
							
							echo "</ul>";
												
						}
						else {
					?>
						
						<ul class="groups">
							<li class='group-top' id='group-<?php echo $on; ?>'>
								<div class='add-notice'><span></span></div>
								<ul class="group-list" id='group-list-<?php echo $on; ?>'>						
								<?php
								
									if ( count($thisGroupUsers) == 0 ) {
										echo "<li class='empty'><div class='none'><h3>No Users In This Group</h3>You haven't added any users to this group. To add some: find them in the list on the left labeled 'Add Friends'. Then drag their profile pic right here.</div></li>";
									}
											
									// each 
									foreach ( $thisGroupUsers as $u ) {
										
										// has to exists 
										if ( array_key_exists($u['friend_id'],$this->allFriends) ) {
										
											// find user in all friends
											$usr = $this->allFriends[$u['friend_id']];
											
											$pic = $this->getUserPic($usr['img']);
									
											// show
											echo "
												<li>
													<a id='up|{$u['friend_id']}|{$usr['sn']}' class='user-panel' href='".$this->url('user',array('screen_name'=>$usr['sn']))."'><img class='defer-pic' src='".BLANK."' style='background-image: url({$pic});'></a>
													<a href='/user/{$usr['sn']}'>{$usr['name']}</a>
													<em>{$usr['sn']}</em>
												</li>
											";
											
										}
									}
								
								?>
								</ul>
							</li>
						</ul>
						
					<?php } ?>
				</div>
			</div>		
		</div>
		<div class="yui-u last">
			<div class="module">
				<div class="bd">			
					
					<h3 class="add-friends">Add Friends</h3>
					<p class="add-friends">Below is a list of friends not in this group. Just drag their pic to to the list at right to add them</p>
					
					<?php if ( $on != 'orphans' ) { ?>
					<form class="add-friends" name='<?php echo rand(9,time()); ?>' action='#'>
						<input id='group-search-<?php echo $on; ?>' type='text' value='Search for a Friend' onfocus="this.value = '';">
						<div class='results' id='group-results-<?php echo $on; ?>'></div>
					</form>				
					<?php } ?>
					<div class="add-friends">
						<ul>
						<?php									
							$keys = array_keys($thisGroupUsers);
							foreach ( $this->allFriends as $f ) { 
								if ( !in_array($f['id'],$keys) ) {
									$f['img'] = $this->getUserPic($f['img']);
									echo "
										<li id='usr-{$f['id']}' class='no-group-item'>
											<img class='defer-pic' src='".BLANK."' style='background-image: url({$f['img']});'>
											<a href='/user/{$f['sn']}'>{$f['name']}</a>
											<em>{$f['sn']}</em>										
										</li>
									";
									// missing
									$missing[$f['id']] = $f;
								}
							}
						?>				
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>	
	


<script type="text/javascript">
	TT.addToQueue(function(){
	
		// get
		YAHOO.util.Get.css("<?php echo $this->asset('css','groups','1.3'); ?>",{
			'onSuccess': function() {
				$d.removeClass( $d.getElementsByClassName('defer-pic','img',$('page-content') ), 'defer-pic' );
			}
		});
		
		<?php if ( $on == 'orphans' ) { ?>

			// friends 
			var friends = <?php echo json_encode($missing); ?>;
			var groups = <?php echo json_encode($groups); ?>;
			
			// init the list
			TT.Global.initNonGroupSelector(friends,groups);			
			
			// add hover states
			$e.on('all-groups-list','mouseover',function(e){
			
				// target
				var tar = $e.getTarget(e);
				
				// check 
				var li = $d.isGoodTarget(tar,'group-top',10);
				
					// nope
					if ( !li ) {
						return;
					}
				
				var open = $d.getElementsByClassName('open','li',$('all-groups-list'));

				for ( var el in open ) {
					if ( !$d.hasClass(open[el],'sicky') ) {
						$d.removeClass(open[el],'open');
					}
				}							
				
				// open it
				if ( li && $d.hasClass(li,'group-top') ) {
					$d.addClass(li,'open');
				}
				
			
			});
		
		<?php } else if ( count($groups) != 0 ) { ?>
									
			// friends 
			var friends = <?php echo json_encode($missing); ?>;
			var groups = <?php echo json_encode(array( $on => $groups[$on] )); ?>;
			
			// init the list
			TT.Global.initNonGroupSelector(friends,groups);	
	
			// ds 
			var ds = new YAHOO.util.XHRDataSource( TT.Global.xhrUrl('friendSearch'), {'connMethodPost':true} ); 
				ds.responseType = YAHOO.util.XHRDataSource.TYPE_JSON;								
				ds.responseSchema = { 
					resultsList : 'resp', 
					fields : ['name','id','img','sn'] 
				}; 
			
			// data 
			var ac = new YAHOO.widget.AutoComplete(
				'group-search-<?php echo $on; ?>',
				'group-results-<?php echo $on; ?>', 
				ds,
				{
					'useShadow': true,
					'queryDelay': .5,
					'typeAhead': true,
					'applyLocalFilter': true,
					'queryMatchSubset': true
				});
			
			// when selected
			ac.itemSelectEvent.subscribe(function(type,args) {
				TT.Global.addUserToGroup('<?php echo $on; ?>',args);
			});
			
			// filter
			ac.filterResults = function(q,full,parsed) {
			
				// results
				var r = [];
				
				// leave 
				for ( var i in parsed.results ) {
					if ( !inArray(TT.data.ac[<?php echo $on; ?>],parsed.results[i].id)) {
						r[r.length] = parsed.results[i];
					}
				}
				
				// return
				return {'results':r};
				
			};
			
			// global
			TT.data.ac = {};
			TT.data.ac[<?php echo $on; ?>] = <?php echo json_encode( array_keys($thisGroupUsers) ); ?>;		
			
		<?php } ?>
			
	});
</script>