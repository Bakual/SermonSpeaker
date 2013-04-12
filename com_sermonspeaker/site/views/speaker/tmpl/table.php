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
$listOrderSermons	= $this->state_sermons->get('list.ordering');
$listDirnSermons	= $this->state_sermons->get('list.direction');
$listOrderSeries	= $this->state_series->get('list.ordering');
$listDirnSeries		= $this->state_series->get('list.direction');
$limit 		= (int)$this->params->get('limit', '');
$player		= SermonspeakerHelperSermonspeaker::getPlayer($this->sermons);
$this->document->addScriptDeclaration('Joomla.tableOrdering = function(order, dir, task, form) {
		if (typeof(form) === "undefined") {
			if (task == "series") {
				form = document.getElementById("adminFormSeries");
				task = "";
			} else {
				form = document.getElementById("adminForm");
			}
		}

		form.filter_order.value = order;
		form.filter_order_Dir.value = dir;
		Joomla.submitform(task, form);
	}');
$this->document->addScriptDeclaration('jQuery(function() {
		if (location.hash == \'#series\') {
			tab = \'#tab_series\';
		} else {
			tab = \'#tab_sermons\';
		}
		jQuery(\'#speakerTab a[href="\' + tab + \'"]\').tab(\'show\');
	})');

?>
<div class="category-list<?php echo $this->pageclass_sfx;?> ss-speaker-container<?php echo $this->pageclass_sfx; ?>">
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>
	<div class="<?php echo ($this->item->state) ? '': 'system-unpublished'; ?>">
		<div class="btn-group pull-right">
			<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				<i class="icon-cog"></i>
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
				<li class="email-icon"><?php echo JHtml::_('icon.email', $this->item, $this->params, array('type' => 'speaker')); ?></li>
				<?php if ($canEdit or ($canEditOwn and ($user->id == $this->item->created_by))) : ?>
					<li class="edit-icon"><?php echo JHtml::_('icon.edit', $this->item, $this->params, array('type' => 'speaker')); ?></li>
				<?php endif; ?>
			</ul>
		</div>
		<div class="page-header">
			<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->item->slug)); ?>">
				<h2><?php echo $this->item->name; ?></h2>
			</a>
			<?php if (!$this->item->state) : ?>
				<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
			<?php endif; ?>
		</div>
		<?php if ($this->item->pic) : ?>
			<div class="img-polaroid pull-right item-image">
				<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->item->slug)); ?>">
					<img src="<?php echo SermonspeakerHelperSermonspeaker::makelink($this->item->pic); ?>">
				</a>
			</div>
		<?php endif; ?>
		<div class="article-info speaker-info muted">
			<dl class="article-info">
				<dt class="article-info-term"><?php  echo JText::_('JDETAILS'); ?></dt>
				<?php if (in_array('speaker:category', $this->columns) and $this->item->category_title) : ?>
					<dd>
						<div class="category-name">
							<i class="icon-folder"></i>
							<?php echo JText::_('JCATEGORY'); ?>:
							<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakersRoute($this->item->catslug)); ?>"><?php echo $this->item->category_title; ?></a>
						</div>
					</dd>
				<?php endif;
				if (in_array('speaker:hits', $this->columns)) : ?>
					<dd>
						<div class="hits">
							<i class="icon-eye-open"></i>
							<?php echo JText::_('JGLOBAL_HITS'); ?>:
							<?php echo $this->item->hits; ?>
						</div>
					</dd>
				<?php endif;
				if ($this->item->website) : ?>
					<dd>
						<div class="website">
							<i class=" icon-out-2"></i>
							<a href="<?php echo $this->item->website; ?>">
								<?php echo JText::_('COM_SERMONSPEAKER_FIELD_WEBSITE_LABEL'); ?>
							</a>
						</div>
					</dd>
				<?php endif; ?>
			</dl>
		</div>
		<?php if (in_array('speaker:intro', $this->columns) and $this->item->intro) : ?>
			<div>
				<?php echo JHtml::_('content.prepare', $this->item->intro, '', 'com_sermonspeaker.intro'); ?>
			</div>
		<?php endif;
		if(in_array('speaker:bio', $this->columns) and $this->item->bio) : ?>
			<div>
				<?php echo JHtml::_('content.prepare', $this->item->bio, '', 'com_sermonspeaker.bio'); ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="clearfix"></div>
	<ul class="nav nav-pills" id="speakerTab">
		<li><a href="#tab_sermons" data-toggle="pill"><?php echo JText::_('COM_SERMONSPEAKER_SERMONS'); ?></a></li>
		<li><a href="#tab_series" data-toggle="pill"><?php echo JText::_('COM_SERMONSPEAKER_SERIES'); ?></a></li>
	</ul>
	<div class="pill-content">
		<div class="pill-pane" id="tab_sermons">
			<?php if (in_array('speaker:player', $this->col_sermon) and count($this->sermons)) :
				JHtml::stylesheet('com_sermonspeaker/player.css', '', true); ?>
				<div id="ss-speaker-player" class="ss-player row-fluid">
					<div class="span10 offset1">
						<hr />
						<?php if ($player->player != 'PixelOut') : ?>
							<div id="playing">
								<img id="playing-pic" class="picture" src="" alt="" />
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
						<hr />
						<?php if ($player->toggle) : ?>
							<div class="span2 offset4 btn-group">
								<img class="btn" src="media/com_sermonspeaker/images/Video.png" onclick="Video()" alt="Video" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_VIDEO'); ?>" />
								<img class="btn" src="media/com_sermonspeaker/images/Sound.png" onclick="Audio()" alt="Audio" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_AUDIO'); ?>" />
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
			<div class="cat-items">
				<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString().'#sermons'); ?>" method="post" id="adminForm" name="adminForm" class="form-inline">
					<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) :
						echo $this->loadTemplate('filters');
					endif; ?>
					<div class="clearfix"></div>
					<?php if (!count($this->sermons)) : ?>
						<div class="no_entries alert alert-error"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
					<?php else : ?>
						<table class="table table-striped table-hover table-condensed">
							<thead><tr>
								<?php if (in_array('speaker:num', $this->col_sermon)) : ?>
									<th class="num hidden-phone hidden-tablet">
										<?php if (!$limit) :
											echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_SERMONNUMBER', 'sermon_number', $listDirnSermons, $listOrderSermons);
										else :
											echo JText::_('COM_SERMONSPEAKER_SERMONNUMBER');
										endif; ?>
									</th>
								<?php endif; ?>
								<th class="ss-title">
									<?php if (!$limit) :
										echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'sermon_title', $listDirnSermons, $listOrderSermons);
									else :
										echo JText::_('JGLOBAL_TITLE');
									endif; ?>
								</th>
								<?php if (in_array('speaker:category', $this->col_sermon)) : ?>
									<th class="ss-col ss-category hidden-phone">
										<?php if (!$limit) :
											echo JHtml::_('grid.sort', 'JCATEGORY', 'category_title', $listDirnSermons, $listOrderSermons);
										else :
											echo JText::_('JCATEGORY');
										endif; ?>
									</th>
								<?php endif;
								if (in_array('speaker:scripture', $this->col_sermon)) : ?>
									<th class="ss-col ss-scripture hidden-phone">
										<?php if (!$limit) :
											echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL', 'book', $listDirnSermons, $listOrderSermons);
										else :
											echo JText::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL');
										endif; ?>
									</th>
								<?php endif;
								if (in_array('speaker:date', $this->col_sermon)) : ?>
									<th class="ss-col ss-date">
										<?php if (!$limit) :
											echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_DATE_LABEL', 'sermons.sermon_date', $listDirnSermons, $listOrderSermons);
										else :
											echo JText::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL');
										endif; ?>
									</th>
								<?php endif;
								if (in_array('speaker:length', $this->col_sermon)) : ?>
									<th class="ss-col ss-length hidden-phone hidden-tablet">
										<?php if (!$limit) :
											 echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_LENGTH_LABEL', 'sermon_time', $listDirnSermons, $listOrderSermons);
										else :
											echo JText::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL');
										endif; ?>
									</th>
								<?php endif;
								if (in_array('speaker:series', $this->col_sermon)) : ?>
									<th class="ss-col ss-series hidden-phone">
										<?php if (!$limit) :
											 echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_SERIES', 'series_title', $listDirnSermons, $listOrderSermons);
										else :
											echo JText::_('COM_SERMONSPEAKER_SERIES');
										endif; ?>
									</th>
								<?php endif;
								if (in_array('speaker:addfile', $this->col_sermon)) : ?>
									<th class="ss-col ss-addfile hidden-phone">
										<?php if (!$limit) :
											 echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_ADDFILE', 'addfileDesc', $listDirnSermons, $listOrderSermons);
										else :
											echo JText::_('COM_SERMONSPEAKER_ADDFILE');
										endif; ?>
									</th>
								<?php endif;
								if (in_array('speaker:hits', $this->col_sermon)) : ?>
									<th class="ss-col ss-hits hidden-phone hidden-tablet">
										<?php if (!$limit) :
											echo JHtml::_('grid.sort', 'JGLOBAL_HITS', 'hits', $listDirnSermons, $listOrderSermons);
										else :
											echo JText::_('JGLOBAL_HITS');
										endif; ?>
									</th>
								<?php endif;
								if (in_array('speaker:download', $this->col_sermon)) : 
									$prio	= $this->params->get('fileprio'); ?>
									<th class="ss-col ss-dl hidden-phone"></th>
								<?php endif; ?>
							</tr></thead>
						<!-- Begin Data -->
							<tbody>
								<?php foreach($this->sermons as $i => $item) : ?>
									<tr id="sermon<?php echo $i; ?>" class="<?php echo ($item->state) ? '': 'system-unpublished '; ?>cat-list-row<?php echo $i % 2; ?>">
										<?php if (in_array('speaker:num', $this->col_sermon)) : ?>
											<td class="num hidden-phone hidden-tablet">
												<?php echo $item->sermon_number; ?>
											</td>
										<?php endif; ?>
										<td class="ss-title">
											<?php echo SermonspeakerHelperSermonspeaker::insertSermonTitle($i, $item, $player);
											if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
												<span class="list-edit pull-left width-50">
													<?php echo JHtml::_('icon.edit', $item, $this->params, array('type' => 'sermon')); ?>
												</span>
											<?php endif; ?>
											<?php if (!$item->state) : ?>
												<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
											<?php endif; ?>
										</td>
										<?php if (in_array('speaker:category', $this->col_sermon)) : ?>
											<td class="ss-col ss-category hidden-phone">
												<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonsRoute($item->catslug)); ?>"><?php echo $item->category_title; ?></a>
											</td>
										<?php endif;
										if (in_array('speaker:scripture', $this->col_sermon)) : ?>
											<td class="ss-col ss-scripture hidden-phone">
												<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '<br />');
												echo JHtml::_('content.prepare', $scriptures); ?>
											</td>
										<?php endif;
										if (in_array('speaker:date', $this->col_sermon)) : ?>
											<td class="ss-col ss-date">
												<?php if ($item->sermon_date != '0000-00-00 00:00:00'):
													echo JHtml::date($item->sermon_date, JText::_($this->params->get('date_format')), true);
												endif; ?>
											</td>
										<?php endif;
										if (in_array('speaker:length', $this->col_sermon)) : ?>
											<td class="ss-col ss-length hidden-phone hidden-tablet">
												<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
											</td>
										<?php endif;
										if (in_array('speaker:series', $this->col_sermon)) : ?>
											<td class="ss-col ss-series hidden-phone">
												<?php if ($item->series_state): ?>
													<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->series_slug)); ?>"><?php echo $item->series_title; ?></a>
												<?php else:
													echo $item->series_title;
												endif; ?>
											</td>
										<?php endif;
										if (in_array('speaker:addfile', $this->col_sermon)) : ?>
											<td class="ss-col ss-addfile hidden-phone">
												<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($item->addfile, $item->addfileDesc); ?>
											</td>
										<?php endif;
										if (in_array('speaker:hits', $this->col_sermon)) : ?>
											<td class="ss-col ss-hits hidden-phone hidden-tablet">
												<?php echo $item->hits; ?>
											</td>
										<?php endif;
										if (in_array('speaker:download', $this->col_sermon)) : 
											$type = ($item->videofile && ($prio || !$item->audiofile)) ? 'video' : 'audio';
											$filesize = $type.'filesize'; ?>
											<td class="ss-col ss-dl hidden-phone">
												<?php echo SermonspeakerHelperSermonspeaker::insertdlbutton($item->slug, $type, 3, $item->$filesize); ?>
											</td>
										<?php endif; ?>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif;
					if ($user->authorise('core.edit.own', 'com_sermonspeaker')) :
						echo JHtml::_('icon.create', $this->category, $this->params);
					endif;
					if ($this->params->get('show_pagination') and ($this->pag_sermons->get('pages.total') > 1)) : ?>
						<div class="pagination">
							<?php if ($this->params->get('show_pagination_results', 1)) : ?>
								<p class="counter pull-right">
									<?php echo $this->pag_sermons->getPagesCounter(); ?>
								</p>
							<?php endif;
							echo $this->pag_sermons->getPagesLinks(); ?>
						</div>
					<?php endif; ?>
					<input type="hidden" name="task" value="" />
					<input type="hidden" name="filter_order" value="<?php echo $listOrderSermons; ?>" />
					<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirnSermons; ?>" />
					<input type="hidden" name="limitstart" value="" />
				</form>
			</div>
		</div>
		<div class="pill-pane" id="tab_series">
			<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString().'#series'); ?>" method="post" id="adminFormSeries" name="adminFormSeries">
				<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) : ?>
					<div class="filters btn-toolbar">
						<?php if ($this->params->get('show_pagination_limit')) : ?>
							<div class="btn-group pull-right">
								<label class="element-invisible">
									<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
								</label>
								<?php echo $this->pag_series->getLimitBox(); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<div class="clearfix"></div>
				<?php if (!count($this->series)) : ?>
					<div class="no_entries alert alert-error"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERIES')); ?></div>
				<?php else : ?>
					<table class="table table-striped table-hover table-condensed">
						<thead><tr>
							<?php if ($this->av) : ?>
								<th class="ss-av hidden-phone hidden-tablet"> </th>
							<?php endif; ?>
							<th class="ss-title">
								<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'series_title', $listDirnSeries, $listOrderSeries, 'series'); ?>
							</th>
							<?php if (in_array('speaker:category', $this->col_serie)) : ?>
								<th class="ss-col ss-category hidden-phone">
									<?php echo JHtml::_('grid.sort', 'JCATEGORY', 'category_title', $listDirnSeries, $listOrderSeries, 'series'); ?>
								</th>
							<?php endif;
							if (in_array('speaker:description', $this->col_serie)): ?>
								<th class="ss-col ss-series_desc hidden-phone">
									<?php echo JHtml::_('grid.sort', 'JGLOBAL_DESCRIPTION', 'series_description', $listDirnSeries, $listOrderSeries, 'series'); ?>
								</th>
							<?php endif;
							if (in_array('speaker:speaker', $this->col_serie)): ?>
								<th class="ss-col ss-speakers hidden-phone">
									<?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS'); ?>
								</th>
							<?php endif;
							if (in_array('speaker:hits', $this->col_serie)) : ?>
								<th class="ss-col ss-hits hidden-phone hidden-tablet">
									<?php echo JHtml::_('grid.sort', 'JGLOBAL_HITS', 'hits', $listDirnSeries, $listOrderSeries, 'series'); ?>
								</th>
							<?php endif;
							if (in_array('speaker:download', $this->col_serie)) : ?>
								<th class="ss-col ss-dl hidden-phone"></th>
							<?php endif; ?>
						</tr></thead>
					<!-- Begin Data -->
						<tbody>
							<?php foreach($this->series as $i => $item) : ?>
								<tr class="<?php echo ($item->state) ? '': 'system-unpublished '; ?>cat-list-row<?php echo $i % 2; ?>">
									<?php if ($this->av) :
										if ($item->avatar) : ?>
											<td class="ss-col ss-av hidden-phone hidden-tablet"><a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->slug)); ?>"><img class="img-polaroid" src="<?php echo SermonspeakerHelperSermonspeaker::makelink($item->avatar); ?>"></a></td>
										<?php else : ?>
											<td class="ss-col ss-av hidden-phone hidden-tablet"></td>
										<?php endif;
									endif; ?>
									<td class="ss-title">
										<a title='<?php echo JText::_('COM_SERMONSPEAKER_SERIESLINK_HOOVER'); ?>' href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->slug)); ?>">
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
									<?php if (in_array('speaker:category', $this->col_serie)) : ?>
										<td class="ss-col ss-category hidden-phone">
											<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSeriesRoute($item->catslug)); ?>"><?php echo $item->category_title; ?></a>
										</td>
									<?php endif;
									if (in_array('speaker:description', $this->col_serie)): ?>
										<td class="ss-col ss-series_desc hidden-phone"><?php echo JHtml::_('content.prepare', $item->series_description); ?></td>
									<?php endif;
									if (in_array('speaker:speaker', $this->col_serie)): ?>
										<td class="ss-col ss-speakers hidden-phone"><?php echo $item->speakers; ?></td>
									<?php endif;
									if (in_array('speaker:hits', $this->col_serie)) : ?>
										<td class="ss-col ss-hits hidden-phone hidden-tablet"><?php echo $item->hits; ?></td>
									<?php endif;
									if (in_array('speaker:download', $this->col_serie)) : ?>
										<td class="ss-col ss-dl hidden-phone"><a href="<?php echo JRoute::_('index.php?view=serie&layout=download&tmpl=component&id='.$item->slug); ?>" class="modal hasTip" rel="{handler:'iframe',size:{x:400,y:200}}" title="::<?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADSERIES_DESC'); ?>">
											<i class="icon-download"> </i>
										</a></td>
									<?php endif; ?>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif;
				if ($this->params->get('show_pagination') && ($this->pag_series->get('pages.total') > 1)) : ?>
					<div class="pagination">
						<?php if ($this->params->get('show_pagination_results', 1)) : ?>
							<p class="counter">
								<?php echo $this->pag_series->getPagesCounter(); ?>
							</p>
						<?php endif;
						echo $this->pag_series->getPagesLinks(); ?>
					</div>
				<?php endif; ?>
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="filter_order" value="<?php echo $listOrderSeries; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirnSeries; ?>" />
			</form>
		</div>
	</div>
</div>