<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML::_('behavior.tooltip');
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
		<th align="left" valign="bottom"><?php echo JText::_('SINGLESERIES').": ".$this->serie->series_title; ?></th>
	</tr>
</table>
<table cellpadding="2" cellspacing="10">
<tr>
	<th><?php if ($this->serie->avatar != "") {
			echo "<img src='".SermonspeakerHelperSermonspeaker::makelink($this->serie->avatar)."' >";
        } ?>
	</th>
	<th align="left"><?php echo $this->serie->series_description; ?></th>
</tr>
</table>
<p></p>
<div class="Pages">
	<div class="Paginator">
		<?php echo $this->pagination->getResultsCounter(); ?><br>
		<?php if ($this->pagination->getPagesCounter()) echo $this->pagination->getPagesCounter()."<br>"; ?>
		<?php if ($this->pagination->getPagesLinks()) echo $this->pagination->getPagesLinks()."<br>"; ?>
	</div>
</div>

<hr style="width: 100%; height: 2px;">

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
		<th align="left"><?php echo JHTML::_('grid.sort', 'SPEAKER', 'name', $this->lists['order_Dir'], $this->lists['order']); ?></th>
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
	<?php
	if($this->rows) {
		$i = 0;
		foreach($this->rows as $row) { 
			echo "<tr class=\"row$i\">\n"; 
			$i = 1 - $i;
			if( $this->params->get('client_col_sermon_number')){ ?>
				<td><?php echo $row->sermon_number; ?></td>
			<?php } ?>
				<td align="left">
					&nbsp;<a href="<?php echo JRoute::_("index.php?view=sermon&id=$row->slug"); ?>"><img title="<?php echo JText::_('PLAYTOPLAY'); ?>" src="<?php echo JURI::root().'components/com_sermonspeaker/images/play.gif'; ?>" width='16' height='16' border='0' align='top' alt="" /></a>
					<a title="<?php echo JText::_('SINGLE_SERMON_HOOVER_TAG'); ?>" href="<?php echo JRoute::_("index.php?view=sermon&id=$row->slug"); ?>">
						<?php echo $row->sermon_title; ?>
					</a>
				</td>
				<?php if( $this->params->get('client_col_sermon_scripture_reference')){ ?>		
					<td align="left" valign="top" ><?php echo $row->sermon_scripture; ?></td>
				<?php } ?>
				<td align="left" valign="top">
					<?php echo SermonspeakerHelperSermonSpeaker::SpeakerTooltip($row->s_id, $row->pic, $row->name); ?>
				</td>
				<?php if( $this->params->get('client_col_sermon_date')){ ?>
					<td align="left" valign="top" ><?php echo JHTML::date($row->sermon_date, '%x', 0); ?></td>
				<?php }
				if( $this->params->get('client_col_sermon_time')){ ?>
					<td><?php echo SermonspeakerHelperSermonspeaker::insertTime($row->sermon_time); ?></td>
				<?php }
				if ($this->params->get('client_col_sermon_addfile')) { ?>
					<td><?php echo SermonspeakerHelperSermonspeaker::insertAddfile($row->addfile, $row->addfileDesc); ?></td>
				<?php } ?>
			</tr>
		<?php } ?>
	<?php } ?>
</table>
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
<br>
<div class="Pages">
	<div class="Paginator">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
</div>