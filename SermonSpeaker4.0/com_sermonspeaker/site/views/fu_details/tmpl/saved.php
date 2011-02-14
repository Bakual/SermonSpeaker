<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<div id="ss-frup-container">
	<h1><?php echo JText::_('COM_SERMONSPEAKER_FU_UPSAVEDOK'); ?></h1>
	<div id="ss-frup-nav">
		<?php echo JText::_('COM_SERMONSPEAKER_FU_STEP'); ?> 1: <?php echo JText::_('COM_SERMONSPEAKER_FU_STEP1'); ?> | 
		<?php echo JText::_('COM_SERMONSPEAKER_FU_STEP'); ?> 2: <?php echo JText::_('COM_SERMONSPEAKER_FU_STEP2'); ?> | 
		<b><u><?php echo JText::_('COM_SERMONSPEAKER_FU_STEP'); ?> 3: <?php echo JText::_('COM_SERMONSPEAKER_FU_STEP3'); ?></u></b>
	</div>
	<form><input type="button" value="<?php echo JText::_('COM_SERMONSPEAKER_FU_ANOTHER'); ?>" onclick="window.location.href='index.php?option=com_sermonspeaker&view=frontendupload'"> </form>
	<?php echo SermonspeakerHelperSermonspeaker::fu_logoffbtn(); ?>
</div>
