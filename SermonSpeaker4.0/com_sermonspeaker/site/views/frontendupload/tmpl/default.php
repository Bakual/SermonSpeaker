<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
$uri = JURI::getInstance();
$uri->delVar('file');
$self = $uri->toString();
?>
<div id="ss-frup-container">
	<h1><?php echo JText::_('COM_SERMONSPEAKER_FU_NEWSERMON'); ?></h1>
	<div id="ss-frup-form">
		<form action="<?php echo JURI::base(); ?>index.php?option=com_sermonspeaker&amp;task=file.upload&amp;tmpl=component&amp;<?php echo $this->session->getName().'='.$this->session->getId(); ?>&amp;<?php echo JUtility::getToken();?>=1" id="uploadForm" name="uploadForm" method="post" enctype="multipart/form-data">
			<fieldset id="upload-noflash" class="actions">
				<legend><?php echo JText::_('COM_SERMONSPEAKER_FU_STEP1'); ?></legend>
				<label for="upload-file" class="label"><?php echo JText::_('COM_SERMONSPEAKER_FU_UPLOAD'); ?></label>
				<input type="file" size="50" id="upload-file" name="Filedata" /><br />
				<input type="submit" class="submit" value="<?php echo JText::_('COM_SERMONSPEAKER_FU_START_UPLOAD'); ?>" />
				<input type="hidden" name="return-url" value="<?php echo base64_encode($self); ?>" />
			</fieldset>
		</form>
		<form name="fu_createsermon" id="fu_createsermon" method="post" enctype="multipart/form-data" >
			<label for="sermon_title"><?php echo JText::_('JGLOBAL_TITLE'); ?></label>
			<input class="text_area" type="text" name="sermon_title" id="sermon_title" size="50" maxlength="250" value="<?php echo $this->data['sermon_title'];?>" /><br />
			<label for="alias"><?php echo JText::_('JFIELD_ALIAS_LABEL'); ?></label>
			<input class="text_area" type="text" name="alias" id="alias" size="50" maxlength="250" value="<?php echo $this->data['alias']; ?>" /><br />
			<label for="audiofile"><?php echo JText::_('COM_SERMONSPEAKER_FIELD_AUDIOFILE_LABEL'); ?></label>
			<input class="text_area" type="text" name="audiofile" id="audiofile" size="50" maxlength="250" value="<?php echo $this->data['audiofile']; ?>" />
				<img onClick="window.location.href='<?php echo $self; ?>&amp;type=audio&amp;file='+document.fu_createsermon.audiofile.value;" src='<?php echo JURI::root(); ?>/components/com_sermonspeaker/images/find.png' alt='lookup ID3' title='lookup ID3'><br />
			<div id="upload-flash" class="hide">
				<button id="upload-browse" type="button"><img src="<?php echo JURI::root(); ?>/components/com_sermonspeaker/images/upload.png" /></button>
				<div onClick="this.style.display='none';" id="status-bar" class="ss-hide intend">
					<p class="overall-title"></p>
					<?php echo JHTML::_('image','media/bar.gif', '', array('class' => 'progress overall-progress'), true); ?>
					<div class="clr"> </div>
					<div class="ss-hide">
						<p class="current-title"></p>
						<?php echo JHTML::_('image','media/bar.gif', '', array('class' => 'progress current-progress'), true); ?>
					</div>
					<p class="current-text"></p>
				</div>
			</div>
			<ul class="upload-queue ss-hide" id="upload-queue">
				<li style="display:none;"></li>
			</ul>
			<label for="sermon_scripture"><?php echo JText::_('COM_SERMONSPEAKER_SCRIPTURE'); ?></label>
			<input class="text_area" type="text" name="sermon_scripture" id="sermon_scripture" size="50" maxlength="250" value="<?php echo $this->data['sermon_scripture'];?>" />
				<?php $tag = $this->params->get('plugin_tag'); ?>
				<img onClick="sendText(document.fu_createsermon.sermon_scripture,'<?php echo $tag[0]; ?>','<?php echo $tag[1]; ?>')" src='<?php echo JURI::root(); ?>/components/com_sermonspeaker/images/blue_tag.png'><br />
			<div title='<?php echo JText::_('COM_SERMONSPEAKER_FU_DATE_DESC'); ?>'>
				<label for="sermon_date"><?php echo JText::_('COM_SERMONSPEAKER_SERMONDATE'); ?></label>
				<input class="inputbox" type="text" name="sermon_date" id="sermon_date" size="25" maxlenght="20" value="<?php echo date('Y-m-d'); ?>" />
					<img class="calendar" src="templates/system/images/calendar.png" alt="calendar" id="showCalendar" /> 
					<script type="text/javascript">
						Calendar.setup( {
						inputField  : "sermon_date",
						ifFormat    : "%Y-%m-%d",
						button      : "showCalendar"
						} );
					</script><br />
			</div>
			<label for="sermon_number"><?php echo JText::_('COM_SERMONSPEAKER_FU_SERMONNUMBER'); ?></label>
			<input class="text_area" type="text" name="sermon_number" id="sermon_number" size="10" maxlength="250" value="<?php echo $this->data['sermon_number']; ?>" /><br />
			<div title="<?php echo JText::_('COM_SERMONSPEAKER_FU_SERMONTIME_DESC'); ?>">
				<label for="sermon_time"><?php echo JText::_('COM_SERMONSPEAKER_SERMONLENGTH'); ?></label>
				<input class="text_area" type="text" name="sermon_time" id="sermon_time" size="10" maxlength="250" value="<?php echo $this->data['sermon_time']; ?>" /><br />
			</div>
			<label for="speaker_id"><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER'); ?></label>
			<?php echo $this->lists['speaker_id']; ?><br />
			<label for="series_id"><?php echo JText::_('COM_SERMONSPEAKER_SERIES'); ?></label>
			<?php echo $this->lists['series_id']; ?><br />
			<label for="notes"><?php echo JText::_('COM_SERMONSPEAKER_FU_NOTES'); ?></label>
			<?php echo $this->editor->display('notes', $this->data['notes'], '500', '200', '40', '10'); ?><br />
			<br />
			<label for="catid"><?php echo JText::_('JCATEGORY'); ?></label>
			<?php echo $this->lists['catid']; ?><br />
			<div class="radio" title="<?php echo JText::_('JFIELD_PUBLISHED_DESC'); ?>">
				<div class="label"><?php echo JText::_('JPUBLISHED'); ?></div>
				<?php echo $this->lists['state']; ?><br />
			</div>
			<div class="radio" title="<?php echo JText::_('COM_SERMONSPEAKER_FU_SERMONCAST_DESC'); ?>">
				<div class="label"><?php echo JText::_('COM_SERMONSPEAKER_FU_SERMONCAST'); ?></div>
				<?php echo $this->lists['podcast']; ?><br />
			</div>
			<label for="addfile_txt"><?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?></label>
			<input type="radio" class="radio" name="seladdfile" value="1" onclick="enableElement(this.form.elements['addfile_txt'], this.form.elements['addfile_choice']);" checked>
				<input class="text_area" type="text" name="addfile_txt" id="addfile_txt" size="46" maxlength="250" value="" /><br />
			<input type="radio" class="intend" name="seladdfile" value="2" onclick="enableElement(this.form.elements['addfile_choice'], this.form.elements['addfile_txt']);">
				<?php echo $this->lists['addfile_choice']; ?><br />
			<label for="addfileDesc"><?php echo JText::_('COM_SERMONSPEAKER_FU_ADDFILEDESC'); ?></label>
			<input class="text_area" type="text" name="addfileDesc" id="addfileDesc" size="50" maxlength="250" value="" /><br />
			<div>
				<input type="submit" class="submit" value="<?php echo JText::_('JSAVE'); ?>">
				<input type="reset" value=" <?php echo JText::_('COM_SERMONSPEAKER_FU_RESET'); ?> ">
				<input type="hidden" name="filename" value="<?php echo $this->filename; ?>">
				<input type="hidden" name="submitted" value="true">
			</div>
		</form>
	</div>
	<?php echo SermonspeakerHelperSermonspeaker::fu_logoffbtn(); ?>
</div>