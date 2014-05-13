<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2014 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

JHtml::_('bootstrap.tooltip');

$user		= JFactory::getUser();
$fu_enable	= $this->params->get('fu_enable');
$canEdit	= ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn	= ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$limit 		= (int) $this->params->get('limit', '');
$player		= SermonspeakerHelperSermonspeaker::getPlayer($this->items);
?>
<div class="category-list<?php echo $this->pageclass_sfx;?> ss-sermons-container<?php echo $this->pageclass_sfx; ?>">
	<?php
	if ($this->params->get('show_page_heading', 1)) : ?>
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
	<?php endif;

	if (in_array('sermons:player', $this->columns) and count($this->items)) :
		JHtml::stylesheet('com_sermonspeaker/player.css', '', true); ?>
		<div id="ss-sermons-player" class="ss-player row-fluid">
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
		<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" id="adminForm" name="adminForm" class="form-inline">
			<?php
			if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) :
				echo $this->loadTemplate('filters');
			endif; ?>
			<div class="clearfix"></div>
			<?php if (!count($this->items)) : ?>
				<div class="no_entries alert alert-error"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
			<?php else : ?>
				<ul class="category list-striped list-condensed">
					<?php foreach($this->items as $i => $item) :
						$sep = 0; ?>
						<li id="sermon<?php echo $i; ?>" class="<?php echo ($item->state) ? '': 'system-unpublished '; ?>cat-list-row<?php echo $i % 2; ?>">
							<?php
							if (in_array('sermons:hits', $this->columns)) : ?>
								<span class="ss-hits badge badge-info pull-right">
									<?php echo JText::sprintf('JGLOBAL_HITS_COUNT', $item->hits); ?>
								</span>
							<?php endif;

							if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
								<span class="list-edit pull-left width-50">
									<?php echo JHtml::_('icon.edit', $item, $this->params, array('type' => 'sermon')); ?>
								</span>
							<?php endif; ?>
							<strong class="ss-title">
								<?php echo SermonspeakerHelperSermonspeaker::insertSermonTitle($i, $item, $player); ?>
							</strong>
							<?php if (!$item->state) : ?>
								<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
							<?php endif; ?>
							<br />
							<?php if (in_array('sermons:speaker', $this->columns) and $item->speaker_title) :
								$sep = 1; ?>
								<small class="ss-speaker">
									<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER'); ?>:
									<?php echo JLayoutHelper::render('com_sermonspeaker.titles.speaker', array('item' => $item, 'params' => $this->params)); ?>
								</small>
							<?php endif;

							if (in_array('sermons:series', $this->columns) and $item->series_title) :
								if ($sep) : ?>
									|
								<?php endif;
								$sep = 1; ?>
								<small class="ss-series">
									<?php echo JText::_('COM_SERMONSPEAKER_SERIE'); ?>: 
									<?php
									if ($item->series_state): ?>
										<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->series_slug)); ?>">
											<?php echo $item->series_title; ?>
										</a>
									<?php else :
										echo $item->series_title;
									endif; ?>
								</small>
							<?php endif;

							if (in_array('sermons:length', $this->columns) and $item->sermon_time != '00:00:00') :
								if ($sep) : ?>
									|
								<?php endif;
								$sep = 1; ?>
								<small class="ss-length">
									<?php echo JText::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL'); ?>: 
									<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
								</small>
							<?php endif;

							if (in_array('sermons:scripture', $this->columns) and $item->scripture) :
								if ($sep) : ?>
									|
								<?php endif;
								$sep = 1; ?>
								<small class="ss-scripture">
									<?php echo JText::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?>: 
									<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '; ');
									echo JHtml::_('content.prepare', $scriptures, '', 'com_sermonspeaker.scripture'); ?>
								</small>
							<?php endif;

							if (in_array('sermons:date', $this->columns) and ($item->sermon_date != '0000-00-00 00:00:00')) : ?>
								<span class="ss-date small pull-right">
									<?php echo JHtml::date($item->sermon_date, JText::_($this->params->get('date_format')), true); ?>
								</span>&nbsp;
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif;

			if ($user->authorise('core.create', 'com_sermonspeaker')) :
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
	<?php if (!empty($this->children[$this->category->id]) and $this->maxLevel != 0) : ?>
		<div class="cat-children">
			<h3><?php echo JTEXT::_('JGLOBAL_SUBCATEGORIES'); ?></h3>
			<?php echo $this->loadTemplate('children'); ?>
		</div>
	<?php endif; ?>
</div>
