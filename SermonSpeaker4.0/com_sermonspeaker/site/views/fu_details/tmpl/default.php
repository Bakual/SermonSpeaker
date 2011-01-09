<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<form name="fu_createsermon" id="fu_createsermon" method="post" enctype="multipart/form-data" >
<table border='0'>
	<colgroup>
		<col width='100' />
		<col width='5' />
		<col />
	</colgroup>
	<tr>
		<td colspan="3"><h1><?php echo JText::sprintf('COM_SERMONSPEAKER_FU_FILENAME', $this->filename); ?></h1></td>
	</tr>
	<tr>
		<td colspan="3"><b><?php echo JText::_('COM_SERMONSPEAKER_FU_STEP'); ?> 2 : </b><?php echo JText::_('COM_SERMONSPEAKER_FU_STEP2'); ?></td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_FU_SERMONTITLE'); ?></td>
		<td></td>
		<td><input class="text_area" type="text" name="sermon_title" id="sermon_title" size="50" maxlength="250" value="<?php echo $this->id3['sermon_title'];?>" /></td>
	</tr>
	<tr>
		<td align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_FU_ALIAS'); ?></td>
		<td></td>
		<td><input class="text_area" type="text" name="alias" id="alias" size="50" maxlength="250" value="<?php echo $this->id3['alias']; ?>" /></td>
	</tr>
	<tr>
		<td align="right" class="key"> <?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE'); ?> </td> 
		<td></td>
		<td>
			<input class="text_area" type="text" name="sermon_scripture" id="sermon_scripture" size="50" maxlength="250" value="<?php echo $this->id3['sermon_scripture'];?>" />
			<?php if (JPluginHelper::isEnabled('content', 'biblelinkxt')){
				$biblelinkxt = 'title="insert Biblelink tag" alt="insert Biblelink tag"';
			} else {
				$biblelinkxt = 'class="transparent" title="insert Biblelink tag, Plugin not enabled" alt="insert Biblelink tag, Plugin not enabled"';
			}
			if (JPluginHelper::isEnabled('content', 'scripturelinks')){
				$scripturelink = 'title="insert ScriptureLink tag" alt="insert ScriptureLink tag"';
			} else {
				$scripturelink = 'class="transparent" title="insert ScriptureLink tag, Plugin not enabled" alt="insert ScriptureLink tag, Plugin not enabled"';
			} ?>
			<img <?php echo $biblelinkxt; ?> onClick="sendText(document.fu_createsermon.sermon_scripture,'{bib=','}')" src='<?php echo JURI::root(); ?>/components/com_sermonspeaker/images/blue_tag.png'>
			<img <?php echo $scripturelink; ?> onClick="sendText(document.fu_createsermon.sermon_scripture,'{bible}','{/bible}')" src='<?php echo JURI::root(); ?>/components/com_sermonspeaker/images/green_tag.png'>
		</td>
	</tr>
	<tr title='<?php echo JText::_('COM_SERMONSPEAKER_FU_DATE_DESC'); ?>'>
		<td align='right' class='key'> <?php echo JText::_('COM_SERMONSPEAKER_SERMONDATE'); ?> </td> 
		<td></td>
		<td>
			<input class="inputbox" type="text" name="sermon_date" id="sermon_date" size="25" maxlenght="20" value="<?php echo date('Y-m-d'); ?>" /> 
			<img class="calendar" src="templates/system/images/calendar.png" alt="calendar" id="showCalendar" /> 
			<script type="text/javascript">
				Calendar.setup( {
				inputField  : "sermon_date",
				ifFormat    : "%Y-%m-%d",
				button      : "showCalendar"
				} );
			</script>
		</td>
	</tr>
	<tr>
		<td align="right" class="key"> <?php echo JText::_('COM_SERMONSPEAKER_FU_SERMONNUMBER'); ?> </td> 
		<td></td>
		<td><input class="text_area" type="text" name="sermon_number" id="sermon_number" size="10" maxlength="250" value="<?php echo $this->id3['sermon_number']; ?>" /> </td>
	</tr>
	<tr title='<?php echo JText::_('COM_SERMONSPEAKER_FU_SERMONTIME_DESC'); ?>'>
		<td align="right" class="key"> <?php echo JText::_('COM_SERMONSPEAKER_SERMONLENGTH'); ?> </td> 
		<td></td>
		<td><input class="text_area" type="text" name="sermon_time" id="sermon_time" size="10" maxlength="250" value="<?php echo $this->id3['sermon_time']; ?>" /> </td>
	</tr>
	<tr>
		<td align="right" class="key"> <?php echo JText::_('COM_SERMONSPEAKER_SPEAKER'); ?> </td> 
		<td></td>
		<td><?php echo $this->lists['speaker_id']; ?> </td>
	</tr>
	<tr>
		<td align="right" class="key"> <?php echo JText::_('COM_SERMONSPEAKER_SERIES'); ?> </td> 
		<td></td>
		<td><?php echo $this->lists['series_id']; ?> </td>
	</tr>
	<tr>
		<td align="right" class="key"> <?php echo JText::_('COM_SERMONSPEAKER_FU_NOTES'); ?> </td> 
		<td></td>
		<td><?php echo $this->editor->display('notes', $this->id3['notes'], '100%', '200', '40', '10');	?> </td>
	</tr>
	<tr>
		<td align="right" class="key"> <?php echo JText::_('JCATEGORY'); ?> </td>
		<td></td>
		<td><?php echo $this->lists['catid']; ?></td>
	</tr>
	<tr title='<?php echo JText::_('COM_SERMONSPEAKER_FU_PUBLISHED_DESC'); ?>'>
		<td align="right" class="key"> <?php echo JText::_('JGLOBAL_STATE'); ?> </td> 
		<td></td>
		<td><?php echo $this->lists['state']; ?> </td>
	</tr>
	<tr title='<?php echo JText::_('COM_SERMONSPEAKER_FU_SERMONCAST_DESC'); ?>'>
		<td align="right" class="key"> <?php echo JText::_('COM_SERMONSPEAKER_FU_SERMONCAST'); ?> </td> 
		<td></td>
		<td><?php echo $this->lists['podcast']; ?> </td>
	</tr>
	<tr>
		<td align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?></td>
		<td></td>
		<td>
			<input type="radio" name="seladdfile" value="1" onclick="enableElement(this.form.elements['addfile_txt'], this.form.elements['addfile_choice']);" checked>
			<input class="text_area" type="text" name="addfile_txt" id="addfile_txt" size="46" maxlength="250" value="" />
			<br>
			<input type="radio" name="seladdfile" value="2" onclick="enableElement(this.form.elements['addfile_choice'], this.form.elements['addfile_txt']);">
			<?php echo $this->lists['addfile_choice']; ?>
		</td>
	</tr>
	<tr>
		<td align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_FU_ADDFILEDESC'); ?></td>
		<td></td>
		<td><input class="text_area" type="text" name="addfileDesc" id="addfileDesc" size="50" maxlength="250" value="" /></td>
	</tr>
	<tr>
		<td colspan='3'>&nbsp;</td>
	</tr>
	<tr>
		<td colspan='3'>
			<input type="submit" value=" <?php echo JText::_('COM_SERMONSPEAKER_FU_SAVE'); ?> ">
			<input type="reset" value=" <?php echo JText::_('COM_SERMONSPEAKER_FU_RESET'); ?> ">
			<input type="hidden" name="filename" value="<?php echo $this->filename; ?>">
			<input type="hidden" name="submitted" value="true">
		</td>
	</tr>
	<tr>
		<td colspan='3'><br/><br/></td>
	</tr>
	<tr>
		<td colspan='3'><?php echo SermonspeakerHelperSermonspeaker::fu_logoffbtn(); ?></td>
	</tr>
</table>
</form>