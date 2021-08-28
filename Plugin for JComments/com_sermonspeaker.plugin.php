<?php
// Plugin for JComments, manually copy to the JComments folder '/components/com_jcomments/plugins' if you want to use it.
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

class jc_com_sermonspeaker extends JCommentsPlugin {
	function getObjectTitle($id) {
		// Data load from database by given id 
		$db = Factory::getDbo();
		$db->setQuery("SELECT title FROM #__sermon_sermons WHERE id='$id'");
		return $db->loadResult();
	}
	function getObjectLink($id) {
		// Itemid meaning of our component
		$_Itemid = JCommentsPlugin::getItemid('com_sermonspeaker');
		// url link creation for given object by id 
		return Route::_('index.php?option=com_sermonspeaker&view=sermon&id='.$id.'&Itemid='.$_Itemid);
	}
	function getObjectOwner($id) {
		$db = Factory::getDbo();
		$db->setQuery('SELECT created_by, id FROM #__sermon_sermons WHERE id = '.$id);
		return $db->loadResult();
	}
}
