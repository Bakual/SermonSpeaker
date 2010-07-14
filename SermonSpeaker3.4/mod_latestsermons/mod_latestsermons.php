<?php
defined('_JEXEC') or die('Restricted access');
error_reporting(E_ERROR | E_WARNING | E_PARSE);

if ($params->get('ls_show_mouseover')) {
	JHTML::_('behavior.tooltip');
} //include only if needed...

$database = &JFactory::getDBO();

$query 	= 'SELECT a.sermon_title, a.id, a.sermon_date, b.name, c.series_title'
		. ", CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(':', a.id, a.alias) ELSE a.id END as slug \n"
		. ' FROM #__sermon_sermons a'
		. ' LEFT JOIN #__sermon_speakers b ON a.speaker_id = b.id'
		. ' LEFT JOIN #__sermon_series c ON a.series_id = c.id'
		. ' WHERE a.published = 1'
		. ' ORDER BY sermon_date DESC, (sermon_number+0) DESC'
		. ' LIMIT '.(int)$params->get('ls_count');

$database->setQuery($query);
$rows = $database->loadObjectList();

echo '<ul class="'.$params->get('moduleclass_sfx')."\">\n";
foreach($rows as $row) {
	echo '<li class="'.$params->get('moduleclass_sfx').'">';
	if ($params->get('ls_show_mouseover')) {
		$tips = NULL;
		if ($params->get('ls_show_mo_speaker')) {
			$tips[] = JText::_('MOD_LATESTSERMONS_SPEAKER').": ".$row->name;
		}
		if ($params->get('ls_show_mo_series')) {
			$tips[] = JText::_('MOD_LATESTSERMONS_SERIE').": ".trim($row->series_title);
		}
		if ($params->get('ls_show_mo_date')) {
			$date_format = $params->get('ls_mo_date_format', JText::_('%Y-%M-%D'));
			$tips[] = JText::_('MOD_LATESTSERMONS_DATE').": ".JHtml::Date($row->sermon_date, $date_format, 0);
		}
		$tip = implode('<br>', $tips);
		$title = htmlspecialchars(stripslashes($row->sermon_title),ENT_QUOTES);
		echo JHTML::tooltip($tip, '', '', $title, JRoute::_('index.php?option=com_sermonspeaker&view=sermon&id='.$row->slug));
		echo "</li>\n";
	} else {
		echo '<a href="'.JRoute::_('index.php?option=com_sermonspeaker&view=sermon&id='.$row->slug).'">'.stripslashes($row->sermon_title)."</a></li>\n";
	} // if mouseover
}
echo "</ul>\n";