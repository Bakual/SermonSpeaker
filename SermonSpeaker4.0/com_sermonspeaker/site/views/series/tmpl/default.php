<?php
defined('_JEXEC') or die('Restricted access');
JHtml::core();
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<div id="ss-series-container">
<h1 class="componentheading"><?php echo JText::_('COM_SERMONSPEAKER_SERIES_TITLE').$this->cat; ?></h1>
<p />
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
			<?php if ($this->av) : ?>
				<th width='10'> </th>
			<?php endif; ?>
			<th class="ss-title">
				<?php echo JHTML::_('grid.sort', 'JGLOBAL_TITLE', 'series_title', $listDirn, $listOrder); ?>
			</th>
			<th align="left"><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER'); ?></th>
		</tr></thead>
	<!-- Begin Data -->
		<tbody>
			<?php foreach($this->items as $i => $item) : ?>
				<tr class="<?php echo ($i % 2) ? "odd" : "even"; ?>">
					<?php if ($this->av) :
						if ($item->avatar) : ?>
							<td class="ss-col"><img src="<?php echo SermonspeakerHelperSermonspeaker::makelink($item->avatar); ?>"></td>
						<?php else : ?>
							<td class="ss-col"></td>
						<?php endif;
					endif; ?>
					<td class="ss-title"><a title='<?php echo JText::_('COM_SERMONSPEAKER_SERIESLINK_HOOVER'); ?>' href="<?php echo JRoute::_("index.php?view=serie&id=$item->slug" ); ?>"><?php echo $item->series_title; ?></a></td>
					<td  class="ss-col"><?php echo $item->speakers; ?></td>
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
<?php endif; ?>
</div>