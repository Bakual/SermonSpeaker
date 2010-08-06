<?php
defined('_JEXEC') or die('Restricted access');

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

// set a menu item
$menu = &JSite::getMenu();
$menuitems = $menu->getItems('link', 'index.php?option=com_sermonspeaker&view=sermons');
if ($menuitems == '') {
	$menuitems = $menu->getItems('component', 'com_sermonspeaker');
}
$ss_itemid = $menuitems[0]->id;
?>
<ul class="<?php echo $params->get('moduleclass_sfx'); ?>">
<?php foreach($rows as $row) { ?>
	<li class="<?php echo $params->get('moduleclass_sfx'); ?>">
	<?php if ($params->get('ls_show_mouseover')) {
		$tips = NULL;
		if ($params->get('ls_show_mo_speaker')) {
			$tips[] = JText::_('MOD_LATESTSERMONS_SPEAKER').": ".$row->name;
		}
		if ($params->get('ls_show_mo_series')) {
			$tips[] = JText::_('MOD_LATESTSERMONS_SERIE').": ".trim($row->series_title);
		}
		if ($params->get('ls_show_mo_date')) {
			$date_format = JText::_($params->get('ls_mo_date_format', '%Y-%M-%D'));
			$tips[] = JText::_('MOD_LATESTSERMONS_DATE').": ".JHtml::Date($row->sermon_date, $date_format, 0);
		}
		$tip = implode('<br>', $tips);
		$title = htmlspecialchars(stripslashes($row->sermon_title), ENT_QUOTES);
		echo JHTML::tooltip($tip, '', '', $title, JRoute::_('index.php?option=com_sermonspeaker&view=sermon&id='.$row->slug.'&Itemid='.ss_itemid)); ?>
		</li>
	<?php } else { ?>
		<a href="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=sermon&id='.$row->slug.'&Itemid='.ss_itemid); ?>"><?php echo stripslashes($row->sermon_title); ?></a></li>
	<?php } // if mouseover
} ?>
</ul>
<?php if ($params->get('ls_show_mo_link')) { ?>
	<br>
	<a href="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=sermons&Itemid='.ss_itemid); ?>"><?php echo JText::_('MOD_LATESTSERMONS_LINK'); ?></a>
<?php } ?>