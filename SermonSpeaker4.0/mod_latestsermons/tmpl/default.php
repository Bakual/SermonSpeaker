<?php 
// no direct access
defined('_JEXEC') or die; 
$i = 0;
$tooltip = ($params->get('ls_show_mo_speaker') || $params->get('ls_show_mo_series') || $params->get('ls_show_mo_date') || $params->get('show_hits') & 1);
if ($tooltip) {
	//include only if needed...
	JHTML::_('behavior.tooltip');
}
?>
<div class="latestsermons<?php echo $moduleclass_sfx; ?>">
<?php if ($params->get('show_list')): ?>
	<ul class="latestsermons_list">
	<?php foreach($list as $row) :
		$i++; ?>
		<li class="latestsermons_entry<?php echo $i; ?>">
		<?php
		if ($params->get('use_date')) :
			$date_format = JText::_($params->get('ls_mo_date_format', 'DATE_FORMAT_LC4'));
			$text = JHtml::Date($row->sermon_date, $date_format, true);
		else :
			$text = $row->sermon_title;
		endif;
		if ($params->get('show_hits') > 1 and $row->hits) :
			$text .= ' <small>('.$row->hits.')</small>';
		endif;
		$link = JRoute::_('index.php?option=com_sermonspeaker&view=sermon&id='.$row->slug.'&Itemid='.$itemid);
		if ($tooltip) :
			$title = '';
			if ($params->get('show_tooltip_title')) :
				$title = $row->sermon_title;
			endif;
			$tips = array();
			if ($params->get('ls_show_mo_speaker') and $row->name) :
				$tips[] = JText::_('MOD_LATESTSERMONS_SPEAKER').': '.$row->name;
			endif;
			if ($params->get('ls_show_mo_series') and $row->series_title) :
				$tips[] = JText::_('MOD_LATESTSERMONS_SERIE').': '.$row->series_title;
			endif;
			if ($params->get('ls_show_mo_date') and $row->sermon_date) :
				$date_format = JText::_($params->get('ls_mo_date_format', 'DATE_FORMAT_LC4'));
				$tips[] = JText::_('JDATE').': '.JHtml::Date($row->sermon_date, $date_format, true);
			endif;
			if (($params->get('show_hits') & 1) and $row->hits) :
				$tips[] = JText::_('JGLOBAL_HITS').': '.$row->hits;
			endif;
			$tip = implode('<br />', $tips);
			echo JHTML::tooltip($tip, $title, '', $text, $link); ?>
		<?php else : ?>
			<a href="<?php echo $link; ?>">
				<?php echo $text; ?>
			</a>
		<?php endif; ?>
		</li>
	<?php endforeach; ?>
	</ul>
<?php endif;
if ($params->get('show_player')) : ?>
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