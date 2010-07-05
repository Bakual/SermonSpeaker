<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<table width="100%" cellpadding="2" cellspacing="0">
	<tr class="componentheading">
		<th align="left" valign="bottom"><?php echo JText::_('COM_SERMONSPEAKER_SERIESSERMONS_TITLE').$this->cat; ?></th>
	</tr>
</table>
<p />
<div class="Pages">
	<div class="Paginator">
		<?php echo $this->pagination->getResultsCounter(); ?><br />
		<?php if ($this->pagination->getPagesCounter()) echo $this->pagination->getPagesCounter()."<br />"; ?>
		<?php if ($this->pagination->getPagesLinks()) echo $this->pagination->getPagesLinks()."<br />"; ?>
	</div>
</div>

<hr style="width: 100%; height: 2px;" />
<!-- Begin Data -->
<form action="http://<?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ?>" method="post" id="adminForm" name="adminForm">
<table style="width: 100%" cellpadding="10" cellspacing="0" width="100%">
	<?php
	$callback = NULL;
	if ($this->params->get('ga')) { $callback = "&callback=".$this->params->get('ga'); }
	$model	= &$this->getModel();
	$linkbase = JURI::root()."components/com_sermonspeaker/media/player/";
	foreach($this->rows as $row) {
		$sermons = &$model->getSermons($row->id);
		?>
		<tr>
			<td style="width: 10%" align="left" valign="top">
				<?php if ($row->avatar) { echo "<img src='".SermonspeakerHelperSermonspeaker::makelink($row->avatar)."' >"; } ?>
			</td>
			<td style="width: 90%" align="left" valign="top">
				<h3><?php echo $this->escape($row->series_title); ?></h3>
				<p><?php echo $row->series_description; ?></p>
				<?php
				foreach($sermons as $sermon) { ?>
					<p><b><?php echo $this->escape($sermon->sermon_title); ?></b>
					(<?php echo JHTML::date($sermon->sermon_date, '%x', 0); ?>)<br />
					<?php echo $sermon->notes; ?><br />
					<?php $return = SermonspeakerHelperSermonspeaker::insertAddfile($sermon->addfile, $sermon->addfileDesc);
					if ($return != NULL) {
						echo "<b>".JText::_('COM_SERMONSPEAKER_ADDFILE')." : </b>".$return."<br />\n";
					}
					if ($this->params->get('client_col_player')){
						//Check if link targets to an external source
						if (substr($sermon->sermon_path,0,7) == "http://"){
							$lnk = $sermon->sermon_path;
						} else {  
							$lnk = SermonspeakerHelperSermonspeaker::makelink($sermon->sermon_path); 
						}
						$ret = SermonspeakerHelperSermonspeaker::insertPlayer($lnk);
						$pp_ret = explode("/",$ret);
						$pp_h = $pp_ret[0];
						$pp_w = $pp_ret[1];
					} else {
						// if player is disabled show a link
						echo JText::_('COM_SERMONSPEAKER_DIRECTLINK_HOOVER').": <a title=\"".JText::_('COM_SERMONSPEAKER_DIRECTLINK_HOOVER')."\" href=\"".$lnk."\">".$this->escape($sermon->sermon_title)."</a>";
					} ?>
					</p>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td colspan="2"> <hr size="2" width="100%" /> </td>
		</tr>
	<?php } ?>
</table>
<br />
<div class="Pages">
	<div class="Paginator">
		<?php echo $this->pagination->getListFooter(); ?><br />
	</div>
</div>
</form>