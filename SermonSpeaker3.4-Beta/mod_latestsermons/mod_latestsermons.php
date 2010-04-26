<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
error_reporting(E_ERROR | E_WARNING | E_PARSE);

// require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_sermonspeaker'.DS.'config.sermonspeaker.php');
// $config = new sermonConfig;
// neu mittels JoomlaParameter gelöst, $params wird automatisch von Joomla geliefert

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
		. ' LIMIT '.(int)$params->get('ls_nbr_latest');

$database->setQuery( $query );
$rows = $database->loadObjectList();

echo "<ul class=\"".$params->get('moduleclass_sfx')."\">\n";
foreach($rows as $row) {
	echo "<li class=\"".$params->get('moduleclass_sfx')."\">";
	if ($params->get('ls_show_mouseover')) {
		$tips = NULL;
		if ($params->get('ls_show_mo_speaker')) {
			$tips[] = JText::_('SPEAKER').": ".$row->name;
		}
		if ($params->get('ls_show_mo_series')) {
			$tips[] = JText::_('SERIE').": ".trim($row->series_title);
		}
		if ($params->get('ls_show_mo_date')) {
			$tips[] = JText::_('SERMON_DATE').": ".JHtml::_('date', $row->sermon_date, '%x', 0);
		}
		$tip = implode("<br>", $tips);
		$title = htmlspecialchars(stripslashes($row->sermon_title),ENT_QUOTES);
		echo JHTML::tooltip($tip,'','',$title,JRoute::_('index.php?option=com_sermonspeaker&view=sermon&id='.$row->slug));
		echo "</li>\n";
	} else {
		echo "<a href=\"".JRoute::_( "index.php?option=com_sermonspeaker&view=sermon&id=$row->slug" )."\">".stripslashes($row->sermon_title)."</a></li>\n";
	} // if mouseover
}
echo "</ul>\n";
