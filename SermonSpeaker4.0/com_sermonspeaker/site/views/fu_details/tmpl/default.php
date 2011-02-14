<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div id="ss-frup-container">
	<h1><?php echo JText::sprintf('COM_SERMONSPEAKER_FU_FILENAME', $this->filename); ?></h1>
	<div id="ss-frup-nav">
		<?php echo JText::_('COM_SERMONSPEAKER_FU_STEP'); ?> 1: <?php echo JText::_('COM_SERMONSPEAKER_FU_STEP1'); ?> | 
		<b><u><?php echo JText::_('COM_SERMONSPEAKER_FU_STEP'); ?> 2: <?php echo JText::_('COM_SERMONSPEAKER_FU_STEP2'); ?></u></b> | 
		<?php echo JText::_('COM_SERMONSPEAKER_FU_STEP'); ?> 3: <?php echo JText::_('COM_SERMONSPEAKER_FU_STEP3'); ?>
	</div>
	<div id="ss-frup-form">
		<form name="fu_createsermon" id="fu_createsermon" method="post" enctype="multipart/form-data" >
			<div class="key"><?php echo JText::_('JGLOBAL_TITLE'); ?></div>
			<div class="value"><input class="text_area" type="text" name="sermon_title" id="sermon_title" size="50" maxlength="250" value="<?php echo $this->id3['sermon_title'];?>" /></div>
			<div class="key"><?php echo JText::_('JFIELD_ALIAS_LABEL'); ?></div>
			<div class="value"><input class="text_area" type="text" name="alias" id="alias" size="50" maxlength="250" value="<?php echo $this->id3['alias']; ?>" /></div>
			<div class="key"><?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE'); ?></div>
			<div class="value">
				<input class="text_area" type="text" name="sermon_scripture" id="sermon_scripture" size="50" maxlength="250" value="<?php echo $this->id3['sermon_scripture'];?>" />
				<?php $tag = $this->params->get('plugin_tag'); ?>
				<img onClick="sendText(document.fu_createsermon.sermon_scripture,'<?php echo $tag[0]; ?>','<?php echo $tag[1]; ?>')" src='<?php echo JURI::root(); ?>/components/com_sermonspeaker/images/blue_tag.png'>
			</div>
			<div title='<?php echo JText::_('COM_SERMONSPEAKER_FU_DATE_DESC'); ?>'>
				<div align="right" class="key"><?php echo JText::_('COM_SERMONSPEAKER_SERMONDATE'); ?></div> 
				<div class="value">
					<input class="inputbox" type="text" name="sermon_date" id="sermon_date" size="25" maxlenght="20" value="<?php echo date('Y-m-d'); ?>" /> 
					<img class="calendar" src="templates/system/images/calendar.png" alt="calendar" id="showCalendar" /> 
					<script type="text/javascript">
						Calendar.setup( {
						inputField  : "sermon_date",
						ifFormat    : "%Y-%m-%d",
						button      : "showCalendar"
						} );
					</script>
				</div>
			</div>
			<div class="key"><?php echo JText::_('COM_SERMONSPEAKER_FU_SERMONNUMBER'); ?></div>
			<div class="value"><input class="text_area" type="text" name="sermon_number" id="sermon_number" size="10" maxlength="250" value="<?php echo $this->id3['sermon_number']; ?>" /></div>
			<div title="<?php echo JText::_('COM_SERMONSPEAKER_FU_SERMONTIME_DESC'); ?>">
				<div class="key"><?php echo JText::_('COM_SERMONSPEAKER_SERMONLENGTH'); ?></div> 
				<div class="value"><input class="text_area" type="text" name="sermon_time" id="sermon_time" size="10" maxlength="250" value="<?php echo $this->id3['sermon_time']; ?>" /></div>
			</div>
			<div class="key"><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER'); ?></div>
			<div class="value"><?php echo $this->lists['speaker_id']; ?></div>
			<div class="key"><?php echo JText::_('COM_SERMONSPEAKER_SERIES'); ?></div>
			<div class="value"><?php echo $this->lists['series_id']; ?></div>
			<div class="key"><?php echo JText::_('COM_SERMONSPEAKER_FU_NOTES'); ?></div>
			<div class="value"><?php echo $this->editor->display('notes', $this->id3['notes'], '100%', '200', '40', '10');	?></div>
			<div class="clr"></div>
			<div class="key"><?php echo JText::_('JCATEGORY'); ?></div>
			<div class="value"><?php echo $this->lists['catid']; ?></div>
			<div title="<?php echo JText::_('JFIELD_PUBLISHED_DESC'); ?>">
				<div class="key"> <?php echo JText::_('JPUBLISHED'); ?></div>
				<div class="value"><?php echo $this->lists['state']; ?></div>
			</div>
			<div title="<?php echo JText::_('COM_SERMONSPEAKER_FU_SERMONCAST_DESC'); ?>">
				<div class="key"> <?php echo JText::_('COM_SERMONSPEAKER_FU_SERMONCAST'); ?></div>
				<div class="value"><?php echo $this->lists['podcast']; ?></div>
			</div>
			<div class="key"><?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?></div>
			<div class="value">
				<input type="radio" name="seladdfile" value="1" onclick="enableElement(this.form.elements['addfile_txt'], this.form.elements['addfile_choice']);" checked>
				<input class="text_area" type="text" name="addfile_txt" id="addfile_txt" size="46" maxlength="250" value="" />
				<br>
				<input type="radio" name="seladdfile" value="2" onclick="enableElement(this.form.elements['addfile_choice'], this.form.elements['addfile_txt']);">
				<?php echo $this->lists['addfile_choice']; ?>
			</div>
			<div class="key"><?php echo JText::_('COM_SERMONSPEAKER_FU_ADDFILEDESC'); ?></div>
			<div class="value"><input class="text_area" type="text" name="addfileDesc" id="addfileDesc" size="50" maxlength="250" value="" /></div>
			<div>
				<input type="submit" value=" <?php echo JText::_('JSAVE'); ?> ">
				<input type="reset" value=" <?php echo JText::_('COM_SERMONSPEAKER_FU_RESET'); ?> ">
				<input type="hidden" name="filename" value="<?php echo $this->filename; ?>">
				<input type="hidden" name="submitted" value="true">
			</div>
		</form>
	</div>
	<?php echo SermonspeakerHelperSermonspeaker::fu_logoffbtn(); ?>
</div>