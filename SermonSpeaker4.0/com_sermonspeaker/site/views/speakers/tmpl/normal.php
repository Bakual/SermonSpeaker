<?php
defined('_JEXEC') or die;
JHtml::stylesheet('com_sermonspeaker/sermonspeaker.css', '', true);
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
$user		= JFactory::getUser();
$fu_enable	= $this->params->get('fu_enable');
$canEdit	= ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn	= ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<div class="category-list<?php echo $this->pageclass_sfx;?> ss-speakers-container<?php echo $this->pageclass_sfx; ?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif;
if ($this->params->get('show_category_title', 1) or $this->params->get('page_subheading')) : ?>
	<h2>
		<?php echo $this->escape($this->params->get('page_subheading'));
		if ($this->params->get('show_category_title')) : ?>
			<span class="subheading-category"><?php echo $this->category->title;?></span>
		<?php endif; ?>
	</h2>
<?php endif;
if ($this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
	<div class="category-desc">
		<?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
			<img src="<?php echo $this->category->getParams()->get('image'); ?>"/>
		<?php endif;
		if ($this->params->get('show_description') && $this->category->description) :
			echo JHtml::_('content.prepare', $this->category->description);
		endif; ?>
		<div class="clearfix"></div>
	</div>
<?php endif; ?>
<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" id="adminForm" name="adminForm">
	<?php if ($this->params->get('show_pagination_limit')) : ?>
	<div class="display-limit">
		<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>
	<?php endif;
	if (empty($this->items)) : ?>
		<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SPEAKERS')); ?></div>
	<?php else :
		foreach($this->items as $item) : ?>
			<h3><a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug)); ?>"><?php echo $item->name; ?></a></h3>
			<?php if ($canEdit || ($canEditOwn && ($user->id == $item->created_by))) : ?>
				<ul class="actions">
					<li class="edit-icon">
						<?php echo JHtml::_('icon.edit', $item, $this->params, array('type' => 'speaker')); ?>
					</li>
				</ul>
			<?php endif; ?>
			<div class="ss-speaker-text">
				<div class="ss-pic">
					<?php if ($item->pic) : ?>
						<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug)); ?>">
							<img src="<?php echo SermonspeakerHelperSermonSpeaker::makelink($item->pic); ?>" title="<?php echo $item->name; ?>" alt="<?php echo $item->name; ?>" />
						</a>
					<?php endif; ?>
				</div>
				<?php if(in_array('speakers:intro', $this->col_speaker) && $item->intro) :
					echo JHtml::_('content.prepare', $item->intro);
				endif;
				if(in_array('speakers:bio', $this->col_speaker) && $item->bio) :
					echo JHtml::_('content.prepare', $item->bio);
				endif; ?>
				<div class="clear-left"></div>
				<?php if ($item->series): ?>
					<a title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS_SERIESLINK_HOOVER'); ?>" href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug).'&layout=series'); ?>">
						<?php echo JText::_('COM_SERMONSPEAKER_SERIES'); ?></a>
					 | 
				<?php endif;
				if ($item->sermons): ?>
					<a title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS_SERMONSLINK_HOOVER'); ?>" href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug).'&layout=sermons'); ?>">
						<?php echo JText::_('COM_SERMONSPEAKER_SERMONS'); ?></a>
				<?php endif;
				if ($item->website && $item->website != 'http://') :
					if ($item->sermons): ?>
					 | 
					<?php endif; ?>
					<a href="<?php echo $item->website; ?>" target="_blank" title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER_WEBLINK_HOOVER'); ?>">
						<?php echo JText::sprintf('COM_SERMONSPEAKER_SPEAKER_WEBLINK', $item->name); ?></a>
				<?php endif; ?>
			</div>
			<hr />
		<?php endforeach;
	endif;
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
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
</form>
<?php if (!empty($this->children[$this->category->id]) and $this->maxLevel != 0) : ?>
	<div class="cat-children">
		<h3>
			<?php echo JTEXT::_('JGLOBAL_SUBCATEGORIES'); ?>
		</h3>
		<?php echo $this->loadTemplate('children'); ?>
	</div>
<?php endif; ?>
</div>