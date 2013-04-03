<?php
defined('_JEXEC') or die('Restricted access');
?>
<div id="ss-sermons-container">
<h1 class="componentheading"><?php echo JText::_('COM_SERMONSPEAKER_SERIESSERMONS_TITLE').$this->cat; ?></h1>
<p />
<?php if ($this->rows){ ?>
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
<?php
$count = NULL;
$callback = NULL;
if ($this->params->get('ga')) { $callback = "&callback=".$this->params->get('ga'); }
$model	= &$this->getModel();
$linkbase = JURI::root()."components/com_sermonspeaker/media/player/";
foreach($this->rows as $row) {
	$sermons = &$model->getSermons($row->id); ?>
	<div>
	<?php if($row->avatar) { ?>
		<img src="<?php echo SermonspeakerHelperSermonspeaker::makelink($row->avatar); ?>" style="float:right; margin-top:25px;">
	<?php } ?>
	<h3 class="contentheading"><?php echo $this->escape($row->series_title); ?></h3>
	<p><?php echo $row->series_description; ?></p>
	</div>
	<div style="margin-left:10%;">
	<?php foreach($sermons as $sermon) { 
		$count ++;?>
		<h4 style="margin-left:-5%;"><?php echo $this->escape($sermon->sermon_title).'('.JHTML::date($sermon->sermon_date, JText::_($this->params->get('date_format')), 0).')'; ?></h4>
		<?php echo $sermon->notes;
		if ($sermon->addfile && $sermon->addfileDesc){
			echo '<b>'.JText::_('COM_SERMONSPEAKER_ADDFILE').' : </b>';
			echo SermonspeakerHelperSermonspeaker::insertAddfile($sermon->addfile, $sermon->addfileDesc);
			echo "<br />\n";
		}
		if ($this->params->get('client_col_player')){
			//Check if link targets to an external source
			if (substr($sermon->sermon_path,0,7) == "http://"){
				$lnk = $sermon->sermon_path;
			} else {
				$lnk = SermonspeakerHelperSermonspeaker::makelink($sermon->sermon_path); 
			}
			SermonspeakerHelperSermonspeaker::insertPlayer($lnk, $sermon->sermon_time, $count, $sermon->sermon_title);
		} else {
			// if player is disabled show a link
			echo JText::_('COM_SERMONSPEAKER_DIRECTLINK_HOOVER').": <a title=\"".JText::_('COM_SERMONSPEAKER_DIRECTLINK_HOOVER')."\" href=\"".$lnk."\">".$this->escape($sermon->sermon_title)."</a>";
		} ?>
	<?php } ?>
	</div>
	<br style="clear:both;" />
	<hr size="2" width="100%" />
	<?php } ?>
<br />
<div class="Pages">
	<div class="Paginator">
		<?php echo $this->pagination->getListFooter(); ?><br />
	</div>
</div>
</form>
<?php } else { ?>
	<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERIES')); ?></div>
<?php } ?>
</div>