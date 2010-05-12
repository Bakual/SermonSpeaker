<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!-- Begin Data -->
<?php if ($this->params->get('popup_color')) { ?>
	<body bgcolor="#<?php echo $this->params->get('popup_color'); ?>">
<?php } ?>
<table style="text-align: left; width: 100%;" border="0" cellpadding="2" cellspacing="2">
	<tr>&nbsp;</tr>
	<tr>
		<td style="width: 50px;"></td>
	</tr>
	<tr>
		<td style="width: 20px;"></td>
		<td><h3><?php echo $this->row[0]->sermon_title; ?></h3></td>
	</tr>
	<tr>
		<td style="width: 20px;"></td>
		<td><?php SermonspeakerHelperSermonspeaker::insertPlayer($this->lnk); ?></td>
	</tr>
</table>