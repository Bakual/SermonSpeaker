<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<table border="0">
	<tr>
		<td width ="50"></td>
		<td><h1><?php echo JText::_('COM_SERMONSPEAKER_FU_NEWSERMON'); ?></h1></td>
	</tr>
	<tr>
		<td width ="50"></td>
		<td><b><?php echo JText::_('COM_SERMONSPEAKER_FU_STEP'); ?> 1 : </b><?php echo JText::_('COM_SERMONSPEAKER_FU_STEP1'); ?></td>
	</tr>
	<tr>
		<td colspan ="4">&#160;</td>
	</tr>
	<tr>
		<td width ="50"></td>
		<td align='left'>
			<form name="fu_uploader" method="post" enctype="multipart/form-data" >
				<?php echo JText::_('COM_SERMONSPEAKER_FU_UPLOAD'); ?>
				<input class="inputbox" type="file" name="upload" id="upload" size="60">
				<br>
				<input type="submit" name="submitit" onclick="submitOnce(fu_uploader);return false;" value=" <?php echo JText::_('COM_SERMONSPEAKER_FU_SAVE'); ?> ">&nbsp;
				<input type="reset" value=" <?php echo JText::_('COM_SERMONSPEAKER_FU_RESET'); ?> ">
			</form>
			<br/>&nbsp;<br/>
		<td>
	</tr>
	<tr>
		<td width ="50"></td>
		<td><?php echo SermonspeakerHelperSermonspeaker::fu_logoffbtn(); ?></td>
	</tr>
</table> 