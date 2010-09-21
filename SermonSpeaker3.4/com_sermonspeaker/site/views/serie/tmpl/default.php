<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
/* JS Script für Joomla Sortierung */
$this->document->addScriptDeclaration( "
	function tableOrdering( order, dir, task ) {
		var form = document.adminForm;
		form.filter_order.value = order;
		form.filter_order_Dir.value = dir;
		form.submit( task );
	}"
);
?>
<div id="ss-serie-container">
<h1 class="componentheading"><?php echo JText::_('COM_SERMONSPEAKER_SERIE_TITLE').": ".$this->serie->series_title; ?></h1>

<div class="ss-serie-desc">
	<span class="ss-avatar"><?php if ($this->serie->avatar != "") {
				echo "<img src='".SermonspeakerHelperSermonspeaker::makelink($this->serie->avatar)."' >";
	        } ?>
	</span>
	<?php echo $this->serie->series_description; ?>
</div>
<br style="clear:both" />
<?php if ($this->rows){ ?>
<?php if ($this->params->get('series_player')){ ?>
	<hr style="width: 80%; height: 1px; text-align: center;">
	<?php
	$lnk = "index.php%3Foption%3Dcom_sermonspeaker%26view%3Dfeed%26series_id%3D".$this->serie->id;
	$ret = SermonspeakerHelperSermonspeaker::insertPlayer($lnk);
	$pp_ret = explode("/",$ret);
	$pp_h = $pp_ret[0];
	$pp_w = $pp_ret[1];
	?>
	<hr style="width: 80%; height: 1px; text-align: center;">
<?php } ?>
<div class="Pages">
	<div class="Paginator">
		<?php echo $this->pagination->getResultsCounter(); ?><br>
		<?php if ($this->pagination->getPagesCounter()) echo $this->pagination->getPagesCounter()."<br>"; ?>
		<?php if ($this->pagination->getPagesLinks()) echo $this->pagination->getPagesLinks()."<br>"; ?>
	</div>
</div>

<hr style="width: 100%; height: 2px;">
<form action="http://<?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ?>" method="post" id="adminForm" name="adminForm">
<table class="adminlist" cellpadding="2" cellspacing="2" width="100%">
<!-- Tabellenkopf mit Sortierlinks erstellen -->
<thead>
	<tr>
		<?php if ($this->params->get('client_col_sermon_number')) { ?>
			<th width="5%" align="left"><?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERMONNUMBER', 'sermon_number', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		<?php } ?>
		<th align="left"><?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERMONTITLE', 'sermon_title', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		<?php if ($this->params->get('client_col_sermon_scripture_reference')) { ?>
			<th align="left"><?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SCRIPTURE', 'sermon_scripture', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		<?php } ?>
		<th align="left"><?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SPEAKER', 'name', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		<?php if ($this->params->get('client_col_sermon_date')) { ?>
			<th align="left">
				<?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERMONDATE', 'sermon_date', $this->lists['order_Dir'], $this->lists['order']); ?>
			</th>
		<?php }
		if ($this->params->get('client_col_sermon_time')) { ?>
		<th align="center"><?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_SERMONTIME', 'sermon_time', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		<?php }
		if ($this->params->get('client_col_sermon_addfile')) { ?>
		<th align="left"><?php echo JHTML::_('grid.sort', 'COM_SERMONSPEAKER_ADDFILE', 'addfileDesc', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		<?php } ?>
	</tr>
</thead>
<!-- Begin Data -->
	<?php
	$i = 0;
	foreach($this->rows as $row) { 
		echo "<tr class=\"row$i\">\n"; 
		$i = 1 - $i;
		if( $this->params->get('client_col_sermon_number')){ ?>
			<td><?php echo $row->sermon_number; ?></td>
		<?php } ?>
			<td align="left">
				&nbsp;<a href="<?php echo $row->link1; ?>"><img title="<?php echo JText::_('COM_SERMONSPEAKER_PLAYICON_HOOVER'); ?>" src="<?php echo JURI::root().'components/com_sermonspeaker/images/play.gif'; ?>" class='icon_play' width='16' height='16' border='0' alt="" /></a>
				<a title="<?php echo JText::_('COM_SERMONSPEAKER_SERMONTITLE_HOOVER'); ?>" href="<?php echo $row->link2; ?>">
					<?php echo $row->sermon_title; ?>
				</a>
			</td>
			<?php if($this->params->get('client_col_sermon_scripture_reference')){ ?>		
				<td align="left" valign="top" ><?php echo $row->sermon_scripture; ?></td>
			<?php } ?>
			<td align="left" valign="top">
				<?php echo SermonspeakerHelperSermonSpeaker::SpeakerTooltip($row->s_id, $row->pic, $row->name); ?>
			</td>
			<?php if( $this->params->get('client_col_sermon_date')){ ?>
				<td align="left" valign="top" ><?php echo JHTML::date($row->sermon_date, JText::_($this->params->get('date_format')), 0); ?></td>
			<?php }
			if( $this->params->get('client_col_sermon_time')){ ?>
				<td><?php echo SermonspeakerHelperSermonspeaker::insertTime($row->sermon_time); ?></td>
			<?php }
			if ($this->params->get('client_col_sermon_addfile')) { ?>
				<td><?php echo SermonspeakerHelperSermonspeaker::insertAddfile($row->addfile, $row->addfileDesc); ?></td>
			<?php } ?>
		</tr>
	<?php } ?>
</table>
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<br>
<div class="Pages">
	<div class="Paginator">
		<?php echo $this->pagination->getListFooter(); ?>
	</div>
</div>
</form>
<?php } else { ?>
	<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
<?php } ?>
</div>