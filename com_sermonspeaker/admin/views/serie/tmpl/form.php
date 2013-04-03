<?php
defined('_JEXEC') or die('Restricted access');

$cid = JRequest::getVar('cid', array(0), '', 'array');
JArrayHelper::toInteger($cid, array(0));

$edit = JRequest::getBool('edit', true);
$text = ($edit ? JText::_('Edit') : JText::_('New'));

JToolBarHelper::title(JText::_('COM_SERMONSPEAKER_SERIE').': <small><small>[ '.$text.' ]</small></small>', 'series');
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
	<legend><?php echo JText::_('COM_SERMONSPEAKER_SERIE'); ?></legend>
	<table class="admintable">
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_SERIESTITLE'); ?></td> 
			<td><input class="text_area" type="text" name="series_title" id="series_title" size="50" maxlength="250" value="<?php echo $this->row->series_title;?>" /></td> 
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_ENTEREDBY'); ?></td>
			<td><?php echo $this->lists['created_by']; ?></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_SERIESDESCRIPTION'); ?></td>
			<td><?php echo $editor->display('series_description', $this->row->series_description, '100%', '200', '40', '10'); ?></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_SERIESAVATAR'); ?></td>
			<td><?php echo $this->lists['avatar']; ?></td>
		</tr>
		<tr>
			<td valign="top" align="right" class="key"><label for="catid"><?php echo JText::_('CATEGORY'); ?>:</label></td>
			<td><?php echo $this->lists['catid']; ?></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('PUBLISHED'); ?></td>
			<td><?php echo JHTML::_( 'select.booleanlist',  'published', 'class="inputbox"', $this->row->published ); ?></td>
		</tr>
	</table> 
	</fieldset> 
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" /> 
	<input type="hidden" name="created_on" value="<?php echo $this->row->created_on; ?>" /> 
	<input type="hidden" name="option" value="com_sermonspeaker" /> 
	<input type="hidden" name="view" value="series" />
	<input type="hidden" name="controller" value="serie" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_('form.token'); ?>
</form>