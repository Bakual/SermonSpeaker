<?php
defined('_JEXEC') or die('Restricted access');
JHtml::core();
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<div id="ss-speakers-container" >
<h1 class="componentheading"><?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS_TITLE'); ?></h1>
<?php if (empty($this->items)) : ?>
	<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
<?php else : 
// Begin Data ?>
<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" id="adminForm" name="adminForm">
	<fieldset class="filters">
	<legend class="hidelabeltxt"><?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?></legend>
		<div class="display-limit">
			<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
	</fieldset>

<?php foreach($this->items as $item) : ?>
	<h3 class="contentheading">
		<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug)); ?>" title="<?php echo $item->name; ?>">
			<?php echo $item->name; ?>
		</a>
	</h3>
		<?php if($item->pic) : ?>
			<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug)); ?>">
				<img class="speaker" src="<?php echo $item->pic; ?>" title="$item->name; ?>" alt="<?php echo $item->name; ?>" />
			</a>
		<?php endif;
		if($this->params->get('speaker_intro') && $item->intro) : ?>
			<p><?php echo $item->intro; ?></p>
		<?php endif; ?>
		<?php if($item->pic) : ?>
			<br style="clear:both" />
		<?php endif; ?>
		<a title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS_SERIESLINK_HOOVER'); ?>" href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug)); ?>">
			<?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS_SERIESLINK'); ?>
		</a>
		 | 
		<a title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS_SERMONSLINK_HOOVER'); ?>" href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug)).'&layout=latest-sermons'; ?>">
			<?php echo JText::_('COM_SERMONSPEAKER_SERMONS'); ?>
		</a>
		<?php if ($item->website && $item->website != 'http://') : ?>
			 | <a href="<?php echo $item->website; ?>" target="_blank" title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_WEBLINK_HOOVER'); ?>">
				<?php echo JText::sprintf('COM_SERMONSPEAKER_SPEAKER_WEBLINK', $item->name); ?></a>
		<?php endif; ?>
	<hr style="width: 100%; height: 2px;" />
<?php endforeach; ?>
<div class="Pages">
	<div class="Paginator">
		<?php echo $this->pagination->getListFooter(); ?><br />
	</div>
</div>
</form>
<?php endif; ?>
</div>