<strong>What your friends are doing</strong>

<table align="center" class="menu">
<tr>
	<?php
	
		// since
		$since = $this->param('since','last');
	
		$pages = array(
			'last' => 'Last Time',			
			'two' => 'Two Hours Ago',
			'eight' => 'Eight Hours Ago',
			'yesterday' => 'Yesterday'
		);
		
		foreach ( $pages as $key => $page ) {
			echo "<td class='".($since==$key?'on':'')."'><a href='/home?since={$key}'>{$page}</a></td>";
		}
		
	?>
</tr>
</table>

<?php

	$page = $this->param('page','1');
	$tag = $this->param('tag');

	// get them 
	list($h,$r) = $this->getTweets($since,$page,$tag,20,true,null);

	echo $h;

?>

<br/>
<strong>Filter by Tag</strong>
<div class='tags'>
<?php				
	$tags = $this->getPopularTags(20); 
	foreach ( $tags as $tag => $t ) {
		echo " <a href='/home?since={$since};tag={$tag}'>{$tag}</a> ";
	}
?>
</div>