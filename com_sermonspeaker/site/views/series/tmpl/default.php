<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
?>
<div id="ss-series-container">
<h1 class="componentheading">
	<?php echo JText::_('COM_SERMONSPEAKER_SERIES_TITLE').$this->cat; ?>
</h1>
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
<form action="http://<?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ?>" method="post" id="adminForm" name="adminForm">
<table class="adminlist" cellpadding="2" cellspacing="2" width="100%">
<thead class="sectiontableheader">
	<tr>
		<?php if($this->av) { echo "<th width='10'> </th>"; } ?>
		<th align="left"><?php echo JText::_('COM_SERMONSPEAKER_SERIESTITLE'); ?></th>
		<th align="left"><?php echo JText::_('COM_SERMONSPEAKER_SPEAKER'); ?></th>
	</tr>
</thead>
	<?php
	$i = 0;
	$base = JURI::root();
    	foreach($this->rows as $row) {
			echo "<tr class=\"row$i\">\n"; 
			$i = 1 - $i;
			if ($this->av) {
				if ($row->avatar != '') { 
					echo "<td><img src='".SermonspeakerHelperSermonspeaker::makelink($row->avatar)."' ></td>";
				} else { 
					echo "<td> </td>"; 
				} 
			}
			?> 
    		<td align="left" nowrap><a title='<?php echo JText::_('COM_SERMONSPEAKER_SERIESLINK_HOOVER'); ?>' href="<?php echo JRoute::_("index.php?view=serie&id=$row->id" ); ?>"><?php echo $row->series_title; ?></a></td>
    		<td align="left">
				<?php echo $row->speakers; ?>
			</td>
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
<?php } else { ?>
	<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERIES')); ?></div>
<?php } ?>
</div>