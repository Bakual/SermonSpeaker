<?php
defined('_JEXEC') or die('Restricted access');

if ($params->get('ls_show_mouseover')) {
	//include only if needed...
	JHTML::_('behavior.tooltip');
}

$db = JFactory::getDBO();

$where = '';
if ($params->get('sermon_cat')){
	$where = ' AND a.catid = '.(int)$params->get('sermon_cat');
}
if ($params->get('speaker_cat')){
	$where .= ' AND b.catid = '.(int)$params->get('speaker_cat');
}
if ($params->get('series_cat')){
	$where .= ' AND c.catid = '.(int)$params->get('series_cat');
}

$query 	= 'SELECT a.sermon_title, a.id, a.sermon_date, b.name, c.series_title, a.audiofile, a.videofile'
		. ', a.sermon_time, a.picture, b.pic'
		. ", CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(':', a.id, a.alias) ELSE a.id END as slug \n"
		. ' FROM #__sermon_sermons a'
		. ' LEFT JOIN #__sermon_speakers b ON a.speaker_id = b.id'
		. ' LEFT JOIN #__sermon_series c ON a.series_id = c.id'
		. ' WHERE a.state = 1'
		.$where
		. ' ORDER BY sermon_date DESC, (sermon_number+0) DESC'
		. ' LIMIT '.(int)$params->get('ls_count');
$db->setQuery($query);
$rows = $db->loadObjectList();

// get the menu item from the params
$ss_itemid = $params->get('ls_mo_menuitem');

if ($params->get('show_list')){ ?>
	<ul class="latestsermons<?php echo $params->get('moduleclass_sfx'); ?>">
	<?php foreach($rows as $row) { ?>
		<li class="<?php echo $params->get('moduleclass_sfx'); ?>">
		<?php if ($params->get('ls_show_mouseover')) {
			$tips = array();
			if ($params->get('ls_show_mo_speaker') && $row->name) {
				$tips[] = JText::_('MOD_LATESTSERMONS_SPEAKER').": ".$row->name;
			}
			if ($params->get('ls_show_mo_series') && $row->series_title) {
				$tips[] = JText::_('MOD_LATESTSERMONS_SERIE').": ".trim($row->series_title);
			}
			if ($params->get('ls_show_mo_date') && $row->sermon_date) {
				$date_format = JText::_($params->get('ls_mo_date_format', 'DATE_FORMAT_LC4'));
				$tips[] = JText::_('JDATE').": ".JHtml::Date($row->sermon_date, $date_format, 'UTC');
			}
			$tip = implode('<br />', $tips);
			$title = htmlspecialchars(stripslashes($row->sermon_title), ENT_QUOTES);
			echo JHTML::tooltip($tip, '', '', $title, JRoute::_('index.php?option=com_sermonspeaker&view=sermon&id='.$row->slug.'&Itemid='.$ss_itemid)); ?>
		<?php } else { ?>
			<a href="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=sermon&id='.$row->slug.'&Itemid='.$ss_itemid); ?>"><?php echo stripslashes($row->sermon_title); ?></a>
		<?php } ?>
		</li>
	<?php } ?>
	</ul>
<?php } ?>
<?php if ($params->get('show_player')) {
	require_once(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'helpers'.DS.'sermonspeaker.php');
	require_once(JPATH_SITE.DS.'components'.DS.'com_sermonspeaker'.DS.'helpers'.DS.'player.php');
	jimport('joomla.application.component.helper');
	$c_params = JComponentHelper::getParams('com_sermonspeaker');
	$config['autostart']	= 0;
	$config['count']		= 'ls';
	$config['type'] 		= 'audio';
	$config['alt_player']	= $c_params->get('alt_player');
	$player = new SermonspeakerHelperPlayer($rows, $config);
	echo $player->mspace;
	echo $player->script;
} ?>
<?php if ($params->get('ls_show_mo_link')) { ?>
	<br />
	<a href="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=sermons&Itemid='.$ss_itemid); ?>"><?php echo JText::_('MOD_LATESTSERMONS_LINK'); ?></a>
<?php } ?>