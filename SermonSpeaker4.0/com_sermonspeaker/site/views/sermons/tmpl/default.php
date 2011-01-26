<?php
defined('_JEXEC') or die('Restricted access');
JHtml::core();
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');

// TODO show category name in header
$this->cat = '';

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$limit 		= (int)$this->params->get('limit', '');
?>
<div id="ss-sermons-container">
<h1 class="componentheading"><?php echo JText::_('COM_SERMONSPEAKER_SERMONS_TITLE').$this->cat; ?></h1>
<p />
<?php if (in_array('sermons:player', $this->columns) && count($this->items)) : ?>
	<div class="ss-sermons-player">
		<hr class="ss-sermons-player">
	<?php
	$player = SermonspeakerHelperSermonspeaker::insertPlayer($this->items);
	echo $player['mspace'];
	echo $player['script'];
	?>
		<hr class="ss-sermons-player">
	</div>
<?php endif; ?>
<?php if (empty($this->items)) : ?>
	<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
<?php else : ?>

<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" id="adminForm" name="adminForm">
	<fieldset class="filters">
	<legend class="hidelabeltxt"><?php echo JText::_('JGLOBAL_FILTER_LABEL'); ?></legend>
		<div class="display-limit">
			<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
	</fieldset>

	<table class="adminlist" cellpadding="2" cellspacing="2" width="100%">
	<!-- Create the headers with sorting links -->
		<thead><tr>
			<?php if (in_array('sermons:num', $this->columns)) : ?>
				<th class="ss-num">
					<?php if (!$limit) :
						echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERMONNUMBER', 'sermon_number', $listDirn, $listOrder);
					else :
						echo JText::_('COM_SERMONSPEAKER_SERMONNUMBER');
					endif; ?>
				</th>
			<?php endif; ?>
			<th class="ss-title">
				<?php if (!$limit) :
					echo JHTML::_('grid.sort', 'JGLOBAL_TITLE', 'sermon_title', $listDirn, $listOrder);
				else :
					echo JText::_('JGLOBAL_TITLE');
				endif; ?>
			</th>
			<?php if (in_array('sermons:scripture', $this->columns)) : ?>
				<th class="ss-col">
					<?php if (!$limit) :
						echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SCRIPTURE', 'sermon_scripture', $listDirn, $listOrder);
					else :
						echo JText::_('COM_SERMONSPEAKER_SCRIPTURE');
					endif; ?>
				</th>
			<?php endif;
			if (in_array('sermons:speaker', $this->columns)) : ?>
				<th class="ss-col">
					<?php if (!$limit) :
						 echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SPEAKER', 'name', $listDirn, $listOrder);
					else :
						echo JText::_('COM_SERMONSPEAKER_SPEAKER');
					endif; ?>
				</th>
			<?php endif;
			if (in_array('sermons:date', $this->columns)) : ?>
				<th class="ss-col">
					<?php if (!$limit) :
						 echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERMONDATE', 'sermon_date', $listDirn, $listOrder);
					else :
						echo JText::_('COM_SERMONSPEAKER_SERMONDATE');
					endif; ?>
				</th>
			<?php endif;
			if (in_array('sermons:length', $this->columns)) : ?>
				<th class="ss-col">
					<?php if (!$limit) :
						 echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERMONLENGTH', 'sermon_time', $listDirn, $listOrder);
					else :
						echo JText::_('COM_SERMONSPEAKER_SERMONLENGTH');
					endif; ?>
				</th>
			<?php endif;
			if (in_array('sermons:series', $this->columns)) : ?>
				<th class="ss-col">
					<?php if (!$limit) :
						 echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERIES', 'series_title', $listDirn, $listOrder);
					else :
						echo JText::_('COM_SERMONSPEAKER_SERIES');
					endif; ?>
				</th>
			<?php endif;
			if (in_array('sermons:addfile', $this->columns)) : ?>
				<th class="ss-col">
					<?php if (!$limit) :
						 echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_ADDFILE', 'addfileDesc', $listDirn, $listOrder);
					else :
						echo JText::_('COM_SERMONSPEAKER_ADDFILE');
					endif; ?>
				</th>
			<?php endif; ?>
		</tr></thead>
	<!-- Begin Data -->
		<tbody>
			<?php foreach($this->items as $i => $item) : ?>
				<tr class="<?php echo ($i % 2) ? "odd" : "even"; ?>">
					<?php if (in_array('sermons:num', $this->columns)) : ?>
						<td class="ss-num">
							<?php echo $item->sermon_number; ?>
						</td>
					<?php endif; ?>
					<td class="ss-title">
						<a href="<?php echo $item->link1; ?>">
							<img title="<?php echo JText::_('COM_SERMONSPEAKER_PLAYICON_HOOVER'); ?>" src="<?php echo JURI::root().'components/com_sermonspeaker/images/play.gif'; ?>" class='icon_play' alt="" />
						</a>
						<a title="<?php echo JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER'); ?>" href="<?php echo $item->link2; ?>">
							<?php echo $item->sermon_title; ?>
						</a>
					</td>
					<?php if (in_array('sermons:scripture', $this->columns)) : ?>
						<td class="ss-col">
							<?php echo JHTML::_('content.prepare', $item->sermon_scripture); ?>
						</td>
					<?php endif;
					if (in_array('sermons:speaker', $this->columns)) : ?>
						<td class="ss_col">
							<?php echo SermonspeakerHelperSermonSpeaker::SpeakerTooltip($item->speaker_slug, $item->pic, $item->name); ?>
						</td>
					<?php endif;
					if (in_array('sermons:date', $this->columns)) : ?>
						<td class="ss_col">
							<?php echo JHTML::date($item->sermon_date, JText::_($this->params->get('date_format'))); ?>
						</td>
					<?php endif;
					if (in_array('sermons:length', $this->columns)) : ?>
						<td class="ss_col">
							<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
						</td>
					<?php endif;
					if (in_array('sermons:series', $this->columns)) : ?>
						<td class="ss_col">
							<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->series_slug)); ?>">
								<?php echo $item->series_title; ?>
							</a>
						</td>
					<?php endif;
					if (in_array('sermons:addfile', $this->columns)) : ?>
						<td class="ss_col">
							<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($item->addfile, $item->addfileDesc); ?>
						</td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<div class="pagination">
		<p class="counter">
			<?php echo $this->pagination->getPagesCounter(); ?>
		</p>
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	<div>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	</div>
</form>
</div>
<?php endif; ?>