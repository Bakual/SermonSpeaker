<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$limit 		= (int)$this->params->get('limit', '');
$player = new SermonspeakerHelperPlayer($this->items);
?>
<div class="ss-sermons-container<?php echo htmlspecialchars($this->params->get('pageclass_sfx')); ?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif;
if ($this->cat): ?>
	<h2><span class="subheading-category"><?php echo $this->cat; ?></span></h2>
<?php endif;
if (in_array('sermons:player', $this->columns) && count($this->items)) : ?>
	<div class="ss-sermons-player">
		<hr class="ss-sermons-player" />
		<?php if ($player->player != 'PixelOut'): ?>
			<div id="playing"></div>
		<?php endif;
		echo $player->mspace;
		echo $player->script;
		?>
		<hr class="ss-sermons-player" />
	<?php if ($player->toggle): ?>
		<div>
			<img class="pointer" src="media/com_sermonspeaker/images/Video.png" onclick="Video()" alt="Video" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_VIDEO'); ?>" />
			<img class="pointer" src="media/com_sermonspeaker/images/Sound.png" onclick="Audio()" alt="Audio" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_AUDIO'); ?>" />
		</div>
	<?php endif; ?>
	</div>
<?php endif; ?>
<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" id="adminForm" name="adminForm">
	<?php foreach($this->items as $i => $item) : ?>
		<div id="sermon<?php echo $i; ?>" class="ss-entry" onclick="jwplayer().playlistItem('<?php echo $i; ?>')">
			<?php if ($item->picture): ?>
				<div class="ss-picture"><img src="<?php echo SermonspeakerHelperSermonspeaker::makelink($item->picture); ?>"></div>
			<?php elseif ($item->pic): ?>
				<div class="ss-picture"><img src="<?php echo SermonspeakerHelperSermonspeaker::makelink($item->pic); ?>"></div>
			<?php endif; ?>
			<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug)); ?>"><h3><?php echo $item->sermon_title; ?></h3></a>
			<dl class="article-info" style="float:left">
			<dt class="article-info-term"><?php echo JText::_('JDETAILS'); ?></dt>
			<?php if (in_array('sermons:series', $this->columns) && $item->series_title) : ?>
				<dd class="category-name">
					<?php echo JText::_('COM_SERMONSPEAKER_SERIE_TITLE'); ?>:
					<?php if ($item->series_state) : ?>
						<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->series_slug)); ?>">
					<?php echo $this->escape($item->series_title); ?></a>
					<?php else :
						echo $this->escape($item->series_title);
					endif; ?>
				</dd>
			<?php endif;
			if (in_array('sermons:date', $this->columns)) : ?>
				<dd class="create">
					<?php echo JText::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'); ?>:
					<?php echo JHTML::Date($item->sermon_date, JText::_($this->params->get('date_format')), 'UTC'); ?>
				</dd>
			<?php endif;
			if (in_array('sermons:speaker', $this->columns) && $item->name) : ?>
				<dd class="createdby">
					<?php echo JText::_('COM_SERMONSPEAKER_SPEAKER'); ?>:
					<?php if ($item->speaker_state):
						echo SermonspeakerHelperSermonSpeaker::SpeakerTooltip($item->speaker_slug, $item->pic, $item->name);
					else: 
						echo $item->name;
					endif; ?>
				</dd>
			<?php endif;
			if (in_array('sermons:hits', $this->columns)) : ?>
				<dd class="hits">
					<?php echo JText::_('JGLOBAL_HITS'); ?>: 
					<?php echo $item->hits; ?>
				</dd>
			<?php endif;if (in_array('sermons:scripture', $this->columns) && $item->sermon_scripture) : ?>
				<dd class="ss-sermondetail-info">
					<?php echo JText::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?>:
					<?php echo JHTML::_('content.prepare', $item->sermon_scripture); ?>
				</dd>
			<?php endif;
			if ($this->params->get('custom1') && $item->custom1) : ?>
				<dd class="ss-sermondetail-info">
					<?php echo JText::_('COM_SERMONSPEAKER_CUSTOM1'); ?>:
					<?php echo $item->custom1; ?>
				</dd>
			<?php endif;
			if ($this->params->get('custom2') && $item->custom2) : ?>
				<dd class="ss-sermondetail-info">
					<?php echo JText::_('COM_SERMONSPEAKER_CUSTOM2'); ?>:
					<?php echo $item->custom2; ?>
				</dd>
			<?php endif;
			if (in_array('sermons:length', $this->columns)) : ?>
				<dd class="ss-sermondetail-info">
					<?php echo JText::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL'); ?>:
					<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
				</dd>
			<?php endif;
			if (in_array('sermons:addfile', $this->columns) && $item->addfile) : ?>
				<dd class="ss-sermondetail-info">
					<?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?>:
					<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($item->addfile, $item->addfileDesc); ?>
				</dd>
			<?php endif; ?>
			</dl>
			<div style="clear:left"></div>
			<?php if (strlen($item->notes) > 0) : ?>
				<div>
					<?php echo JHTML::_('content.prepare', $item->notes); ?>
				</div>
			<?php endif; ?>
		</div>
		<hr class="ss-sermons-player" style="clear:both" />
	<?php endforeach; ?>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
</form>
</div>