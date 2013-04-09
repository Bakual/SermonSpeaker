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
$limit 		= (int)$this->params->get('limit', '');
$player		= SermonspeakerHelperSermonspeaker::getPlayer($this->items);
?>
<div class="category-list<?php echo $this->pageclass_sfx;?> ss-serie-container<?php echo $this->pageclass_sfx; ?>">
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
				<?php if (in_array('serie:download', $this->col_serie)) : ?>
					<li class="download-icon">
						<a href="<?php echo JRoute::_('index.php?view=serie&layout=download&tmpl=component&id='.$this->item->slug); ?>" class="modal" rel="{handler:'iframe',size:{x:400,y:200}}">
							<i class="icon-download" > </i> 
							<?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADSERIES_LABEL'); ?>
						</a>
					</li>
				<?php endif; ?>
				<li class="email-icon"><?php echo JHtml::_('icon.email', $this->item, $this->params, array('type' => 'serie')); ?></li>
				<?php if ($canEdit or ($canEditOwn and ($user->id == $this->item->created_by))) : ?>
					<li class="edit-icon"><?php echo JHtml::_('icon.edit', $this->item, $this->params, array('type' => 'serie')); ?></li>
				<?php endif; ?>
			</ul>
		</div>
		<div class="page-header">
			<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($this->item->slug)); ?>">
				<h2><?php echo $this->item->series_title; ?></h2>
			</a>
			<?php if (!$this->item->state) : ?>
				<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
			<?php endif; ?>
		</div>
		<?php if ($this->item->avatar) : ?>
			<div class="img-polaroid pull-right item-image">
				<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($this->item->slug)); ?>">
					<img src="<?php echo SermonspeakerHelperSermonspeaker::makelink($this->item->avatar); ?>">
				</a>
			</div>
		<?php endif; ?>
		<div class="article-info serie-info muted">
			<dl class="article-info">
				<dt class="article-info-term"><?php  echo JText::_('JDETAILS'); ?></dt>
				<?php if (in_array('serie:category', $this->col_serie) and $this->item->category_title) : ?>
					<dd>
						<div class="category-name">
							<i class="icon-folder"></i>
							<?php echo JText::_('JCATEGORY'); ?>:
							<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSeriesRoute($this->item->catslug)); ?>"><?php echo $this->item->category_title; ?></a>
						</div>
					</dd>
				<?php endif;
				if (in_array('serie:hits', $this->col_serie)) : ?>
					<dd>
						<div class="hits">
							<i class="icon-eye-open"></i>
							<?php echo JText::_('JGLOBAL_HITS'); ?>:
							<?php echo $this->item->hits; ?>
						</div>
					</dd>
				<?php endif; ?>
			</dl>
		</div>
		<?php if (in_array('serie:description', $this->col_serie) and $this->item->series_description) : ?>
			<div>
				<?php echo JHtml::_('content.prepare', $this->item->series_description, '', 'com_sermonspeaker.description'); ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="clearfix"></div>
	<?php if (in_array('serie:player', $this->columns) and count($this->items)) :
		JHtml::stylesheet('com_sermonspeaker/player.css', '', true); ?>
		<div id="ss-serie-player" class="ss-player row-fluid">
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
				echo $this->loadTemplate('filters30');
			endif; ?>
			<div class="clearfix"></div>
			<?php if (!count($this->items)) : ?>
				<div class="no_entries alert alert-error"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
			<?php else : ?>
				<table class="table table-striped table-hover table-condensed">
					<thead><tr>
						<?php if (in_array('serie:num', $this->columns)) : ?>
							<th class="num hidden-phone hidden-tablet">
								<?php if (!$limit) :
									echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_SERMONNUMBER', 'sermon_number', $listDirn, $listOrder);
								else :
									echo JText::_('COM_SERMONSPEAKER_SERMONNUMBER');
								endif; ?>
							</th>
						<?php endif; ?>
						<th class="ss-title">
							<?php if (!$limit) :
								echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'sermon_title', $listDirn, $listOrder);
							else :
								echo JText::_('JGLOBAL_TITLE');
							endif; ?>
						</th>
						<?php if (in_array('serie:category', $this->columns)) : ?>
							<th class="ss-col ss-category hidden-phone">
								<?php if (!$limit) :
									echo JHtml::_('grid.sort', 'JCATEGORY', 'category_title', $listDirn, $listOrder);
								else :
									echo JText::_('JCATEGORY');
								endif; ?>
							</th>
						<?php endif;
						if (in_array('serie:scripture', $this->columns)) : ?>
							<th class="ss-col ss-scripture hidden-phone">
								<?php if (!$limit) :
									echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL', 'book', $listDirn, $listOrder);
								else :
									echo JText::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL');
								endif; ?>
							</th>
						<?php endif;
						if (in_array('sermons:speaker', $this->columns)) : ?>
							<th class="ss-col ss-speaker hidden-phone">
								<?php if (!$limit) :
									echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_SPEAKER', 'name', $listDirn, $listOrder);
								else :
									echo JText::_('COM_SERMONSPEAKER_SPEAKER');
								endif; ?>
							</th>
						<?php endif;
						if (in_array('serie:date', $this->columns)) : ?>
							<th class="ss-col ss-date">
								<?php if (!$limit) :
									echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_DATE_LABEL', 'sermons.sermon_date', $listDirn, $listOrder);
								else :
									echo JText::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL');
								endif; ?>
							</th>
						<?php endif;
						if (in_array('serie:length', $this->columns)) : ?>
							<th class="ss-col ss-length hidden-phone hidden-tablet">
								<?php if (!$limit) :
									 echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_LENGTH_LABEL', 'sermon_time', $listDirn, $listOrder);
								else :
									echo JText::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL');
								endif; ?>
							</th>
						<?php endif;
						if (in_array('serie:addfile', $this->columns)) : ?>
							<th class="ss-col ss-addfile hidden-phone">
								<?php if (!$limit) :
									 echo JHtml::_('grid.sort', 'COM_SERMONSPEAKER_ADDFILE', 'addfileDesc', $listDirn, $listOrder);
								else :
									echo JText::_('COM_SERMONSPEAKER_ADDFILE');
								endif; ?>
							</th>
						<?php endif;
						if (in_array('serie:hits', $this->columns)) : ?>
							<th class="ss-col ss-hits hidden-phone hidden-tablet">
								<?php if (!$limit) :
									echo JHtml::_('grid.sort', 'JGLOBAL_HITS', 'hits', $listDirn, $listOrder);
								else :
									echo JText::_('JGLOBAL_HITS');
								endif; ?>
							</th>
						<?php endif;
						if (in_array('serie:download', $this->columns)) : 
							$prio	= $this->params->get('fileprio'); ?>
							<th class="ss-col ss-dl hidden-phone"></th>
						<?php endif; ?>
					</tr></thead>
				<!-- Begin Data -->
					<tbody>
						<?php foreach($this->items as $i => $item) : ?>
							<tr id="sermon<?php echo $i; ?>" class="<?php echo ($item->state) ? '': 'system-unpublished '; ?>cat-list-row<?php echo $i % 2; ?>">
								<?php if (in_array('serie:num', $this->columns)) : ?>
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
								<?php if (in_array('serie:category', $this->columns)) : ?>
									<td class="ss-col ss-category hidden-phone">
										<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonsRoute($item->catslug)); ?>"><?php echo $item->category_title; ?></a>
									</td>
								<?php endif;
								if (in_array('serie:scripture', $this->columns)) : ?>
									<td class="ss-col ss-scripture hidden-phone">
										<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '<br />');
										echo JHtml::_('content.prepare', $scriptures); ?>
									</td>
								<?php endif;
								if (in_array('sermons:speaker', $this->columns)) : ?>
									<td class="ss-col ss-speaker hidden-phone">
										<?php if ($item->speaker_state):
											echo SermonspeakerHelperSermonSpeaker::SpeakerTooltip($item->speaker_slug, $item->pic, $item->name);
										else :
											echo $item->name;
										endif; ?>
									</td>
								<?php endif;
								if (in_array('serie:date', $this->columns)) : ?>
									<td class="ss-col ss-date">
										<?php if ($item->sermon_date != '0000-00-00 00:00:00'):
											echo JHtml::date($item->sermon_date, JText::_($this->params->get('date_format')), true);
										endif; ?>
									</td>
								<?php endif;
								if (in_array('serie:length', $this->columns)) : ?>
									<td class="ss-col ss-length hidden-phone hidden-tablet">
										<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
									</td>
								<?php endif;
								if (in_array('serie:addfile', $this->columns)) : ?>
									<td class="ss-col ss-addfile hidden-phone">
										<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($item->addfile, $item->addfileDesc); ?>
									</td>
								<?php endif;
								if (in_array('serie:hits', $this->columns)) : ?>
									<td class="ss-col ss-hits hidden-phone hidden-tablet">
										<?php echo $item->hits; ?>
									</td>
								<?php endif;
								if (in_array('serie:download', $this->columns)) : 
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
</div>