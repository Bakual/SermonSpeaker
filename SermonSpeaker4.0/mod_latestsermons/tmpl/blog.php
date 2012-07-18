<?php 
// no direct access
defined('_JEXEC') or die('Restricted access'); 
$i = 0;
$count = count($list);
?>
<div class="latestsermons<?php echo $moduleclass_sfx; ?>">
<?php if ($params->get('show_list')): ?>
	<div class="latestsermons_list">
	<?php foreach($list as $row) : 
		$i++; ?>
		<div class="latestsermons_entry<?php echo $i; ?>">
			<h3><a href="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=sermon&id='.$row->slug.'&Itemid='.$itemid); ?>">
				<?php echo $row->sermon_title;
				if ($params->get('show_hits', 0) > 1 && $row->hits) : ?>
					<small>(<?php echo $row->hits; ?>)</small>
				<?php endif; ?>
			</a></h3>
			<dl class="article-info sermon-info" style="display:block; margin:0;">
				<dt class="article-info-term"><?php echo JText::_('JDETAILS'); ?></dt>
				<?php if ($params->get('ls_show_mo_series') && $row->series_title) : ?>
					<dd class="category-name">
						<?php echo JText::_('MOD_LATESTSERMONS_SERIE'); ?>: 
						<?php if ($row->series_state): ?>
							<a href="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=serie&id='.$row->series_slug.'&Itemid='.$itemid); ?>">
								<?php echo $row->series_title; ?>
							</a>
						<?php else:
							echo $row->series_title; 
						endif; ?>
					</dd>
				<?php endif;
				if ($params->get('ls_show_mo_date') && $row->sermon_date) : ?>
					<dd class="published">
						<?php $date_format = JText::_($params->get('ls_mo_date_format', 'DATE_FORMAT_LC4'));
						echo JText::_('JDATE').": ".JHtml::Date($row->sermon_date, $date_format, true); ?>
					</dd>
				<?php endif;
				if ($params->get('ls_show_mo_speaker') && $row->name) : ?>
					<dd class="createdby">
						<?php echo JText::_('MOD_LATESTSERMONS_SPEAKER'); ?>: 
						<?php if ($row->speaker_state): ?>
							<a href="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=speaker&id='.$row->speaker_slug.'&Itemid='.$itemid); ?>">
								<?php echo $row->name; ?>
							</a>
						<?php else:
							echo $row->name; 
						endif; ?>
					</dd>
				<?php endif;
				if ($params->get('show_hits', 0) & 1) : ?>
					<dd class="hits">
						<?php echo JText::_('JGLOBAL_HITS'); ?>: 
						<?php echo $row->hits; ?>
					</dd>
				<?php endif; ?>
			</dl>
			<div style="clear:left;"></div>
			<?php if (strlen($row->notes) > 0) : ?>
				<div>
					<?php echo JHTML::_('content.prepare', $row->notes); ?>
				</div>
			<?php endif;
			if ($i < $count) : ?>
				<hr />
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
	</div>
<?php endif;
if ($params->get('show_player')) : 
	if ($params->get('show_list')) : ?>
		<br />
	<?php endif; ?>
	<div class="latestsermons_player">
	<?php require_once(JPATH_SITE.'/components/com_sermonspeaker/helpers/sermonspeaker.php');
	require_once(JPATH_SITE.'/components/com_sermonspeaker/helpers/player.php');
	jimport('joomla.application.component.helper');
	$c_params = JComponentHelper::getParams('com_sermonspeaker');
	$config['autostart']	= 0;
	$config['count']		= 'ls';
	$config['type']			= $c_params->get('fileprio') ? 'video' : 'audio';
	$config['alt_player']	= $c_params->get('alt_player');
	$config['vheight']		= $params->get('vheight');
	$player = new SermonspeakerHelperPlayer($list, $config);
	echo $player->mspace;
	echo $player->script; ?>
	</div>
<?php endif;
if ($params->get('ls_show_mo_link')) : ?>
	<br />
	<div class="latestsermons_link">
		<a href="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=sermons&Itemid='.$itemid); ?>"><?php echo JText::_('MOD_LATESTSERMONS_LINK'); ?></a>
	</div>
<?php endif; ?>
</div>