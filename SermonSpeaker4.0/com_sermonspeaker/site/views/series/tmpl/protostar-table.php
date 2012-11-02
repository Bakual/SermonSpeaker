<?php
defined('_JEXEC') or die;

JHTML::addIncludePath(JPATH_COMPONENT.'/helpers');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');

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
	if ($this->params->get('show_description', 1) or $this->params->get('show_description_image', 1)) : ?>
		<div class="category-desc">
			<?php if ($this->params->get('show_description_image') and $this->category->getParams()->get('image')) : ?>
				<img src="<?php echo $this->category->getParams()->get('image'); ?>"/>
			<?php endif;
			if ($this->params->get('show_description') and $this->category->description) :
				echo JHtml::_('content.prepare', $this->category->description, '', 'com_sermonspeaker.category');
			endif; ?>
			<div class="clr"></div>
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
				<div class="no_entries alert alert-error"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERIES')); ?></div>
			<?php else : ?>
				<table class="table table-striped table-hover table-condensed">
					<thead><tr>
						<?php if ($this->av) : ?>
							<th class="ss-av hidden-phone hidden-tablet"> </th>
						<?php endif; ?>
						<th class="ss-title">
							<?php echo JHTML::_('grid.sort', 'JGLOBAL_TITLE', 'series_title', $listDirn, $listOrder); ?>
						</th>
						<?php if (in_array('series:category', $this->col_serie)) : ?>
							<th class="ss-col ss-category hidden-phone">
								<?php echo JHTML::_('grid.sort', 'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
							</th>
						<?php endif;
						if (in_array('series:description', $this->col_serie)): ?>
							<th class="ss-col ss-series_desc hidden-phone">
								<?php echo JHTML::_('grid.sort', 'JGLOBAL_DESCRIPTION', 'series_description', $listDirn, $listOrder); ?>
							</th>
						<?php endif;
						if (in_array('series:speaker', $this->col_serie)) : ?>
							<th class="ss-col ss-speakers hidden-phone hidden-tablet"><?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS'); ?></th>
						<?php endif;
						if (in_array('series:hits', $this->col_serie)) : ?>
							<th class="ss-col ss-hits hidden-phone hidden-tablet">
								<?php echo JHTML::_('grid.sort', 'JGLOBAL_HITS', 'hits', $listDirn, $listOrder); ?>
							</th>
						<?php endif;
						if (in_array('series:download', $this->col_serie)) : ?>
							<th class="ss-col ss-dl hidden-phone"></th>
						<?php endif; ?>
					</tr></thead>
				<!-- Begin Data -->
					<tbody>
						<?php foreach($this->items as $i => $item) : ?>
							<tr class="<?php echo ($item->state) ? '': 'system-unpublished '; ?>cat-list-row<?php echo $i % 2; ?>">
								<?php if ($this->av) :
									if ($item->avatar) : ?>
										<td class="ss-col ss-av hidden-phone hidden-tablet"><a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->slug)); ?>"><img class="img-polaroid" src="<?php echo SermonspeakerHelperSermonspeaker::makelink($item->avatar); ?>"></a></td>
									<?php else : ?>
										<td class="ss-col ss-av hidden-phone hidden-tablet"></td>
									<?php endif;
								endif; ?>
								<td class="ss-title">
									<a class="hasTip" title="::<?php echo JText::_('COM_SERMONSPEAKER_SERIESLINK_HOOVER'); ?>" href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->slug)); ?>">
										<?php echo $item->series_title; ?>
									</a>
									<?php if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
										<span class="list-edit pull-left width-50">
											<?php echo JHtml::_('icon.edit', $item, $this->params, array('type' => 'serie')); ?>
										</span>
									<?php endif; ?>
									<?php if (!$item->state) : ?>
										<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
									<?php endif; ?>
								</td>
								<?php if (in_array('series:category', $this->col_serie)) : ?>
									<td class="ss-col ss-category hidden-phone">
										<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSeriesRoute($item->catslug)); ?>"><?php echo $item->category_title; ?></a>
									</td>
								<?php endif;
								if (in_array('series:description', $this->col_serie)): ?>
									<td class="ss-col ss-series_desc hidden-phone"><?php echo JHTML::_('content.prepare', $item->series_description); ?></td>
								<?php endif;
								if (in_array('series:speaker', $this->col_serie)) : ?>
									<td class="ss-col ss-speakers hidden-phone hidden-tablet"><?php echo $item->speakers; ?></td>
								<?php endif;
								if (in_array('series:hits', $this->col_serie)) : ?>
									<td class="ss-col ss-hits hidden-phone hidden-tablet"><?php echo $item->hits; ?></td>
								<?php endif;
								if (in_array('series:download', $this->col_serie)) : ?>
									<td class="ss-col ss-dl hidden-phone"><a href="<?php echo JRoute::_('index.php?view=serie&layout=download&tmpl=component&id='.$item->slug); ?>" class="modal hasTip" rel="{handler:'iframe',size:{x:400,y:200}}" title="::<?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADSERIES_DESC'); ?>">
										<i class="icon-download"> </i>
									</a></td>
								<?php endif; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
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