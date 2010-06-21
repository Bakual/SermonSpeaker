<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<form name="fu_createsermon" method="post" enctype="multipart/form-data" >
<table border="0">
	<tr>
		<td width ="50">&#160;</td>
		<td colspan="3"><h1><?php echo JText::_('FILENAME')." \"".$this->file."\" ".JText::_('FU_UPLOADOK'); ?></h1></td>
	</tr>
	<tr>
		<td width ="50">&#160;</td>
		<td colspan="3"><b><?php echo JText::_('FU_STEP'); ?> 2 : </b><?php echo JText::_('FU_STEP2'); ?></td>
	</tr>
	<tr>
		<td colspan ="4">&#160;</td>
	</tr>
	<tr>
		<td width ="50">&#160;</td>
		<td align="right" class="key"> <?php echo JText::_('SERMONTITLE'); ?> </td> 
		<td> &nbsp; <input class="text_area" type="text" name="sermon_title" id="sermon_title" size="50" maxlength="250" value="<?php echo $this->id3['title'];?>" /> </td>
		<td>&#160;</td>
	</tr>
	<tr>
		<td width ="50">&#160;</td>
		<td align="right" class="key"> <?php echo JText::_('SCRIPTURE'); ?> </td> 
		<td> &nbsp; <input class="text_area" type="text" name="sermon_scripture" id="sermon_scripture" size="50" maxlength="250" value="<?php echo $this->id3['ref'];?>" /> </td>
		<td>&#160;</td>
	</tr>
	<tr>
		<td width ="50">&#160;</td>
		<td align="right" class="key"> <?php echo JText::_('SERMON_DATE'); ?> </td> 
		<td> &nbsp;
			<input class="inputbox" type="text" name="sermon_date" id="sermon_date" size="25" maxlenght="20" value="<?php echo date('Y-m-d'); ?>" /> 
			<img class="calendar" src="templates/system/images/calendar.png" alt="calendar" id="showCalendar" /> 
			<script type="text/javascript">
				Calendar.setup( {
				inputField  : "sermon_date",
				ifFormat    : "%Y-%m-%d",
				button      : "showCalendar"
				} );
			</script>
		</td>
		<td align="left"><?php echo JText::_('FU_DATE_DESC'); ?></td>
	</tr>
	<tr>
		<td width ="50">&#160;</td>
		<td align="right" class="key"> <?php echo JText::_('FU_SERMONNUMBER'); ?> </td> 
		<td> &nbsp; <input class="text_area" type="text" name="sermon_number" id="sermon_number" size="10" maxlength="250" value="<?php echo $this->id3['number']; ?>" /> </td>
		<td>&#160;</td>
	</tr>
	<tr>
		<td width ="50">&#160;</td>
		<td align="right" class="key"> <?php echo JText::_('SERMONTIME'); ?> </td> 
		<td> &nbsp; <input class="text_area" type="text" name="sermon_time" id="sermon_time" size="10" maxlength="250" value="<?php echo $this->id3['time']; ?>" /> </td>
		<td align="left"><?php echo JText::_('FU_SERMONTIME_DESC'); ?></td>
	</tr>
	<tr>
		<td width ="50">&#160;</td>
		<td align="right" class="key"> <?php echo JText::_('SPEAKER'); ?> </td> 
		<td> &nbsp; <?php echo $this->lists['speaker_id']; ?> </td>
		<td>&#160;</td>
	</tr>
	<tr>
		<td width ="50">&#160;</td>
		<td align="right" class="key"> <?php echo JText::_('SERIES'); ?> </td> 
		<td> &nbsp; <?php echo $this->lists['series_id']; ?> </td>
		<td>&#160;</td>
	</tr>
	<tr>
		<td width ="50">&#160;</td>
		<td align="right" class="key"> <?php echo JText::_('NOTES'); ?> </td> 
		<td> &nbsp; <?php echo $this->editor->display('notes', $this->id3['notes'], '100%', '200', '40', '10');	?> </td>
		<td>&#160;</td>
	</tr>
	<tr>
		<td width ="50">&#160;</td>
		<td align="right" class="key"> <?php echo JText::_('published'); ?> </td> 
		<td> &nbsp; <?php echo $this->lists['published']; ?> </td>
		<td align="left"><?php echo JText::_('FU_PUBLISHED_DESC'); ?></td>
	</tr>
	<tr>
		<td width ="50">&#160;</td>
		<td align="right" class="key"> <?php echo JText::_('SERMONCAST'); ?> </td> 
		<td> &nbsp; <?php echo $this->lists['podcast']; ?> </td>
		<td align="left"><?php echo JText::_('FU_SERMONCAST_DESC'); ?></td>
	</tr>
	<tr>
		<td colspan ="4">&#160;</td>
	</tr>
	<tr>
		<td width ="50">&#160;</td>
		<td colspan="2">
			<input type="submit" value=" <?php echo JText::_('FU_SAVE'); ?> ">&nbsp;
			<input type="reset" value=" <?php echo JText::_('FU_RESET'); ?> ">
		</td>
		<input type="hidden" name="filename" value="<?php echo $this->file; ?>">
		<input type="hidden" name="submitted" value="true">
		<td>&#160;</td>
	</tr>
	<tr>
		<td width ="50"></td>
		<td><br/>&nbsp;<br/></td>
		<td colspan="2"></td>
	</tr>
	<tr>
		<td width ="50"></td>
		<td><?php echo SermonspeakerHelperSermonspeaker::fu_logoffbtn(); ?></td>
		<td colspan="2"></td>
	</tr>
</table>
</form>