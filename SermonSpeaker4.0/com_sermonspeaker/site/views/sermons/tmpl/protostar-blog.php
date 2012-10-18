<?php
defined('_JEXEC') or die;

JHTML::addIncludePath(JPATH_COMPONENT.'/helpers');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
JHtml::_('bootstrap.tooltip');

$user		= JFactory::getUser();
$canEdit	= $user->authorise('core.edit', 'com_sermonspeaker');
$canEditOwn	= $user->authorise('core.edit.own', 'com_sermonspeaker');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$limit 		= (int)$this->params->get('limit', '');
$player		= new SermonspeakerHelperPlayer($this->items);
?>
<div class="category-list<?php echo $this->pageclass_sfx;?> ss-sermons-container<?php echo $this->pageclass_sfx; ?>">
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
	<?php endif;
	if (in_array('sermons:player', $this->columns) and count($this->items)) : ?>
		<div class="ss-sermons-player">
			<hr class="ss-sermons-player" />
			<?php if ($player->player != 'PixelOut') : ?>
				<div id="playing">
					<img id="playing-pic" class="picture" src="" />
					<span id="playing-duration" class="duration"></span>
					<div class="text">
						<span id="playing-title" class="title"></span>
						<span id="playing-desc" class="desc"></span>
					</div>
					<span id="playing-error" class="error"></span>
				</div>
			<?php endif;
			echo $player->mspace;
			echo $player->script;
			?>
			<hr class="ss-sermons-player" />
			<?php if ($player->toggle) : ?>
				<div>
					<img class="pointer" src="media/com_sermonspeaker/images/Video.png" onclick="Video()" alt="Video" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_VIDEO'); ?>" />
					<img class="pointer" src="media/com_sermonspeaker/images/Sound.png" onclick="Audio()" alt="Audio" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_AUDIO'); ?>" />
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<div class="cat-items">
		<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" id="adminForm" name="adminForm" class="form-inline">
			<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) : ?>
				<div class="filters btn-toolbar">
					<?php if ($this->params->get('filter_field')) :?>
						<div class="btn-group">
							<label class="filter-search-lbl element-invisible" for="filter-search">
								<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
								<?php echo JText::_('JGLOBAL_FILTER_LABEL').'&#160;'; ?>
							</label>
							<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="input-medium" onchange="document.adminForm.submit();" title="<?php echo JText::_('COM_SERMONSPEAKER_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('COM_SERMONSPEAKER_FILTER_SEARCH_DESC'); ?>" />
						</div>
						<div class="btn-group filter-select">
							<select name="book" id="filter_books" class="input-medium" onchange="this.form.submit()">
								<?php echo JHtml::_('select.options', $this->books, 'value', 'text', $this->state->get('scripture.book'), true);?>
							</select>
							<select name="month" id="filter_months" class="input-medium" onchange="this.form.submit()">
								<option value="0"><?php echo JText::_('COM_SERMONSPEAKER_SELECT_MONTH'); ?></option>
								
								<?php echo JHtml::_('select.options', $this->months, 'value', 'text', $this->state->get('date.month'), true);?>
							</select>
							<select name="year" id="filter_years" class="input-medium" onchange="this.form.submit()">
								<option value="0"><?php echo JText::_('COM_SERMONSPEAKER_SELECT_YEAR'); ?></option>
								<?php echo JHtml::_('select.options', $this->years, 'year', 'year', $this->state->get('date.year'), true);?>
							</select>
						</div>
					<?php endif;
					if ($this->params->get('show_pagination_limit')) : ?>
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
				<div class="no_entries alert alert-error"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
			<?php else : ?>
				<div class="items-leading">
					<?php foreach($this->items as $i => $item) : ?>
						<div id="sermon<?php echo $i; ?>" class="<?php echo ($item->state) ? '': 'system-unpublished'; ?>">
							<div class="btn-group pull-right">
								<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
									<i class="icon-cog"></i>
									<span class="caret"></span>
								</a>
								<ul class="dropdown-menu">
									<li class="play-icon"><?php echo JHtml::_('icon.play', $item, $this->params, array('index' => $i)); ?></li>
									<?php if (in_array('sermons:download', $this->columns)) :
										if ($item->audiofile) : ?>
											<li class="download-icon"><?php echo JHtml::_('icon.download', $item, $this->params, array('type' => 'audio')); ?></li>
										<?php endif;
										if ($item->videofile) : ?>
											<li class="download-icon"><?php echo JHtml::_('icon.download', $item, $this->params, array('type' => 'video')); ?></li>
										<?php endif; ?>
									<?php endif; ?>
									<li class="email-icon"><?php echo JHtml::_('icon.email', $item, $this->params, array('type' => 'sermon')); ?></li>
									<?php if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
										<li class="edit-icon"><?php echo JHtml::_('icon.edit', $item, $this->params, array('type' => 'sermon')); ?></li>
									<?php endif; ?>
								</ul>
							</div>
							<div class="page-header">
								<h2><?php echo SermonspeakerHelperSermonspeaker::insertSermonTitle($i, $item, $player, false); ?></h2>
								<?php if (!$item->state) : ?>
									<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
								<?php endif;
								if (in_array('sermons:speaker', $this->columns) and $item->name) : ?>
									<small class="ss-speaker createdby">
										<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER'); ?>: 
										<?php if ($item->speaker_state):
											echo SermonspeakerHelperSermonSpeaker::SpeakerTooltip($item->speaker_slug, $item->pic, $item->name);
										else :
											echo $item->name;
										endif; ?>
									</small>
								<?php endif; ?>
							</div>
							<?php if ($picture = SermonspeakerHelperSermonspeaker::insertPicture($item)) : ?>
								<div class="img-polaroid pull-right"><img src="<?php echo $picture; ?>"></div>
							<?php endif; ?>
							<div class="article-info sermon-info muted">
								<dl class="article-info">
									<dt class="article-info-term"><?php  echo JText::_('JDETAILS'); ?></dt>
									<?php if (in_array('sermons:category', $this->columns) and $item->category_title) : ?>
										<dd>
											<div class="category-name">
												<i class="icon-folder"></i>
												<?php echo JText::_('JCATEGORY'); ?>:
												<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonsRoute($item->catslug)); ?>"><?php echo $item->category_title; ?></a>
											</div>
										</dd>
									<?php endif;
									if (in_array('sermons:series', $this->columns) and $item->series_title) : ?>
										<dd>
											<div class="category-name">
												<i class="icon-drawer-2"></i>
												<?php echo JText::_('COM_SERMONSPEAKER_SERIE_TITLE'); ?>:
												<?php if ($item->series_state) : ?>
													<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->series_slug)); ?>">
												<?php echo $this->escape($item->series_title); ?></a>
												<?php else :
													echo $this->escape($item->series_title);
												endif; ?>
											</div>
										</dd>
									<?php endif;
									if (in_array('sermons:date', $this->columns) and ($item->sermon_date != '0000-00-00 00:00:00')) : ?>
										<dd>
											<div class="create">
												<i class="icon-calendar"></i>
												<?php echo JText::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'); ?>:
												<?php echo JHTML::Date($item->sermon_date, JText::_($this->params->get('date_format')), true); ?>
											</div>
										</dd>
									<?php endif;
									if (in_array('sermons:hits', $this->columns)) : ?>
										<dd>
											<div class="hits">
												<i class="icon-eye-open"></i>
												<?php echo JText::_('JGLOBAL_HITS'); ?>:
												<?php echo $item->hits; ?>
											</div>
										</dd>
									<?php endif;
									if (in_array('sermons:scripture', $this->columns) and $item->scripture) : ?>
										<dd>
											<div class="ss-sermondetail-info">
												<i class="icon-quote"></i>
												<?php echo JText::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?>:
												<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '; ');
												echo JHTML::_('content.prepare', $scriptures, '', 'com_sermonspeaker.scripture'); ?>
											</div>
										</dd>
									<?php endif;
									if ($this->params->get('custom1') and $item->custom1) : ?>
										<dd>
											<div class="ss-sermondetail-info">
												<?php echo JText::_('COM_SERMONSPEAKER_CUSTOM1'); ?>:
												<?php echo $item->custom1; ?>
											</div>
										</dd>
									<?php endif;
									if ($this->params->get('custom2') and $item->custom2) : ?>
										<dd>
											<div class="ss-sermondetail-info">
												<?php echo JText::_('COM_SERMONSPEAKER_CUSTOM2'); ?>:
												<?php echo $item->custom2; ?>
											</div>
										</dd>
									<?php endif;
									if (in_array('sermons:length', $this->columns) and $item->sermon_time != '00:00:00') : ?>
										<dd>
											<div class="ss-sermondetail-info">
												<i class="icon-clock"></i>
												<?php echo JText::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL'); ?>:
												<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
											</div>
										</dd>
									<?php endif;
									if (in_array('sermons:addfile', $this->columns) and$item->addfile) : ?>
										<dd>
											<div class="ss-sermondetail-info">
												<?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?>:
												<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($item->addfile, $item->addfileDesc); ?>
											</div>
										</dd>
									<?php endif; ?>
								</dl>
							</div>
							<?php if (in_array('sermons:notes', $this->columns) and $item->notes) : ?>
								<div>
									<?php echo JHTML::_('content.prepare', $item->notes, '', 'com_sermonspeaker.notes'); ?>
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