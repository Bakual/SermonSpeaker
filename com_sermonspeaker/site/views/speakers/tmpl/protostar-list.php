<?php
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
JHtml::_('bootstrap.tooltip');

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
	if ($this->params->get('show_description', 1) or $this->params->get('show_description_image', 1)) : ?>
		<div class="category-desc">
			<?php if ($this->params->get('show_description_image') and $this->category->getParams()->get('image')) : ?>
				<img src="<?php echo $this->category->getParams()->get('image'); ?>"/>
			<?php endif;
			if ($this->params->get('show_description') and $this->category->description) :
				echo JHtml::_('content.prepare', $this->category->description, '', 'com_sermonspeaker.category');
			endif; ?>
			<div class="clearfix"></div>
		</div>
	<?php endif; ?>
	<div class="cat-items">
		<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" id="adminForm" name="adminForm">
			<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) : ?>
				<div class="filters btn-toolbar">
					<?php if ($this->params->get('show_pagination_limit')) : ?>
						<div class="btn-group pull-right">
							<label class="element-invisible">
								<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
							</label>
							<?php echo $this->pagination->getLimitBox(); ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="clearfix"></div>
			<?php if (!count($this->items)) : ?>
				<div class="no_entries alert alert-error"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SPEAKERS')); ?></div>
			<?php else : ?>
				<ul class="category list-striped list-condensed">
					<?php foreach($this->items as $i => $item) : 
						$sep = 0; ?>
						<li class="<?php echo ($item->state) ? '': 'system-unpublished '; ?>cat-list-row<?php echo $i % 2; ?>">
							<?php if (in_array('speakers:hits', $this->col_speaker)) : ?>
								<span class="ss-hits badge badge-info pull-right">
									<?php echo JText::sprintf('JGLOBAL_HITS_COUNT', $item->hits); ?>
								</span>
							<?php endif;
							if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
								<span class="list-edit pull-left width-50">
									<?php echo JHtml::_('icon.edit', $item, $this->params, array('type' => 'speaker')); ?>
								</span>
							<?php endif; ?>
							<strong class="ss-title">
								<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug)); ?>">
									<?php echo $item->name; ?>
								</a>
							</strong>
							<?php if (!$item->state) : ?>
								<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
							<?php endif; ?>
							<br />
							<?php if ($item->sermons) :
								$sep = 1; ?>
								<small class="ss-sermons">
									<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug).'#sermons'); ?>">
										<?php echo JText::_('COM_SERMONSPEAKER_SERMONS'); ?></a>
								</small>
							<?php endif;
							if ($item->series) :
								if ($sep) : ?>
									|
								<?php endif;
								$sep = 1; ?>
								<small class="ss-series">
									<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug).'#series'); ?>">
										<?php echo JText::_('COM_SERMONSPEAKER_SERIES'); ?></a>
								</small>
							<?php endif;
							if ($item->website) :
								if ($sep) : ?>
									|
								<?php endif;
								$sep = 1; ?>
								<small class="ss-website">
									<a href="<?php echo $item->website; ?>">
										<?php echo JText::_('COM_SERMONSPEAKER_FIELD_WEBSITE_LABEL'); ?></a>
								</small>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif;
			if ($this->params->get('show_pagination') and ($this->pagination->get('pages.total') > 1)) : ?>
				<div class="pagination">
					<?php if ($this->params->get('show_pagination_results', 1)) : ?>
						<p class="counter pull-right">
							<?php echo $this->pagination->getPagesCounter(); ?>
						</p>
					<?php endif;
					echo $this->pagination->getPagesLinks(); ?>
				</div>
			<?php endif; ?>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<input type="hidden" name="limitstart" value="" />
		</form>
	</div>
	<?php if (!empty($this->children[$this->category->id]) and $this->maxLevel != 0) : ?>
		<div class="cat-children">
			<h3><?php echo JTEXT::_('JGLOBAL_SUBCATEGORIES'); ?></h3>
			<?php echo $this->loadTemplate('children30'); ?>
		</div>
	<?php endif; ?>
</div>