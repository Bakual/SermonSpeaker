<?php
defined('_JEXEC') or die;

JHTML::addIncludePath(JPATH_COMPONENT.'/helpers');

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
				<div class="items-leading">
					<?php foreach($this->items as $i => $item) : ?>
						<div class="<?php echo ($item->state) ? '': 'system-unpublished'; ?>">
							<div class="btn-group pull-right">
								<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
									<i class="icon-cog"></i>
									<span class="caret"></span>
								</a>
								<ul class="dropdown-menu">
									<?php if (in_array('series:download', $this->col_serie)) : ?>
										<li class="download-icon">
											<a href="<?php echo JRoute::_('index.php?view=serie&layout=download&tmpl=component&id='.$item->slug); ?>" class="modal" rel="{handler:'iframe',size:{x:400,y:200}}">
												<i class="icon-download" > </i> 
												<?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADSERIES_LABEL'); ?>
											</a>
										</li>
									<?php endif; ?>
									<li class="email-icon"><?php echo JHtml::_('icon.email', $item, $this->params, array('type' => 'serie')); ?></li>
									<?php if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
										<li class="edit-icon"><?php echo JHtml::_('icon.edit', $item, $this->params, array('type' => 'serie')); ?></li>
									<?php endif; ?>
								</ul>
							</div>
							<div class="page-header">
								<a title="<?php echo JText::_('COM_SERMONSPEAKER_SERIESLINK_HOOVER'); ?>" href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->slug)); ?>">
									<h2><?php echo $item->series_title; ?></h2>
								</a>
								<?php if (!$item->state) : ?>
									<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
								<?php endif;
								if (in_array('series:speaker', $this->col_serie) and $item->speakers) : ?>
									<small class="ss-speakers createdby">
										<?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS'); ?>: 
										<?php echo $item->speakers; ?>
									</small>
								<?php endif; ?>
							</div>
							<?php if ($item->avatar) : ?>
								<div class="img-polaroid pull-right item-image">
									<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->slug)); ?>">
										<img src="<?php echo SermonspeakerHelperSermonspeaker::makelink($item->avatar); ?>">
									</a>
								</div>
							<?php endif; ?>
							<div class="article-info serie-info muted">
								<dl class="article-info">
									<dt class="article-info-term"><?php  echo JText::_('JDETAILS'); ?></dt>
									<?php if (in_array('series:category', $this->col_serie) and $item->category_title) : ?>
										<dd>
											<div class="category-name">
												<i class="icon-folder"></i>
												<?php echo JText::_('JCATEGORY'); ?>:
												<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSeriesRoute($item->catslug)); ?>"><?php echo $item->category_title; ?></a>
											</div>
										</dd>
									<?php endif;
									if (in_array('series:hits', $this->col_serie)) : ?>
										<dd>
											<div class="hits">
												<i class="icon-eye-open"></i>
												<?php echo JText::_('JGLOBAL_HITS'); ?>:
												<?php echo $item->hits; ?>
											</div>
										</dd>
									<?php endif; ?>
								</dl>
							</div>
							<?php if (in_array('series:description', $this->col_serie) and $item->series_description) : ?>
								<div>
									<?php echo JHTML::_('content.prepare', $item->series_description, '', 'com_sermonspeaker.series_description'); ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="clearfix"></div>
					<?php endforeach; ?>
				</div>
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