<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<div class="ss-speakers-container<?php echo htmlspecialchars($this->params->get('pageclass_sfx')); ?>" >
<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif;
if ($this->cat): ?>
	<h2><span class="subheading-category"><?php echo $this->cat; ?></span></h2>
<?php endif;
if (empty($this->items)) : ?>
	<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SPEAKERS')); ?></div>
<?php else : ?>
<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" id="adminForm" name="adminForm">
	<?php if ($this->params->get('show_pagination_limit')) : ?>
	<div class="display-limit">
		<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>
	<?php endif;
	foreach($this->items as $item) : ?>
		<h3><a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug)); ?>"><?php echo $item->name; ?></a></h3>
		<div class="ss-speaker-text">
			<div class="ss-pic">
				<?php if ($item->pic) : ?>
					<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug)); ?>">
						<img src="<?php echo SermonspeakerHelperSermonSpeaker::makelink($item->pic); ?>" title="<?php echo $item->name; ?>" alt="<?php echo $item->name; ?>" />
					</a>
				<?php endif; ?>
			</div>
			<?php if(in_array('speakers:intro', $this->col_speaker) && $item->intro) :
				echo JHTML::_('content.prepare', $item->intro);
			endif;
			if(in_array('speakers:bio', $this->col_speaker) && $item->bio) :
				echo JHTML::_('content.prepare', $item->bio);
			endif; ?>
			<div class="clear-left"></div>
			<a title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS_SERIESLINK_HOOVER'); ?>" href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug)); ?>">
				<?php echo JText::_('COM_SERMONSPEAKER_SERIES'); ?>
			</a>
			 | 
			<a title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS_SERMONSLINK_HOOVER'); ?>" href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug).'&layout=latest-sermons'); ?>">
				<?php echo JText::_('COM_SERMONSPEAKER_SERMONS'); ?>
			</a>
			<?php if ($item->website && $item->website != 'http://') : ?>
				 | <a href="<?php echo $item->website; ?>" target="_blank" title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_WEBLINK_HOOVER'); ?>">
					<?php echo JText::sprintf('COM_SERMONSPEAKER_SPEAKER_WEBLINK', $item->name); ?></a>
			<?php endif; ?>
		</div>
		<hr />
	<?php endforeach;
	if ($this->params->get('show_pagination') && ($this->pagination->get('pages.total') > 1)) : ?>
		<div class="pagination">
			<?php if ($this->params->get('show_pagination_results', 1)) : ?>
				<p class="counter">
					<?php echo $this->pagination->getPagesCounter(); ?>
				</p>
			<?php endif;
			echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php endif; ?>
</form>
<?php endif; ?>
</div>