<?php 
// no direct access
defined('_JEXEC') or die; 
if ($params->get('tooltip')) :
	JHTML::_('behavior.tooltip');
endif;
$level = 1;
?>
<ul class="sermonspeaker<?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $item) : 
	if($item->level > $level): ?>
		<ul>
	<?php elseif ($item->level < $level):
		while ($item->level < $level--): ?>
			</ul>
		<?php endwhile; ?>
	<?php endif;
	$level = $item->level;
	$link = JRoute::_($baseURL.$item->slug.'&Itemid='.$itemid); ?>
	<li>
		<?php if ($params->get('tooltip')) :
			$options	= array('title' => $item->title, 'href' => $link, 'text' => $item->title);
			$tip	= $item->tooltip;
			if ($item->pic):
				$pic = $item->pic;
				if (strpos($pic, 'http://') !== 0):
					$pic = JURI::root().trim($pic, ' /');
				endif;
				$tip	= '<img src="'.$pic.'" alt="" style="float:right; margin-left:15px;max-width:150px;"> '.$tip;
			endif;
			echo JHTML::tooltip($tip, $options); 
		else: ?>
			<a href="<?php echo $link ?>">
				<?php echo $item->title; ?>
			</a>
		<?php endif; ?>
	</li>
<?php endforeach;
while ($level-- > 1): ?>
	</ul>
<?php endwhile; ?>
</ul>
