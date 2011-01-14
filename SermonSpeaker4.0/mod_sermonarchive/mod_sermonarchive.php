<?php
defined('_JEXEC') or die('Restricted access');

$count		= (int)$params->get('archive_count');
$switch 	= FALSE;
if ($params->get('archive_switch') == 'month'){
	$switch = TRUE;
}
$db = JFactory::getDBO();

$select_m	= NULL;
$group_m	= NULL;
if ($switch){
	$select_m	= ", MONTH(sermon_date) AS created_month";
	$group_m 	= ", created_month DESC";
}
$query	= "SELECT sermon_date, YEAR(sermon_date) AS created_year".$select_m." \n"
		. "FROM #__sermon_sermons \n"
		. "WHERE (state = 1) \n"
		. "GROUP BY created_year DESC".$group_m;

$db->setQuery($query, 0, $count);
$rows = $db->loadObjectList();
// get the menu item from the params
$ss_itemid = $params->get('menuitem');

if(count($rows)) { ?>
	<ul class="sermonarchive<?php echo $moduleclass_sfx; ?>">
	<?php foreach ($rows as $row) {
		$request_m	= NULL;
		$text_m		= NULL;
		if ($switch){
			$request_m	= '&amp;month='.$row->created_month;
			$text_m		= JHTML::Date($row->sermon_date, 'F').', ';
		}
		$link = JRoute::_('index.php?option=com_sermonspeaker&amp;view=archive&amp;year='.$row->created_year.$request_m.'&amp;Itemid='.$ss_itemid);
		$text = $text_m.JHTML::Date($row->sermon_date, 'Y');
		?>
		<li><a href="<?php echo $link; ?>"><?php echo $text; ?></a></li>
		<?php
	} ?>
	</ul>
<?php } ?>
