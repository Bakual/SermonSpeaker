<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<table border="0">
	<tr>
		<td width ="50"></td>
		<td>
			<h1><?php echo JText::_('COM_SERMONSPEAKER_FU_WELCOME'); ?></h1>
			<h4><?php echo JText::_('COM_SERMONSPEAKER_FU_PLEASELOGIN'); ?></h4>
		</td>
	</tr>
	<tr>
		<td width ="50"></td>
		<td>
			<form name="fu_login" method="post" enctype="multipart/form-data" ><?php echo JText::_('COM_SERMONSPEAKER_FU_PWD'); ?>:
				<input class="inputbox" type="password" name="pwd" size="40">
				<br/>&nbsp;<br/>
				<input type="submit" value=" <?php echo JText::_('COM_SERMONSPEAKER_FU_LOGIN'); ?> ">&nbsp;
				<input type="reset" value=" <?php echo JText::_('COM_SERMONSPEAKER_FU_RESET'); ?> ">
			</form>
			<br/>&nbsp;<br/>
		<td>
	</tr>
</table>