<?php
defined('_JEXEC') or die('Restricted access');

$cid = JRequest::getVar('cid', array(0), '', 'array');
JArrayHelper::toInteger($cid, array(0));

$edit = JRequest::getBool('edit', true);
$text = ($edit ? JText::_('Edit') : JText::_('New'));

JToolBarHelper::title(JText::_('SERIE').': <small><small>[ '.$text.' ]</small></small>');
JToolBarHelper::save();
JToolBarHelper::apply();
if ($edit) {
	JToolBarHelper::cancel('cancel', 'Close');
} else {
	JToolBarHelper::cancel();
}
$editor =& JFactory::getEditor(); 
?>

<form action="index.php" method="post" name="adminForm" id="adminForm"> 
	<fieldset class="adminform">
	<legend><?php echo JText::_('SERIES'); ?></legend>
	<table class="admintable">
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('SERIESTITLE'); ?></td> 
			<td><input class="text_area" type="text" name="series_title" id="series_title" size="50" maxlength="250" value="<?php echo $this->row->series_title;?>" /></td> 
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('NAMEENTEREDBY'); ?></td>
			<td><?php echo $this->lists['created_by']; ?></td>
		</tr>
		<tr> 
			<td width="100" align="right" class="key"><?php echo JText::_('SPEAKERNAME'); ?></td>
			<td>
				<table>
				<tr>
				<td>1: <?php echo $this->lists['speaker_id']; ?></td> 
				<td>2: <?php echo $this->lists['speaker2']; ?></td> 
				<td>3: <?php echo $this->lists['speaker3']; ?></td>
				</tr>
				<tr>
				<td>4: <?php echo $this->lists['speaker4']; ?></td> 
				<td>5: <?php echo $this->lists['speaker5']; ?></td> 
				<td>6: <?php echo $this->lists['speaker6']; ?></td>
				</tr>
				<tr>
				<td>7: <?php echo $this->lists['speaker7']; ?></td> 
				<td>8: <?php echo $this->lists['speaker8']; ?></td> 
				<td>9: <?php echo $this->lists['speaker9']; ?></td>
				</tr>
				<tr>
				<td>10: <?php echo $this->lists['speaker10']; ?></td> 
				<td>11: <?php echo $this->lists['speaker11']; ?></td> 
				<td>12: <?php echo $this->lists['speaker12']; ?></td>
				</tr>
				<tr>
				<td>13: <?php echo $this->lists['speaker13']; ?></td> 
				<td>14: <?php echo $this->lists['speaker14']; ?></td> 
				<td>15: <?php echo $this->lists['speaker15']; ?></td>
				</tr>
				<tr>
				<td>16: <?php echo $this->lists['speaker16']; ?></td> 
				<td>17: <?php echo $this->lists['speaker17']; ?></td> 
				<td>18: <?php echo $this->lists['speaker18']; ?></td>
				</tr>
				<tr>
				<td>19: <?php echo $this->lists['speaker19']; ?></td> 
				<td>20: <?php echo $this->lists['speaker20']; ?></td> 
				<td></td>
				</tr>
				</table>
			</td>
		</tr> 
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('SERIESDESCRIPTION'); ?></td>
			<td><?php echo $editor->display('series_description', $this->row->series_description, '100%', '200', '40', '10'); ?></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('SERIESAVATAR'); ?></td>
			<td><?php echo $this->lists['avatar']; ?></td>
		</tr>
		<tr>
			<td valign="top" align="right" class="key"><label for="catid"><?php echo JText::_( 'CATEGORY' ); ?>:</label></td>
			<td><?php echo $this->lists['catid']; ?></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('PUBLISHED'); ?></td>
			<td><?php echo JHTML::_( 'select.booleanlist',  'published', 'class="inputbox"', $this->row->published ); ?></td>
		</tr>
	</table> 
	</fieldset> 
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" /> 
	<input type="hidden" name="option" value="<?php echo $option;?>" /> 
	<input type="hidden" name="view" value="series" />
	<input type="hidden" name="controller" value="serie" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_('form.token'); ?>
</form>