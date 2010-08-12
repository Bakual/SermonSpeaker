<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<table border="0">
	<tr>
		<td width ="50">&#160;</td>
		<td colspan="3"><h1><?php echo JText::_('COM_SERMONSPEAKER_FU_UPSAVEDOK'); ?></h1></td>
	</tr>
	<tr>
		<td width ="50">&#160;</td>
		<td colspan="3"><b><?php echo JText::_('COM_SERMONSPEAKER_FU_STEP'); ?> 3 : </b><?php echo JText::_('COM_SERMONSPEAKER_FU_STEP3'); ?></td>
	</tr>
	<tr>
		<td colspan ="4">&#160;</td>
	</tr>
	<tr>
		<td width ="50"></td>
		<td>
			<?php echo "<FORM><INPUT TYPE=\"BUTTON\" VALUE=\"".JText::_('COM_SERMONSPEAKER_FU_ANOTHER')."\" ONCLICK=\"window.location.href='index.php?option=com_sermonspeaker&view=fu_step_1'\"> </FORM>";
			echo "&nbsp;&nbsp;";
			echo SermonspeakerHelperSermonspeaker::fu_logoffbtn(); ?>
		</td>
	</tr>
</table>
