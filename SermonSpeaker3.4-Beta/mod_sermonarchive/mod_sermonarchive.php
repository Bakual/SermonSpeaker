<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$count		= intval($params->get('count'));
$database 	= &JFactory::getDBO();

$query	= "SELECT MONTH(sermon_date) AS created_month, sermon_date, YEAR(sermon_date) AS created_year \n"
		. "FROM #__sermon_sermons \n"
		. "WHERE (published = 1) \n"
		. "GROUP BY created_year DESC, created_month DESC";

$database->setQuery( $query, 0, $count );
$rows = $database->loadObjectList();

$menu = &JSite::getMenu();
$menuitems = $menu->getItems('link', 'index.php?option=com_sermonspeaker&view=sermons');
if ($menuitems == "") {
	$menuitems = $menu->getItems('component', 'com_sermonspeaker');
}

$sermonspeaker_itemid = $menuitems[0]->id;
if(count($rows)) {
	echo '<ul>';
	foreach ( $rows as $row ) {
		$link = 'index.php?option=com_sermonspeaker&amp;view=archive&amp;year='.$row->created_year.'&amp;month='.$row->created_month.'&amp;Itemid='.$sermonspeaker_itemid;
		$text = JHTML::date($row->sermon_date, '%B').', '.JHTML::date($row->sermon_date, '%Y');
		?>
		<li><a href="<?php echo JURI::root().$link; ?>"><?php echo $text; ?></a></li>
		<?php
	}
	echo '</ul>';
} // end of if
?>
