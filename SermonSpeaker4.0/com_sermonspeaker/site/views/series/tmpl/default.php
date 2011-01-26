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
<?php if (empty($this->items)) : ?>
	<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
<?php else : ?>

<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" id="adminForm" name="adminForm">
	<?php if ($this->params->get('show_pagination_limit')) : ?>
	<div class="display-limit">
		<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>
	<?php endif; ?>
	<table class="category">
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
					<td class="ss-title">
						<a title='<?php echo JText::_('COM_SERMONSPEAKER_SERIESLINK_HOOVER'); ?>' href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->slug)); ?>">
							<?php echo $item->series_title; ?>
						</a>
					</td>
					<td  class="ss-col"><?php echo $item->speakers; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php if (($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
	<div class="pagination">
		<?php if ($this->params->def('show_pagination_results', 1)) : ?>
		 	<p class="counter">
				<?php echo $this->pagination->getPagesCounter(); ?>
			</p>
		<?php endif; ?>

		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	<?php endif; ?>
	<div>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	</div>
</form>
<?php endif; ?>
</div>