<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
$Itemid	= JRequest::getInt('Itemid');
?>
<h1 class="componentheading"><?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS_TITLE'); ?></h1>
<?php if ($this->rows){ ?>
<!-- begin Data -->
<form action="http://<?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ?>" method="post" id="adminForm" name="adminForm">
<?php foreach($this->rows as $row) { ?>
	<h3 class="contentheading">
		<?php echo '<a href="'.JRoute::_('index.php?view=speaker&id='.$row->id).'" title="'.$row->name.'">'.$row->name.'</a>'; ?>
	</h3>
	<div class="ss-speaker-container" >
		<?php if($row->pic) { ?>
			<a href="<?php echo JRoute::_('index.php?view=speaker&id='.$row->id); ?>">
				<img class="speaker" src="<?php echo $row->pic; ?>" title="$row->name; ?>" alt="<?php echo $row->name; ?>" />
			</a>
		<?php }
		if($this->params->get('speaker_intro') && $row->intro) {
			echo '<p>'.$row->intro.'</p>';
		} ?>
		<?php if($row->pic) { ?>
			<br style="clear:both" />
		<?php } ?>
		<a title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS_SERIESLINK_HOOVER'); ?>" href="<?php echo JRoute::_('index.php?view=speaker&id='.$row->id); ?>">
			<?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS_SERIESLINK'); ?>
		</a>
		 | 
		<a title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS_SERMONSLINK_HOOVER'); ?>" href="<?php echo JRoute::_('index.php?view=speaker&layout=latest-sermons&id='.$row->id); ?>">
			<?php echo JText::_('COM_SERMONSPEAKER_SERMONS'); ?>
		</a>
		<?php if ($row->website && $row->website != "http://"){
			echo ' | <a href="'.$row->website.'" target="_blank" title="'.JText::_('COM_SERMONSPEAKER_SPEAKER_WEBLINK_HOOVER').'">'.JText::sprintf('COM_SERMONSPEAKER_SPEAKER_WEBLINK', $row->name).'</a>';
		} ?>
	</div>
	<hr style="width: 100%; height: 2px;" />
<?php } ?>
<div class="Pages">
	<div class="Paginator">
		<?php echo $this->pagination->getListFooter(); ?><br />
	</div>
</div>
</form>
<?php } else { ?>
	<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SPEAKERS')); ?></div>
<?php } ?>