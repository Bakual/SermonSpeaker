<?php
defined('_JEXEC') or die('Restricted access');

$cid = JRequest::getVar('cid', array(0), '', 'array');
JArrayHelper::toInteger($cid, array(0));

$edit = JRequest::getBool('edit', true);
$text = ($edit ? JText::_('Edit') : JText::_('New'));

JToolBarHelper::title(JText::_('SERMON').': <small><small>[ '.$text.' ]</small></small>', 'sermons');
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
	<legend><?php echo JText::_('SERMON'); ?></legend>
	<table class="admintable"> 
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('SERMONTITLE'); ?></td>
			<td><input class="text_area" type="text" name="sermon_title" id="sermon_title" size="50" maxlength="250" value="<?php echo $this->row->sermon_title;?>" /></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('ALIAS'); ?></td>
			<td><input class="text_area" type="text" name="alias" id="alias" size="50" maxlength="250" value="<?php echo $this->row->alias;?>" /></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('SCRIPTURE'); ?></td>
			<td><input class="text_area" type="text" name="sermon_scripture" id="sermon_scripture" size="50" maxlength="250" value="<?php echo $this->row->sermon_scripture;?>" /></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('SERMON_DATE'); ?></td>
            <td><?php echo JHTML::Calendar($this->row->sermon_date, 'sermon_date', 'sermon_date').' '.JText::_('FORMAT YYYY-MM-DD'); ?></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('SERMONPATH'); ?></td>
			<td>
				<input type="radio" name="sel" value="1" onclick="enableElement(this.form.elements['sermon_path_txt']);" checked>
				<input class="text_area" type="text" name="sermon_path_txt" id="sermon_path_txt" size="130" maxlength="250" value="<?php echo $this->row->sermon_path;?>" />
				<br>
				<input type="radio" name="sel" value="2" onclick="disElement(this.form.elements['sermon_path_txt']);enableElement(this.form.elements['sermon_path_choice']);">
				<?php echo $this->lists['sermon_path_choice']; ?>
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('SERMONNUMBER'); ?></td>
			<td><input class="text_area" type="text" name="sermon_number" id="sermon_number" size="10" maxlength="250" value="<?php echo $this->row->sermon_number;?>" /></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('SERMONTIME'); ?></td>
			<td><input class="text_area" type="text" name="sermon_time" id="sermon_time" size="50" maxlength="250" value="<?php echo $this->row->sermon_time;?>" /></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('SPEAKER'); ?></td>
			<td><?php echo $this->lists['speaker_id']; ?></td>
		</tr> 
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('SERIES'); ?></td>
			<td><?php echo $this->lists['series_id']; ?></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('NAMEENTEREDBY'); ?></td>
			<td><?php echo $this->lists['created_by']; ?></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('NOTES'); ?></td>
			<td><?php echo $editor->display('notes', $this->row->notes, '100%', '200', '40', '10');	?></td>
		</tr>
		<tr>
			<td valign="top" align="right" class="key"><label for="catid"><?php echo JText::_( 'CATEGORY' ); ?>:</label></td>
			<td><?php echo $this->lists['catid']; ?></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('PUBLISHED'); ?></td>
			<td><?php echo $this->lists['published']; ?></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('SERMONCAST'); ?></td>
			<td><?php echo $this->lists['podcast']; ?></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('ADDFILE'); ?></td>
			<td>
				<input type="radio" name="seladdfile" value="1" onclick="enableElement(this.form.elements['addfile_txt']);" checked>
				<input class="text_area" type="text" name="addfile_txt" id="addfile_txt" size="130" maxlength="250" value="<?php echo $this->row->addfile;?>" />
				<br>
				<input type="radio" name="seladdfile" value="2" onclick="disElement(this.form.elements['addfile_txt']);enableElement(this.form.elements['addfile_choice']);">
				<?php echo $this->lists['addfile_choice']; ?>
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('ADDFILEDESC'); ?></td>
			<td><input class="text_area" type="text" name="addfileDesc" id="addfileDesc" size="80" maxlength="250" value="<?php echo $this->row->addfileDesc;?>" /></td>
		</tr>
	</table> 
	</fieldset> 
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" /> 
	<input type="hidden" name="option" value="com_sermonspeaker" /> 
	<input type="hidden" name="view" value="sermon" />
	<input type="hidden" name="controller" value="sermon" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_('form.token'); ?>
</form> 