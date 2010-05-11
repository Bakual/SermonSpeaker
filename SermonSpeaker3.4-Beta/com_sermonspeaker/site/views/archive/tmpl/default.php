<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
$sort	= JRequest::getWord('sort','sermondate');
?>
<table width="100%" cellpadding="2" cellspacing="0">
	<tr class="componentheading">
		<th align="left" valign="bottom"><?php echo JText::_('SERMFROM')." ".JHtml::Date($this->rows[0]->sermon_date, '%B, %Y', 0); ?></th>
	</tr>
</table>
<p />
<div class="Pages">
	<b><?php echo JText::_('SERSORTBY'); ?></b>
	<?php
	$link = 'index.php?view=archive&month='.$this->month.'&year='.$this->year.'&sort=';
	if ($sort == "sermondate") { $sortheader = JText::_('SERDATE').' | '; }
	else { $sortheader = '<a title="'.JText::_('SORTDATE').'" href="'.JRoute::_($link.'sermondate').'">'.JText::_('SERDATE').'</a> | '; }
	if ($sort == "mostrecentlypublished") { $sortheader .= JText::_('SERPUB').' | '; }
	else { $sortheader .= '<a title="'.JText::_('SORTPUB').'" href="'.JRoute::_($link.'mostrecentlypublished').'">'.JText::_('SERPUB').'</a> | '; }
	if ($sort == "mostviewed") { $sortheader .= JText::_('SERVIEW').' | '; }
	else { $sortheader .= '<a title="'.JText::_('SORTVIEW').'" href="'.JRoute::_($link.'mostviewed').'">'.JText::_('SERVIEW').'</a> | '; }
	if ($sort == "alphabetically") { $sortheader .= JText::_('SERALPH').' | '; }
	else { $sortheader .= '<a title="'.JText::_('SORTALPH').'" href="'.JRoute::_( $link.'alphabetically').'">'.JText::_('SERALPH').'</a>'; }
	echo $sortheader;
	?>
	<div class="Paginator">
		<?php echo $this->pagination->getResultsCounter(); ?><br />
		<?php if ($this->pagination->getPagesCounter()) echo $this->pagination->getPagesCounter()."<br />"; ?>
		<?php if ($this->pagination->getPagesLinks()) echo $this->pagination->getPagesLinks()."<br />"; ?>
	</div>
</div>
<hr style="width: 100%; height: 2px;" />
<table border="0" cellpadding="2" cellspacing="2" width="100%">
    <tr>
		<?php if ($this->params->get('client_col_sermon_number')) { ?>
			<th width="5%" align="left"><?php echo JText::_('SERMONNUMBER'); ?></th>
		<?php } ?>
		<th width="380" align="left"><?php echo JText::_('SERMONNAME'); ?></th>
		<?php if( $this->params->get('client_col_sermon_scripture_reference')){ ?>
			<th align="left"><?php echo JText::_('SCRIPTURE'); ?></th>
		<?php } ?>
		<th align="left"><?php echo JText::_('SPEAKER');?></th>
		<?php if( $this->params->get('client_col_sermon_date')){ ?>
			<th align="left"><?php echo JText::_('SERMON_DATE'); ?></th>
		<?php }
		if( $this->params->get('client_col_sermon_time')){ ?>
			<th align="center"><?php echo JText::_('SERMONTIME'); ?></th>
		<?php } ?>
		<?php if ($this->params->get('client_col_sermon_addfile')) { echo "<th align=\"left\">".JText::_('ADDFILE')."</th>\n"; }?>
	</tr>
<!-- Begin Data -->
    <?php if ($this->rows){
	$i = 0;
	foreach( $this->rows as $row ){ ?>
		<tr class="row<?php echo $i; ?>">
		<?php $i = 1 - $i;
			if ($this->params->get('client_col_sermon_number')) { ?>
				<td align="left" valign="middle"><?php echo $row->sermon_number; ?></td>
			<?php } ?>
			<td align="left">
				<?php
				if (substr($row->sermon_path,0,7) == "http://") {
					$lnk = $row->sermon_path;
				} else {
					$lnk = $mosConfig_live_site . $row->sermon_path;
				} ?>
				&nbsp;&nbsp;<a href="<?php echo JRoute::_("index.php?view=sermon&id=$row->slug"); ?>"><img title="<?php echo JText::_('PLAYTOPLAY'); ?>" src="<?php echo JURI::root(); ?>/components/com_sermonspeaker/images/play.gif" width="16" height="16" border="0" alt="" /></a>
				<a title="<?php echo JText::_('PLAYTOPLAY'); ?>" href="<?php echo JRoute::_("index.php?view=sermon&id=$row->slug"); ?>" style="text-decoration:none"><?php echo $row->sermon_title; ?></a>
			</td>
			<?php if ($this->params->get('client_col_sermon_scripture_reference')){ ?>
				<td align="left" valign="middle"><?php echo $row->sermon_scripture; ?></td>
			<?php }
			if ($row->pic == "") { $row->pic = JURI::root()."components/com_sermonspeaker/images/nopict.jpg"; } ?>
			<td>
				<a class="modal" href="<?php echo JRoute::_('index.php?view=speaker&layout=popup&id='.$row->s_id.'&tmpl=component')?>" rel="{handler: 'iframe', size: {x: 700, y: 500}}">
				<?php echo JHTML::tooltip('<img src="'.$row->pic.'" alt="'.$row->name.'">',$row->name,'',$row->name); ?>
				</a>
			</td>
			<?php if ($this->params->get('client_col_sermon_date')){ ?>
				<td align="left" valign="middle"><?php echo JHtml::Date($row->sermon_date,'%x'); ?></td>
			<?php }
			if ($this->params->get('client_col_sermon_time')){ ?>
				<td align="center" valign="middle"><?php echo JHtml::Date($row->sermon_time, '%X', 0); ?></td>
			<?php }
			if ($this->params->get('client_col_sermon_addfile')) { ?>
				<td><?php echo SermonspeakerHelperSermonspeaker::insertAddfile($row->addfile, $row->addfileDesc); ?></td>
			<?php } ?>
		</tr>
	<?php }
	} ?>
</table>
<br />
<div class="Pages">
	<div class="Paginator">
		<?php echo $this->pagination->getPagesLinks(); ?><br />
	</div>
</div>