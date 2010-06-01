<?php
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');

JToolBarHelper::title(JText::_('STATISTICS'), 'statistics');
JToolbarHelper::spacer();
JToolbarHelper::divider();
JToolbarHelper::spacer();
JToolBarHelper::preferences('com_sermonspeaker',550);
?>

<table border="0" cellpadding="2" cellspacing="0" width="40%" class="adminlist">
	<tr>
		<td style="background-color: #6D86BE; color: #CCC;" colspan="4">
			<img src="<?php echo JURI::base(); ?>components/com_sermonspeaker/images/speakers.png" border="0" width="48" height="48" alt="<?php echo JText::_('SPEAKERS'); ?>" /> <?php echo JText::_('SPEAKERSTATS'); ?>
		</td>
	</tr>
	<tr>
		<th width="20" align="left"><?php echo JText::_('ID'); ?></th>
		<th align="left"><?php echo JText::_('SPEAKER'); ?></th>
		<th width="20" align="left"><?php echo JText::_('COUNT'); ?></th>
		<th width="20" align="left"><?php echo JText::_('RESET'); ?></th>
	</tr>
	<?php
	$k = 0;
	for ($i=0, $n=count($this->speakers); $i < $n; $i++) {
		$row = &$this->speakers[$i]; ?>
		<tr class="<?php echo "row".$k; ?>">
			<td align="left"><?php echo $row->id; ?></td>
			<td align="left"><?php echo $row->name; ?></td>
			<td align="left"><?php echo $row->hits; ?></td>
			<td align="center">
				<a href="index.php?option=com_sermonspeaker&controller=statistics&task=resetcount&table=speakers&id=<?php echo $row->id; ?>">
					<img src="<?php echo JURI::base(); ?>components/com_sermonspeaker/images/reset.png" width="16" height="16" border="0" alt="Reset" />
				</a>
			</td>
		</tr>
		<?php $k = 1 - $k;
	} ?>
</table>

<table border="0" cellpadding="2" cellspacing="0" width="40%" class="adminlist">
	<tr>
		<td style="background-color: #6D86BE; color: #CCC;" colspan="4">
			<img src="<?php echo JURI::base(); ?>components/com_sermonspeaker/images/series.png" border="0" width="48" height="48" alt="<?php echo JText::_('SERIES'); ?>" /> <?php echo JText::_('SERIESTATS'); ?>
		</td>
	</tr>
	<tr>
		<th width="20" align="left"><?php echo JText::_('ID'); ?></th>
		<th align="left"><?php echo JText::_('SERIES'); ?></th>
		<th width="20" align="left"><?php echo JText::_('COUNT'); ?></th>
		<th width="20" align="left"><?php echo JText::_('RESET'); ?></th>
	</tr>
	<?php
	$k = 0;
	for ($i=0, $n=count($this->series); $i < $n; $i++) {
		$row = &$this->series[$i]; ?>
		<tr class="<?php echo "row".$k; ?>">
			<td align="left"><?php echo $row->id; ?></td>
			<td align="left"><?php echo $row->series_title; ?></td>
			<td align="left"><?php echo $row->hits; ?></td>
			<td align="center">
				<a href="index.php?option=com_sermonspeaker&controller=statistics&task=resetcount&table=series&id=<?php echo $row->id; ?>">
					<img src="<?php echo JURI::base(); ?>components/com_sermonspeaker/images/reset.png" width="16" height="16" border="0" alt="Reset" />
				</a>
			</td>
		</tr>
		<?php $k = 1 - $k;
	} ?>
</table>

<table border="0" cellpadding="2" cellspacing="0" width="40%" class="adminlist">
	<tr>
		<td style="background-color: #6D86BE; color: #CCC;" colspan="4">
			<img src="<?php echo JURI::base(); ?>components/com_sermonspeaker/images/sermon.png" border="0" width="48" height="48" alt="<?php echo JText::_('SERMONS'); ?>" /> <?php echo JText::_('SERMONSTATS'); ?>
		</td>
	</tr>
	<tr>
		<th width="20" align="left"><?php echo JText::_('ID'); ?></th>
		<th align="left"><?php echo JText::_('SERMON'); ?></th>
		<th width="20" align="left"><?php echo JText::_('COUNT'); ?></th>
		<th width="20" align="left"><?php echo JText::_('RESET'); ?></th>
	</tr>
	<?php
	$k = 0;
	for ($i=0, $n=count($this->sermons); $i < $n; $i++) {
		$row = &$this->sermons[$i]; ?>
		<tr class="<?php echo "row".$k; ?>">
			<td align="left"><?php echo $row->id; ?></td>
			<td align="left"><?php echo $row->sermon_title; ?></td>
			<td align="left"><?php echo $row->hits; ?></td>
			<td align="center">
				<a href="index.php?option=com_sermonspeaker&controller=statistics&task=resetcount&table=sermons&id=<?php echo $row->id; ?>">
					<img src="<?php echo JURI::base(); ?>components/com_sermonspeaker/images/reset.png" width="16" height="16" border="0" alt="Reset" />
				</a>
			</td>
		</tr>
		<?php $k = 1 - $k;
	} ?>
</table>