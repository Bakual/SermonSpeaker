<?php
defined('_JEXEC') or die;
JHTML::stylesheet('com_sermonspeaker/sermonspeaker.css', '', true);
JHTML::addIncludePath(JPATH_COMPONENT.'/helpers');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
$user		= JFactory::getUser();
$fu_enable	= $this->params->get('fu_enable');
$canEdit	= ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn	= ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<div class="category-list<?php echo $this->pageclass_sfx;?> ss-series-container<?php echo $this->pageclass_sfx; ?>">
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
		<div class="clr"></div>
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
	<?php else : ?>
		<table class="category table table-striped table-hover table-condensed">
		<!-- Create the headers with sorting links -->
			<thead><tr>
				<?php if ($this->av) : ?>
					<th width='10'> </th>
				<?php endif; ?>
				<th class="ss-title">
					<?php echo JHTML::_('grid.sort', 'JGLOBAL_TITLE', 'series_title', $listDirn, $listOrder); ?>
				</th>
				<?php if (in_array('series:description', $this->col_serie)): ?>
					<th class="ss-col ss-series_desc">
						<?php echo JHTML::_('grid.sort', 'JGLOBAL_DESCRIPTION', 'series_description', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('series:speaker', $this->col_serie)) : ?>
					<th class="ss-col ss-speakers"><?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS'); ?></th>
				<?php endif;
				if (in_array('series:hits', $this->col_serie)) : ?>
					<th class="ss-col ss-hits">
						<?php echo JHTML::_('grid.sort', 'JGLOBAL_HITS', 'hits', $listDirn, $listOrder); ?>
					</th>
				<?php endif;
				if (in_array('series:download', $this->col_serie)) : ?>
					<th></th>
				<?php endif; ?>
			</tr></thead>
		<!-- Begin Data -->
			<tbody>
				<?php foreach($this->items as $i => $item) : ?>
					<tr class="<?php echo ($i % 2) ? "odd" : "even"; ?>">
						<?php if ($this->av) :
							if ($item->avatar) : ?>
								<td class="ss-col ss-avatar"><a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->slug)); ?>"><img src="<?php echo SermonspeakerHelperSermonspeaker::makelink($item->avatar); ?>"></a></td>
							<?php else : ?>
								<td class="ss-col ss-avatar"></td>
							<?php endif;
						endif; ?>
						<td class="ss-title">
							<a title='<?php echo JText::_('COM_SERMONSPEAKER_SERIESLINK_HOOVER'); ?>' href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->slug)); ?>">
								<?php echo $item->series_title; ?>
							</a>
							<?php if ($canEdit || ($canEditOwn && ($user->id == $item->created_by))) : ?>
								<ul class="actions">
									<li class="edit-icon">
										<?php echo JHtml::_('icon.edit', $item, $this->params, array('type' => 'serie')); ?>
									</li>
								</ul>
							<?php endif; ?>
						</td>
						<?php if (in_array('series:description', $this->col_serie)): ?>
							<td class="ss-col ss-series_desc"><?php echo JHTML::_('content.prepare', $item->series_description); ?></td>
						<?php endif;
						if (in_array('series:speaker', $this->col_serie)) : ?>
							<td class="ss-col ss-speakers"><?php echo $item->speakers; ?></td>
						<?php endif;
						if (in_array('series:hits', $this->col_serie)) : ?>
							<td class="ss-col ss-hits"><?php echo $item->hits; ?></td>
						<?php endif;
						if (in_array('series:download', $this->col_serie)) : ?>
							<td class="ss-col ss-dl"><a href="<?php echo JRoute::_('index.php?view=serie&layout=download&tmpl=component&id='.$item->slug); ?>" class="modal" rel="{handler:'iframe',size:{x:400,y:200}}" title="<?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADSERIES_DESC'); ?>">
								<img src="media/com_sermonspeaker/images/download.png" alt="<?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADSERIES_LABEL'); ?>" />
							</a></td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif;
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