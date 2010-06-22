<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
/* JS Script für Joomla Sortierung */
JFactory::getDocument()->addScriptDeclaration( "
	function tableOrdering( order, dir, task ) {
		var form = document.adminForm;
		form.filter_order.value = order;
		form.filter_order_Dir.value = dir;
		form.submit( task );
	}"
);
?>
<table width="100%" cellpadding="2" cellspacing="0">
	<tr class="componentheading">
		<th align="left" valign="bottom"><?php echo $this->title ?></th>
	</tr>
</table>
<!-- Begin Data - Speaker -->
<table border='0' cellpadding="2" cellspacing="0" width="100%">
	<tr>
		<?php if ($this->row->pic) { ?>
			<td valign="middle" align="center" width='30%'>
				<img src="<?php echo $this->row->pic; ?>" border="0" alt="" />
			</td>
		<?php } ?>
		<td align="left" valign="top">
		<?php
		if ($this->row->website && $this->row->website != "http://") { ?>
			<a href="<?php echo $this->row->website; ?>" target="_blank" title="<?php echo JText::_('WEB_LINK_DESCRIPTION'); ?>"><?php echo JText::_('WEB_LINK_TAG').' '.$this->row->name; ?></a>
		<?php }
		if ($this->row->bio || $this->row->intro) { ?>
			<p><b><?php echo JText::_('BIO'); ?>:</b>
			<?php if($this->params->get('speaker_intro')) {
				echo $this->row->intro;
			}
			echo $this->row->bio; ?>
			</p>
		<?php } ?>
		</td>
	</tr>
</table>
<p></p>
<form method="post" id="adminForm" name="adminForm">
<table class="adminlist" cellpadding="2" cellspacing="2" width="100%">
<!-- Tabellenkopf mit Sortierlinks erstellen -->
	<thead>
		<tr>
			<?php if ($this->params->get('client_col_sermon_number')) { ?>
				<th width="5%" align="left"><?php echo JHTML::_('grid.sort', 'SERMONNUMBER', 'sermon_number', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<?php } ?>
			<th align="left"><?php echo JHTML::_('grid.sort', 'SERMONNAME', 'sermon_title', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<?php if ($this->params->get('client_col_sermon_scripture_reference')) { ?>
				<th align="left"><?php echo JHTML::_('grid.sort', 'SCRIPTURE', 'sermon_scripture', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<?php } ?>
			<?php if ($this->params->get('client_col_sermon_date')) { ?>
				<th align="left">
					<?php echo JHTML::_('grid.sort', 'SERMON_DATE', 'sermon_date', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
			<?php }
			if ($this->params->get('client_col_sermon_time')) { ?>
			<th align="center"><?php echo JHTML::_('grid.sort', 'SERMONTIME', 'sermon_time', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<?php }
			if ($this->params->get('client_col_sermon_addfile')) { ?>
			<th align="left"><?php echo JHTML::_('grid.sort', 'ADDFILE', 'addfileDesc', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<?php } ?>
		</tr>
	</thead>
<!-- Begin Data -->
	<?php if( $this->sermons ) {
	$i = 0;
		foreach( $this->sermons as $sermon ) {
			echo "<tr class=\"row$i\">";
			$i = 1 - $i;
			if( $this->params->get('client_col_sermon_number')){
				echo "<td align=\"left\" valign=\"middle\" > $sermon->sermon_number </td>";
			} ?>
			<td align="left">
				&nbsp;<a href="<?php echo JRoute::_("index.php?view=sermon&id=$sermon->slug"); ?>"><img title="<?php echo JText::_('PLAYTOPLAY'); ?>" src="<?php echo JURI::root().'components/com_sermonspeaker/images/play.gif'; ?>" width='16' height='16' border='0' align='top' alt="" /></a>
				<a title="<?php echo JText::_('SINGLE_SERMON_HOOVER_TAG'); ?>" href="<?php echo JRoute::_("index.php?view=sermon&id=$sermon->slug"); ?>">
					<?php echo $sermon->sermon_title; ?>
				</a>
			</td>
			<?php
			if( $this->params->get('client_col_sermon_scripture_reference')){
				echo "<td align=\"left\" valign=\"middle\" >$sermon->sermon_scripture</td>";
			}
			if( $this->params->get('client_col_sermon_date')){
				echo "<td align=\"left\" valign=\"middle\">".JHTML::date($sermon->sermon_date,JText::_('DATE_FORMAT_JS1'),0)."</td>";
			}
			if( $this->params->get('client_col_sermon_time')){
				echo "<td align=\"center\" valign=\"middle\">".SermonspeakerHelperSermonspeaker::insertTime($sermon->sermon_time)."</td>";
			}
			if ($this->params->get('client_col_sermon_addfile')) { ?>
				<td><?php echo SermonspeakerHelperSermonspeaker::insertAddfile($sermon->addfile, $sermon->addfileDesc); ?></td>
			<?php }
			echo "</tr>";
		} // end of foreach
	} ?>
</table>
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
