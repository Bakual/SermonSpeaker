<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
JHTML::_('behavior.tooltip');
?>
<table width="100%" cellpadding="2" cellspacing="0">
	<tr class="componentheading">
		<th align="left" valign="bottom"><?php echo $this->serie[0]->series_title.": ".JText::_('SINGLESERIES'); ?></th>
	</tr>
</table>
<table cellpadding="2" cellspacing="10">
<tr>
	<th><?php if ($this->serie[0]->avatar != "") {
			echo "<img src='".SermonspeakerHelperSermonspeaker::makelink($this->serie[0]->avatar)."' >";
        } ?>
	</th>
	<th align="left"><?php echo $this->serie[0]->series_description; ?></th>
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

<table border="0" cellpadding="2" cellspacing="10" width="100%">
	<tr>
		<?php if($this->params->get('client_col_sermon_number')){ ?>
			<th width="5%" align="left" valign="bottom"><?php echo JText::_('SERMONNUMBER'); ?></th>
		<?php } ?>
		<th width="40%" align="left" valign="bottom"> <?php echo JText::_('SERMONNAME'); ?></th>
		<th width="20%" align="left" valign="bottom"> <?php echo JText::_('SPEAKER'); ?></th>
		<?php if( $this->params->get('client_col_sermon_scripture_reference')) { ?>
			<th width="20%" align="left" valign="bottom"> <?php echo JText::_('SCRIPTURE'); ?></th>
		<?php } ?>
		<?php if( $this->params->get('client_col_sermon_date')){ ?>
			<th width="20%" align="left" valign="bottom"><?php echo JText::_('SERMON_DATE'); ?></th>
		<?php } ?>
		<?php if( $this->params->get('client_col_sermon_time')){ ?>
			<th width="20%" align="left" valign="bottom"><?php echo JText::_('SERMONTIME'); ?></th>
		<?php } ?>
	</tr>
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
					&nbsp;<a href="<?php echo JRoute::_("index.php?view=sermon&id=$row->slug"); ?>">
						<img title="<?php echo JText::_('PLAYTOPLAY'); ?>" src="<?php echo JURI::root().'components/com_sermonspeaker/images/play.gif'; ?>" width='16' height='16' border='0' align='top' alt="" />
					</a>
					<a title="<?php echo JText::_('SINGLE_SERMON_HOOVER_TAG'); ?>" href="<?php echo JRoute::_("index.php?view=sermon&id=$row->slug"); ?>">
						<?php echo $row->sermon_title; ?>
					</a>
				</td>
				<td align="left" valign="top">
					<a class="modal" href="<?php echo JRoute::_('index.php?view=speaker&layout=popup&id='.$row->s_id.'&tmpl=component')?>" rel="{handler: 'iframe', size: {x: 700, y: 500}}">
					<?php echo JHTML::tooltip('<img src="'.$row->pic.'" alt="'.$row->name.'"><br>'.$row->name,'','',$row->name); ?>
					</a>
				</td>
				<?php if( $this->params->get('client_col_sermon_scripture_reference')){ ?>		
					<td align="left" valign="top" ><?php echo $row->sermon_scripture; ?></td>
				<?php } ?>
				<?php if( $this->params->get('client_col_sermon_date')){ ?>
					<td align="left" valign="top" ><?php echo JHtml::_('date', $row->sermon_date, '%x', 0); ?></td>
				<?php } ?>
				<?php if( $this->params->get('client_col_sermon_time')){ ?>
					<td><?php echo JHtml::_('date', $row->sermon_time, '%X', 0); ?></td>
				<?php } ?>
			</tr>
		<?php } ?>
	<?php } ?>
</table>
<br>
<div class="Pages">
	<div class="Paginator">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
</div>