<?php
defined('_JEXEC') or die('Restricted access');

$cid = JRequest::getVar('cid', array(0), '', 'array');
JArrayHelper::toInteger($cid, array(0));

$edit = JRequest::getBool('edit', true);
if($edit){
	$text = JText::_('Edit');
	$uri = JURI::getInstance();
	$self = $uri->_uri;
} else {
	$text = JText::_('New');
	$self = JURI::current().'?option=com_sermonspeaker&controller=sermon&task=add';
}

JToolBarHelper::title(JText::_('COM_SERMONSPEAKER_SERMON').': <small><small>[ '.$text.' ]</small></small>', 'sermons');
JToolBarHelper::save();
JToolBarHelper::apply();
if ($edit) {
	JToolBarHelper::cancel('cancel', 'Close');
} else {
	JToolBarHelper::cancel();
}
$editor =& JFactory::getEditor(); 
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" >
	<fieldset class="adminform">
	<legend><?php echo JText::_('COM_SERMONSPEAKER_SERMON'); ?></legend>
	<table class="admintable"> 
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_SERMONTITLE'); ?></td>
			<td><input class="text_area" type="text" name="sermon_title" id="sermon_title" size="50" maxlength="250" value="<?php echo $this->row->sermon_title;?>" /></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('ALIAS'); ?></td>
			<td><input class="text_area" type="text" name="alias" id="alias" size="50" maxlength="250" value="<?php echo $this->row->alias;?>" /></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE'); ?></td>
			<td>
				<input class="text_area" type="text" name="sermon_scripture" id="sermon_scripture" size="50" maxlength="250" value="<?php echo $this->row->sermon_scripture;?>" />
				<img onClick="sendText(document.adminForm.sermon_scripture,'{bib=','}')" src='<?php echo JURI::root(); ?>/components/com_sermonspeaker/images/blue_tag.png' title='insert Biblelink tag' alt='insert Biblelink tag'>
				<img onClick="sendText(document.adminForm.sermon_scripture,'{bible}','{/bible}')" src='<?php echo JURI::root(); ?>/components/com_sermonspeaker/images/green_tag.png' title='insert ScriptureLink tag' alt='insert ScriptureLink tag'>
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_CUSTOM1'); ?></td>
            <td><input class="text_area" type="text" name="custom1" id="custom1" size="50" maxlength="250" value="<?php echo $this->row->custom1;?>" /></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_CUSTOM2'); ?></td>
            <td><input class="text_area" type="text" name="custom2" id="custom2" size="50" maxlength="250" value="<?php echo $this->row->custom2;?>" /></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_SERMONDATE'); ?></td>
            <td><?php echo JHTML::Calendar($this->row->sermon_date, 'sermon_date', 'sermon_date').' '.JText::_('COM_SERMONSPEAKER_DATEFORMAT'); ?></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_SERMONPATH'); ?></td>
			<td>
				<input type="radio" name="sel" value="1" onclick="enableElement(this.form.elements['sermon_path_txt'], this.form.elements['sermon_path_choice']);" checked>
				<input class="text_area" type="text" name="sermon_path_txt" id="sermon_path_txt" size="130" maxlength="250" value="<?php echo $this->row->sermon_path;?>" />
				<br>
				<input type="radio" name="sel" value="2" onclick="enableElement(this.form.elements['sermon_path_choice'], this.form.elements['sermon_path_txt']);">
				<?php echo $this->lists['sermon_path_choice']; ?>
				<img onClick="window.location.href='<?php echo $self; ?>&amp;file='+document.adminForm.sermon_path_choice.value;" src='<?php echo JURI::root(); ?>/components/com_sermonspeaker/images/find.png' alt='lookup ID3' title='lookup ID3'>
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_SERMONNUMBER'); ?></td>
			<td><input class="text_area" type="text" name="sermon_number" id="sermon_number" size="10" maxlength="250" value="<?php echo $this->row->sermon_number;?>" /></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_SERMONTIME'); ?></td>
			<td><input class="text_area" type="text" name="sermon_time" id="sermon_time" size="50" maxlength="250" value="<?php echo $this->row->sermon_time;?>" /></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER'); ?></td>
			<td><?php echo $this->lists['speaker_id']; ?></td>
		</tr> 
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_SERIE'); ?></td>
			<td><?php echo $this->lists['series_id']; ?></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_ENTEREDBY'); ?></td>
			<td><?php echo $this->lists['created_by']; ?></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_SERMONNOTES'); ?></td>
			<td><?php echo $editor->display('notes', $this->row->notes, '100%', '200', '40', '10');	?></td>
		</tr>
		<tr>
			<td valign="top" align="right" class="key"><label for="catid"><?php echo JText::_('CATEGORY'); ?>:</label></td>
			<td><?php echo $this->lists['catid']; ?></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('PUBLISHED'); ?></td>
			<td><?php echo $this->lists['published']; ?></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_SERMONCAST'); ?></td>
			<td><?php echo $this->lists['podcast']; ?></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?></td>
			<td>
				<input type="radio" name="seladdfile" value="1" onclick="enableElement(this.form.elements['addfile_txt'], this.form.elements['addfile_choice']);" checked>
				<input class="text_area" type="text" name="addfile_txt" id="addfile_txt" size="130" maxlength="250" value="<?php echo $this->row->addfile;?>" />
				<br>
				<input type="radio" name="seladdfile" value="2" onclick="enableElement(this.form.elements['addfile_choice'], this.form.elements['addfile_txt']);">
				<?php echo $this->lists['addfile_choice']; ?>
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_ADDFILEDESC'); ?></td>
			<td><input class="text_area" type="text" name="addfileDesc" id="addfileDesc" size="80" maxlength="250" value="<?php echo $this->row->addfileDesc;?>" /></td>
		</tr>
	</table> 
	</fieldset> 
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" /> 
	<input type="hidden" name="created_on" value="<?php echo $this->row->created_on; ?>" /> 
	<input type="hidden" name="option" value="com_sermonspeaker" /> 
	<input type="hidden" name="view" value="sermon" />
	<input type="hidden" name="controller" value="sermon" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_('form.token'); ?>
</form> 