<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<table border="0">
	<tr>
		<td width ="50"></td>
		<td><h1><?php echo JText::_('FU_FAILED'); ?></h1></td>
	</tr>
	<tr>
		<td width ="50"></td>
		<td><b><?php echo JText::_('COM_SERMONSPEAKER_FU_ERROR_EXISTS')."</b>"; ?></b></td>
	</tr>
	<tr>
		<td width ="50"></td>
		<td><br/><a href="<?php echo JURI::root().'index.php?option=com_sermonspeaker&view=fu_step_1'; ?>"><?php echo JText::_('COM_SERMONSPEAKER_FU_ANOTHER'); ?></a></td>
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