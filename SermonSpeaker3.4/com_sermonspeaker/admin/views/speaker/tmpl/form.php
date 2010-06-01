<?php
defined('_JEXEC') or die('Restricted access');

$cid = JRequest::getVar('cid', array(0), '', 'array');
JArrayHelper::toInteger($cid, array(0));

$edit = JRequest::getBool('edit', true);
$text = ($edit ? JText::_('Edit') : JText::_('New'));

JToolBarHelper::title(JText::_('SPEAKER').': <small><small>[ '.$text.' ]</small></small>', 'speakers');
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
	<legend><?php echo JText::_('SPEAKERS'); ?></legend>
	<table class="admintable">
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('NAME'); ?></td>
			<td><input class="text_area" type="text" name="name" id="name" size="50" maxlength="250" value="<?php echo $this->row->name; ?>" /></td> 
		</tr> 
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('NAMEENTEREDBY'); ?></td>
			<td><?php echo JHTML::_('list.users', 'created_by', $this->row->created_by, 0, '', 'name', 0); ?></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('WEBSITE'); ?></td>
			<td><input class="text_area" type="text" name="website" id="website" size="90" maxlength="250" value="<?php echo $this->row->website;?>" /></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('PICTURE'); ?></td>
			<td><input class="text_area" type="text" name="pic" id="pic" size="90" maxlength="250" value="<?php echo $this->row->pic;?>" /></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('INTRO'); ?></td>
			<td><?php echo $editor->display('intro',$this->row->intro,'100%','200','40','10'); ?></td>
		</tr>
		<tr> 
			<td width="100" align="right" class="key"><?php echo JText::_('BIO'); ?></td>
			<td><?php echo $editor->display('bio',$this->row->bio,'100%','300','40','10');	?></td>
		</tr>    
		<tr>
			<td valign="top" align="right" class="key"><label for="catid"><?php echo JText::_( 'CATEGORY' ); ?>:</label></td>
			<td><?php echo $this->lists['catid']; ?></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('PUBLISHED'); ?></td>
			<td><?php echo JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $this->row->published); ?></td>
		</tr>
	</table>
	</fieldset>
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="com_sermonspeaker" />
	<input type="hidden" name="controller" value="speaker" />
	<input type="hidden" name="view" value="speakers" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_('form.token'); ?>
</form>