<?php
defined('_JEXEC') or die('Restricted access');

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

jimport('joomla.html.pane');
$pane	=& JPane::getInstance('sliders', array('allowAllClose' => true));
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" >
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
<td valign="top">
	<table class="adminform"> 
		<tr>
			<td width="100" align="right" class="key">
				<label for="sermon_title"><?php echo JText::_('COM_SERMONSPEAKER_SERMONTITLE'); ?>:</label>
			</td>
			<td>
				<input class="text_area" type="text" name="sermon_title" id="sermon_title" size="40" maxlength="250" value="<?php echo $this->row->sermon_title;?>" />
			</td>
			<td width="100" align="right" class="key">
				<label for="published"><?php echo JText::_('PUBLISHED'); ?>:</label>
			</td>
			<td><?php echo $this->lists['published']; ?></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="alias"><?php echo JText::_('ALIAS'); ?>:</label>
			</td>
			<td>
				<input class="text_area" type="text" name="alias" id="alias" size="40" maxlength="250" value="<?php echo $this->row->alias;?>" />
			</td>
			<td width="100" align="right" class="key">
				<label for="podcast"><?php echo JText::_('COM_SERMONSPEAKER_SERMONCAST'); ?>:</label>
			</td>
			<td><?php echo $this->lists['podcast']; ?></td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="sermon_path"><?php echo JText::_('COM_SERMONSPEAKER_SERMONPATH'); ?>:</label>
			</td>
			<td colspan="3">
				<input type="radio" name="sel" value="1" onclick="enableElement(this.form.elements['sermon_path_txt'], this.form.elements['sermon_path_choice']);" checked>
				<input class="text_area" type="text" name="sermon_path_txt" id="sermon_path_txt" size="100" maxlength="250" value="<?php echo $this->row->sermon_path;?>" />
				<br>
				<input type="radio" name="sel" value="2" onclick="enableElement(this.form.elements['sermon_path_choice'], this.form.elements['sermon_path_txt']);">
				<?php echo $this->lists['sermon_path_choice']; ?>
				<img onClick="window.location.href='<?php echo $self; ?>&amp;file='+document.adminForm.sermon_path_choice.value;" src='<?php echo JURI::root(); ?>/components/com_sermonspeaker/images/find.png' alt='lookup ID3' title='lookup ID3'>
			</td>
		</tr>
	</table>
	<fieldset>
		<legend><?php echo JText::_('COM_SERMONSPEAKER_SERMONNOTES'); ?></legend>
		<?php echo $editor->display('notes', $this->row->notes, '100%', '350', '40', '10');	?>
	</fieldset>
