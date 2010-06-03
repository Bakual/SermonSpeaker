<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
$Itemid	= JRequest::getInt('Itemid');
?>
<table width="100%" cellpadding="2" cellspacing="0">
	<tr class="componentheading">
		<th align="left" valign="bottom"><?php echo JText::_('SPEAKERMAIN'); ?></th>
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
<!-- begin Data -->
<?php foreach($this->rows as $row) { ?>
	<hr style="width: 100%; height: 2px;" />
	<?php echo "<h3>".$row->name."</h3>"; ?>
	<table border='0' cellpadding="2" cellspacing="0" width="100%">
		<tr>
			<td valign="middle" align="center" width="30%">
				<?php if($row->pic) { ?>
					<a href="<?php echo JRoute::_("index.php?view=speaker&id=$row->id"); ?>">
						<img src="<?php echo $row->pic; ?>" border="0" title="<?php echo $row->name; ?>" alt="" />
					</a>
				<?php } ?>
			</td>
			<td valign="top" align="left">
				<?php
				if ($row->website && $row->website != "http://") {
					echo '<a href="'.$row->website.'" target="_blank" title="'.JText::_('WEB_LINK_DESCRIPTION').'">'.JText::_('WEB_LINK_TAG').' '.$row->name.'</a>';
				}
				if($this->params->get('speaker_intro') && $row->intro) {
					echo $row->intro;
				}
				?>
			</td>
		</tr>
		<tr>
			<th colspan="2" align="left"><a  title="<?php echo JText::_('SERIES_HOOVER_TAG'); ?>" href="<?php echo JRoute::_("index.php?view=speaker&id=$row->id" ); ?>"><?php echo JText::_('SERMON_SERIES'); ?></a></th>
		</tr>
		<tr>
		<th colspan="2" align="left"><a title="<?php echo JText::_('SERMON_HOOVER_TAG'); ?>" href="<?php echo JRoute::_("index.php?view=speaker&layout=latest-sermons&id=$row->id" ); ?>"><?php echo JText::_('SERMONS'); ?></a></th>
		</tr>
	</table>
	<p></p>
<?php } ?>
<br />
<div class="Pages">
	<div class="Paginator">
		<?php echo $this->pagination->getPagesLinks(); ?><br />
	</div>
</div>