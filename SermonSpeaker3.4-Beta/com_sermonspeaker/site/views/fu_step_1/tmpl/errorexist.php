<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<table border="0">
	<tr>
		<td width ="50"></td>
		<td><h1><?php echo $lang->fu_failed; ?></h1></td>
	</tr>
	<tr>
		<td width ="50"></td>
		<td><b><?php echo JText::_('FU_EXISTS')."</b>"; ?></b></td>
	</tr>
	<tr>
		<td width ="50"></td>
		<td><br/><a href="index.php?option=com_sermonspeaker&view=fu_step_1"><?php echo JText::_('FU_ANOTHER'); ?></a></td>
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