<?php
// Plugin for JComments, manually copy to the JComments folder '/components/com_jcomments/plugins' if you want to use it.
class jc_com_sermonspeaker extends JCommentsPlugin {
	function getObjectTitle($id) {
		// Data load from database by given id 
		$db = & JFactory::getDBO();
		$db->setQuery("SELECT sermon_title FROM #__sermon_sermons WHERE id='$id'");
		return $db->loadResult();
	}
	function getObjectLink($id) {
		// Itemid meaning of our component
		$_Itemid = JCommentsPlugin::getItemid('com_sermonspeaker');
		// url link creation for given object by id 
		$link = JRoute::_('index.php?option=com_sermonspeaker&view=sermon&id='.$id.'&Itemid='.$_Itemid);
		return $link;
	}
	function getObjectOwner($id) {
		$db = & JFactory::getDBO();
		$db->setQuery('SELECT created_by, id FROM #__sermon_sermons WHERE id = '.$id);
		return $db->loadResult();
	}
}
?>