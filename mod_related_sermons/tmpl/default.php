<?php 
// no direct access
defined('_JEXEC') or die; 
?>
<ul class="relateditems<?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $item) : ?>
<li>
	<a href="<?php echo $item->route; ?>">
		<?php if ($showDate) :
			echo JHtml::Date($item->created, JText::_('DATE_FORMAT_LC4')).' - ';
		endif;
		echo $item->title; ?></a>
</li>
<?php endforeach; ?>
</ul>