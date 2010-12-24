<h1><?php echo $user['name']; ?></h1>

<div class="yui-gc">
	<div class="yui-u first">
		<ul class="timeline">
			<?php
				foreach ( $timeline as $tweet ) {
					echo $tweet->display();
				}
			?>
		</ul>	
	</div>
	<div class="yui-u">
	
	</div>
</div>