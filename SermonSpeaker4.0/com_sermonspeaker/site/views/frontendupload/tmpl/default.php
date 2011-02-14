<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div id="ss-frup-container">
	<h1><?php echo JText::_('COM_SERMONSPEAKER_FU_NEWSERMON'); ?></h1>
	<div id="ss-frup-nav">
		<b><u><?php echo JText::_('COM_SERMONSPEAKER_FU_STEP'); ?> 1: <?php echo JText::_('COM_SERMONSPEAKER_FU_STEP1'); ?></u></b> | 
		<?php echo JText::_('COM_SERMONSPEAKER_FU_STEP'); ?> 2: <?php echo JText::_('COM_SERMONSPEAKER_FU_STEP2'); ?> | 
		<?php echo JText::_('COM_SERMONSPEAKER_FU_STEP'); ?> 3: <?php echo JText::_('COM_SERMONSPEAKER_FU_STEP3'); ?>
	</div>
	<div id="ss-frup-form">
		<form action="<?php echo JURI::base(); ?>index.php?option=com_sermonspeaker&amp;task=file.upload&amp;tmpl=component&amp;<?php echo $this->session->getName().'='.$this->session->getId(); ?>&amp;<?php echo JUtility::getToken();?>=1&amp;format=json" id="uploadForm" name="uploadForm" method="post" enctype="multipart/form-data">
			<fieldset id="uploadform">
				<legend><?php echo JText::_('COM_SERMONSPEAKER_FU_UPLOAD'); ?></legend>
				<fieldset id="upload-noflash" class="actions">
					<label for="upload-file" class="hidelabeltxt"><?php echo JText::_('COM_SERMONSPEAKER_FU_UPLOAD_FILEUPLOAD_FILE'); ?></label>
					<input type="file" id="upload-file" name="Filedata" />
					<label for="upload-submit" class="hidelabeltxt"><?php echo JText::_('COM_SERMONSPEAKER_FU_START_UPLOAD'); ?></label>
					<input type="submit" id="upload-submit" value="<?php echo JText::_('COM_SERMONSPEAKER_FU_START_UPLOAD'); ?>"/>
				</fieldset>
				<div id="upload-flash" class="hide">
					<ul>
						<li><a href="#" id="upload-browse"><?php echo JText::_('COM_SERMONSPEAKER_FU_BROWSE_FILES'); ?></a></li>
					</ul>
					<div class="clr"> </div>
					<p class="overall-title"></p>
					<?php echo JHTML::_('image','media/bar.gif', '', array('class' => 'progress overall-progress'), true); ?>
					<div class="clr"> </div>
					<div class="ss-hide">
						<p class="current-title"></p>
						<?php echo JHTML::_('image','media/bar.gif', '', array('class' => 'progress current-progress'), true); ?>
					</div>
					<p class="current-text"></p>
				</div>
				<ul class="upload-queue" id="upload-queue">
					<li style="display:none;"></li>
				</ul>
				<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_sermonspeaker'); ?>" />
				<input type="hidden" name="format" value="html" />
			</fieldset>
		</form>
	</div>
	<?php echo SermonspeakerHelperSermonspeaker::fu_logoffbtn(); ?>
</div>