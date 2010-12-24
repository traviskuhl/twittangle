<?php

	// get a category map 
	$sth = $this->query("SELECT * FROM network_cats ORDER BY name ");

	// nice
	$top = array();
	$child = array();
	
	// do it 
	while ( $row = $sth->fetch_assoc() ) {
		if ( $row['parent_id'] ) {
			$child[$row['parent_id']][] = $row;
		}
		else {
			$top[] = $row;
		}
	}
	
	$cats = array( 'top' => $top, 'child' => $child );

?>

	<h1>Create a Network</h1>
	
	<form method="post" action="/networks/create">
	<input type="hidden" name="do" value="submit">
	<input type="hidden" name="req" value="<?php echo $this->req; ?>">
	<div class='yui-gc'>
		<div class="yui-u first">
			<div class="module">
				<div class="bd">
					<?php
						if ( isset($error) ) {
							echo "<div class='red b'>{$error}</div>";
						}
					?>
				
					<ul class="form">
						<li>
							<label>
								<em>Title</em>
								<input type="text" name="title" style>
							</label>
						</li>
						<li>
							<label>
								<em>Description</em>
								<textarea name="info"></textarea>
							</label>
						</li>
						<li>
							<label>
								<em>Network URL</em>
								http://networks.twitTangle.com/ <input type="text" name="slug" style="width:100px">
								<div class='gray small'>Limit 20 characters. Letters and Numbers only</div>
							</label>
						</li>				
						<li>
							<br>
							<div class="box cf">						
								<h4>Categories</h4>
								<ul class="categories">
									<?php
										foreach ( $cats['top'] as $c ) {
											if ( $c['id'] != 0 ) {
												echo "<li> <label class='b'><input type='checkbox' name='cats' value='{$c['id']}'> {$c['name']}</label></li>";
											}
										}
									?>
								</ul>			
							
								<b class="tl"></b>
								<b class="tr"></b>
								<b class="bl"></b>
								<b class="br"></b>
							</div>
						</li>
						<li class="cf">
							<input type="submit" value="Create">
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="yui-u">	
			<div class="module"><div class="bd gray">
				You can only create 5 networks, so make every one count. Right now all networks are public, 
				but private networks are coming. Also please try to desribe your network, it's purpose 
				and who should join in the 'description' section. This will help people figure out what Networks they want to join.
			</div></div>
		</div>
	</div>
	</form>
	
</div>