</td>
<td valign="top" width="320" style="padding: 7px 0 0 5px">
	<table width="100%" style="border: 1px dashed silver; padding: 5px; margin-bottom: 10px;">
		<?php if ($this->row->id){ ?>
			<tr>
				<td><strong><?php echo JText::_('COM_SERMONSPEAKER_ID'); ?>:</strong></td>
				<td><?php echo $this->row->id; ?></td>
			</tr>
		<?php } ?>
		<tr>
			<td>
				<strong><?php echo JText::_('COM_SERMONSPEAKER_HITS'); ?></strong>
			</td>
			<td>
				<?php echo $this->row->hits;
				if ($this->row->hits) { ?>
					<a href="index.php?option=com_sermonspeaker&controller=sermon&task=resetcount&id=<?php echo $this->row->id; ?>">
						<img src="<?php echo JURI::base(); ?>components/com_sermonspeaker/images/reset.png" width="16" height="16" border="0" title="<?php echo JText::_('RESET'); ?>" alt="Reset" />
					</a>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td>
				<strong><?php echo JText::_('COM_SERMONSPEAKER_ENTEREDON'); ?></strong>
			</td>
			<td>
				<?php echo JHTML::Date($this->row->created_on, JText::_('%Y-%M-%D')); ?>
			</td>
		</tr>
		<tr>
			<td>
				<strong><?php echo JText::_('COM_SERMONSPEAKER_ENTEREDBY' ); ?></strong>
			</td>
			<td>
				<?php $user =& JFactory::getUser($this->row->created_by);
				echo $user->name; ?>
			</td>
		</tr>
	</table>
	<fieldset>
	<legend><?php echo JText::_('COM_SERMONSPEAKER_INFORMATIONS'); ?></legend>
	<?php
	echo $pane->startPane('sermon-pane');
	echo $pane->startPanel(JText::_('COM_SERMONSPEAKER_GENERAL'), 'general-panel' );
	?>
		<table width="100%" class="paramlist admintable" cellspacing="1">
		<tr>
			<td width="40%" class="paramlist_key">
				<label for="sermon_number"><?php echo JText::_('COM_SERMONSPEAKER_SERMONNUMBER'); ?>:</label>
			</td>
			<td class="paramlist_value">
				<input class="text_area" type="text" name="sermon_number" id="sermon_number" size="5" maxlength="250" value="<?php echo $this->row->sermon_number;?>" />
			</td>
		</tr>
		<tr>
			<td width="40%" class="paramlist_key">
				<label for="sermon_date"><?php echo JText::_('COM_SERMONSPEAKER_SERMONDATE'); ?>:</label>
			</td>
            <td class="paramlist_value">
				<?php echo JHTML::Calendar($this->row->sermon_date, 'sermon_date', 'sermon_date', '%Y-%m-%d', array('size' => '10')).' '.JText::_('COM_SERMONSPEAKER_DATEFORMAT'); ?>
			</td>
		</tr>
		<tr>
			<td width="40%" class="paramlist_key">
				<label for="sermon_time"><?php echo JText::_('COM_SERMONSPEAKER_SERMONTIME'); ?>:</label>
			</td>
			<td class="paramlist_value">
				<input class="text_area" type="text" name="sermon_time" id="sermon_time" size="10" maxlength="250" value="<?php echo $this->row->sermon_time;?>" />
			</td>
		</tr>
		<tr>
			<td width="40%" class="paramlist_key">
				<label for="sermon_scripture"><?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE'); ?>:</label>
			</td>
			<td class="paramlist_value">
				<input class="text_area" type="text" name="sermon_scripture" id="sermon_scripture" size="20" maxlength="250" value="<?php echo $this->row->sermon_scripture;?>" />
				<img onClick="sendText(document.adminForm.sermon_scripture,'{bib=','}')" src='<?php echo JURI::root(); ?>/components/com_sermonspeaker/images/blue_tag.png' title='insert Biblelink tag' alt='insert Biblelink tag'>
				<img onClick="sendText(document.adminForm.sermon_scripture,'{bible}','{/bible}')" src='<?php echo JURI::root(); ?>/components/com_sermonspeaker/images/green_tag.png' title='insert ScriptureLink tag' alt='insert ScriptureLink tag'>
			</td>
		</tr>
		<tr>
			<td width="40%" class="paramlist_key">
				<label for="speaker_id"><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER'); ?>:</label>
			</td>
			<td class="paramlist_value"><?php echo $this->lists['speaker_id']; ?></td>
		</tr> 
		<tr>
			<td width="40%" class="paramlist_key">
				<label for="series_id"><?php echo JText::_('COM_SERMONSPEAKER_SERIE'); ?>:</label>
			</td>
			<td class="paramlist_value"><?php echo $this->lists['series_id']; ?></td>
		</tr>
		<tr>
			<td width="40%" class="paramlist_key">
				<label for="catid"><?php echo JText::_('CATEGORY'); ?>:</label>
			</td>
			<td class="paramlist_value"><?php echo $this->lists['catid']; ?></td>
		</tr>
		</table>
	<?php
	echo $pane->endPanel();
	echo $pane->startPanel(JText::_('COM_SERMONSPEAKER_CUSTOM'), 'custom-panel' );
	?>
		<table width="100%" class="paramlist admintable" cellspacing="1">
		<tr>
			<td width="40%" class="paramlist_key">
				<label for="custom1"><?php echo JText::_('COM_SERMONSPEAKER_CUSTOM1'); ?>:</label>
			</td>
            <td class="paramlist_value">
				<input class="text_area" type="text" name="custom1" id="custom1" size="50" maxlength="250" value="<?php echo $this->row->custom1;?>" />
			</td>
		</tr>
		<tr>
			<td width="40%" class="paramlist_key">
				<label for="custom2"><?php echo JText::_('COM_SERMONSPEAKER_CUSTOM2'); ?>:</label>
			</td>
            <td class="paramlist_value">
				<input class="text_area" type="text" name="custom2" id="custom2" size="50" maxlength="250" value="<?php echo $this->row->custom2;?>" />
			</td>
		</tr>
		</table>
	<?php
	echo $pane->endPanel();
	echo $pane->startPanel(JText::_('COM_SERMONSPEAKER_METADATA'), 'metadata-panel' );
	?>
		<table width="100%" class="paramlist admintable" cellspacing="1">
		<tr>
			<td width="40%" class="paramlist_key">
				<label for="metadesc"><?php echo JText::_('COM_SERMONSPEAKER_METADESC'); ?>:</label>
			</td>
			<td class="paramlist_value">
				<textarea class="text_area" name="metadesc" id="metadesc" cols="30" rows="5"><?php echo $this->row->metadesc; ?></textarea>
			</td>
		</tr>
		<tr>
			<td width="40%" class="paramlist_key">
				<label for="metakey"><?php echo JText::_('COM_SERMONSPEAKER_METAKEY'); ?>:</label>
			</td>
			<td class="paramlist_value">
				<textarea class="text_area" name="metakey" id="metakey" cols="30" rows="5"><?php echo $this->row->metakey; ?></textarea>
			</td>
		</tr>
		</table>
	<?php
	echo $pane->endPanel();
	echo $pane->endPane();
	?>
	</fieldset>
</td>
</tr>
<tr><td>
	<fieldset>
	<legend><?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?></legend>
	<table class="admintable">
		<tr>
			<td width="100" align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_ADDFILEPATH'); ?></td>
			<td>
				<input type="radio" name="seladdfile" value="1" onclick="enableElement(this.form.elements['addfile_txt'], this.form.elements['addfile_choice']);" checked>
				<input class="text_area" type="text" name="addfile_txt" id="addfile_txt" size="100" maxlength="250" value="<?php echo $this->row->addfile;?>" />
				<br>
				<input type="radio" name="seladdfile" value="2" onclick="enableElement(this.form.elements['addfile_choice'], this.form.elements['addfile_txt']);">
				<?php echo $this->lists['addfile_choice']; ?>
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key" title="<?php echo JText::_('COM_SERMONSPEAKER_ADDFILETEXT_DESC'); ?>">
				<?php echo JText::_('COM_SERMONSPEAKER_ADDFILETEXT_LABEL'); ?>
			</td>
			<td title="<?php echo JText::_('COM_SERMONSPEAKER_ADDFILETEXT_DESC'); ?>">
				<input class="text_area" type="text" name="addfileDesc" id="addfileDesc" size="80" maxlength="250" value="<?php echo $this->row->addfileDesc;?>" />
			</td>
		</tr>
	</table> 
	</fieldset>
</td></tr>
</table>
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" /> 
	<input type="hidden" name="created_on" value="<?php echo $this->row->created_on; ?>" /> 
	<input type="hidden" name="created_on" value="<?php echo $this->row->created_by; ?>" /> 
	<input type="hidden" name="option" value="com_sermonspeaker" /> 
	<input type="hidden" name="view" value="sermon" />
	<input type="hidden" name="controller" value="sermon" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_('form.token'); ?>
</form>