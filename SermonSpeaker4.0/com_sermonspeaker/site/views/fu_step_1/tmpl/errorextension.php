<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<table border="0">
	<tr>
		<td width ="50"></td>
		<td><h1><?php echo JText::_('COM_SERMONSPEAKER_FU_ERROR_EXT'); ?></h1></td>
	</tr>
	<tr>
		<td width ="50"></td>
		<td><b><a href="index.php?option=com_sermonspeaker&view=fu_step_1"><?php echo JText::_('COM_SERMONSPEAKER_FU_CONT'); ?></a></b></td>
	</tr>
	<tr>
		<td colspan ="4">&#160;</td>
	</tr>
	<tr>
		<td width ="50"></td>
		<td><br/>&nbsp;<br/><td>
	</tr>
	<tr>
		<td width ="50"></td>
		<td><?php echo SermonspeakerHelperSermonspeaker::fu_logoffbtn(); ?></td>
	</tr>
</table>