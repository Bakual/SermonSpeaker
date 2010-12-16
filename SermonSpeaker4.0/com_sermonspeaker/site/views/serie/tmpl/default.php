<?php
defined('_JEXEC') or die('Restricted access');
JHtml::core();
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');

$columns = $this->params->get('col');
if (!$columns){
	$columns = array();
}
// TODO show category name in header
$this->cat = '';

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<div id="ss-serie-container">
<h1 class="componentheading"><?php echo JText::_('COM_SERMONSPEAKER_SERIE_TITLE').": ".$this->serie->series_title; ?></h1>

<div class="ss-serie-desc">
	<span class="ss-avatar">
		<?php if ($this->serie->avatar != '') : ?>
			<img src="<?php echo SermonspeakerHelperSermonspeaker::makelink($this->serie->avatar); ?>">
		<?php endif; ?>
	</span>
	<?php echo $this->serie->series_description; ?>
</div>
<br style="clear:both" />
<?php if (in_array('serie:player', $columns)) : ?>
	<hr style="width: 80%; height: 1px; text-align: center;">
	<?php
	$lnk = "index.php%3Foption%3Dcom_sermonspeaker%26view%3Dfeed%26series_id%3D".$this->serie->id;
	$ret = SermonspeakerHelperSermonspeaker::insertPlayer($lnk);
	$pp_ret = explode("/",$ret);
	$pp_h = $pp_ret[0];
	$pp_w = $pp_ret[1];
	?>
	<hr style="width: 80%; height: 1px; text-align: center;">
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
			<?php if (in_array('serie:num', $columns)) : ?>
				<th class="ss-num">
					<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERMONNUMBER', 'sermon_number', $listDirn, $listOrder); ?>
				</th>
			<?php endif; ?>
			<th class="ss-title">
				<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERMONTITLE', 'sermon_title', $listDirn, $listOrder); ?>
			</th>
			<?php if (in_array('serie:scripture', $columns)) : ?>
				<th class="ss-col">
					<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SCRIPTURE', 'sermon_scripture', $listDirn, $listOrder); ?>
				</th>
			<?php endif;
			if (in_array('serie:speaker', $columns)) : ?>
				<th class="ss-col">
					<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SPEAKER', 'name', $listDirn, $listOrder); ?>
				</th>
			<?php endif;
			if (in_array('serie:date', $columns)) : ?>
				<th class="ss-col">
					<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERMONDATE', 'sermon_date', $listDirn, $listOrder); ?>
				</th>
			<?php endif;
			if (in_array('serie:length', $columns)) : ?>
				<th class="ss-col">
					<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERMONTIME', 'sermon_time', $listDirn, $listOrder); ?>
				</th>
			<?php endif;
			if (in_array('serie:addfile', $columns)) : ?>
				<th class="ss-col">
					<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_ADDFILE', 'addfileDesc', $listDirn, $listOrder); ?>
				</th>
			<?php endif; ?>
		</tr></thead>
	<!-- Begin Data -->
		<tbody>
			<?php foreach($this->items as $i => $item) : ?>
				<tr class="<?php echo ($i % 2) ? "odd" : "even"; ?>">
					<?php if (in_array('serie:num', $columns)) : ?>
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
					<?php if (in_array('serie:scripture', $columns)) : ?>
						<td class="ss-col">
							<?php echo JHTML::_('content.prepare', $item->sermon_scripture); ?>
						</td>
					<?php endif;
					if (in_array('serie:speaker', $columns)) : ?>
						<td class="ss_col">
							<?php echo SermonspeakerHelperSermonSpeaker::SpeakerTooltip($item->speaker_slug, $item->pic, $item->name); ?>
						</td>
					<?php endif;
					if (in_array('serie:date', $columns)) : ?>
						<td class="ss_col">
							<?php echo JHTML::date($item->sermon_date, JText::_($this->params->get('date_format'))); ?>
						</td>
					<?php endif;
					if (in_array('serie:length', $columns)) : ?>
						<td class="ss_col">
							<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
						</td>
					<?php endif;
					if (in_array('serie:addfile', $columns)) : ?>
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