<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sermonspeaker'.DS.'tables'); 

class SermonspeakerControllerSermon extends SermonspeakerController
{
	/**
	 * Custom Constructor (registers additional tasks to methods)
	 */
	function __construct( $default = array())
	{
		parent::__construct( $default );

		$this->registerTask( 'apply', 		'save');
		$this->registerTask( 'unpublish', 	'publish');
		$this->registerTask( 'unpodcast', 	'podcast');
		$this->registerTask( 'edit', 		'display');
		$this->registerTask( 'add' , 		'display' );
	}

	function display( )
	{
		switch($this->getTask())
		{
			case 'add'     :
			{
				JRequest::setVar('hidemainmenu', 1);
				JRequest::setVar('layout', 'form');
				JRequest::setVar('view', 'sermon');
				JRequest::setVar('edit', false);
			} break;
			case 'edit'    :
			{
				JRequest::setVar('hidemainmenu', 1);
				JRequest::setVar('layout', 'form');
				JRequest::setVar('view', 'sermon');
				JRequest::setVar('edit', true);
			} break;
		}
		parent::display();
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		$row = &JTable::getInstance('sermons', 'Table');
		$post = JRequest::get('post');
		// get the Text Area 'notes' again, but not full *cleaned* by JRequest.
		$post['notes'] = JRequest::getVar('notes', '', 'post', 'string', JREQUEST_ALLOWRAW);
		if ($post['sermon_path_choice']){
			$post['sermon_path'] = $post['sermon_path_choice'];
			unset($post['sermon_path_choice']);
		} elseif ($post['sermon_path_txt']){
			$post['sermon_path'] = $post['sermon_path_txt'];
			unset($post['sermon_path_txt']);
		}
		if ($post['addfile_choice']){
			$post['addfile'] = $post['addfile_choice'];
			unset($post['addfile_choice']);
		} elseif ($post['addfile_txt']){
			$post['addfile'] = $post['addfile_txt'];
			unset($post['addfile_txt']);
		}
		if(empty($post['alias'])) {
				$post['alias'] = $post['sermon_title'];
		}
		$post['alias'] = JFilterOutput::stringURLSafe($post['alias']);

		$success = $row->save($post);
		if (!$success) {
			JError::raiseError(500, $row->getError());
		}

		switch ($this->_task)
		{
			case 'apply':
				$msg = JText::_('COM_SERMONSPEAKER_SERMON_APPLIED');
				$link = 'index.php?option=com_sermonspeaker&controller=sermon&task=edit&cid[]='.$row->id;
				break;

			case 'save':
			default:
				$msg = JText::_('COM_SERMONSPEAKER_SERMON_SAVED');
				$link = 'index.php?option=com_sermonspeaker&view=sermons';
				break;
		}

		$this->setRedirect($link, $msg);
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);

		$msg = JText::_('COM_SERMONSPEAKER_SERMONS_DELETED');
		$row = &JTable::getInstance('sermons', 'Table');

		JImport('joomla.filesystem.file');
		for ($i=0, $n=count($cid); $i < $n; $i++){
			if (!$row->load($cid[$i])){
				$msg .= $row->getError();
			} else {
				if($row->sermon_path && file_exists(JPATH_ROOT.$row->sermon_path)){
					JFile::delete(JPATH_ROOT.$row->sermon_path);
				}
			}
			if (!$row->delete($cid[$i])){
				$msg .= $row->getError();
			}
		}
		$this->setRedirect('index.php?option=com_sermonspeaker&view=sermons', $msg);
	}

	/**
	* Publishes or Unpublishes one or more records
	*/
	function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		$publish = ($this->getTask() == 'publish' ? 1 : 0);

		if (count($cid) < 1) {
			$action = $publish ? JText::_('PUBLISH') : JText::_('UNPUBLISH');
			JError::raiseError(500, JText::_('SELECT_ITEM_TO'.$action, true));
		}

		$msg = $publish ? JText::_('SERMON').' '.JText::_('PUBLISHED') : JText::_('SERMON').' '.JText::_('UNPUBLISHED');
		$row = &JTable::getInstance('sermons', 'Table');
		if (!$row->publish($cid,$publish)) {
			$msg = $row->getError();
		}
		
		$this->setRedirect('index.php?option=com_sermonspeaker&view=sermons', $msg);
	}

	function podcast() {
		$cid = JRequest::getVar('cid', array(), '', 'array');
		JArrayHelper::toInteger($cid);
		$podcast = ($this->getTask() == 'podcast' ? 1 : 0);

		if (count($cid) < 1) {
			$action = $podcast ? JText::_('PODCAST') : JText::_('UNPODCAST');
			JError::raiseError(500, JText::_('SELECT_ITEM_TO'.$action, true));
		}
		$msg = $podcast ? JText::_('SERMON').' '.JText::_('COM_SERMONSPEAKER_PODCASTED') : JText::_('SERMON').' '.JText::_('COM_SERMONSPEAKER_UNPODCASTED');
		$ids = (implode(', ',$cid));

		$database =& JFactory::getDBO();
		$query = "UPDATE `#__sermon_sermons` SET `podcast` = '".$podcast."' WHERE `id` IN (".$ids.")";
		$database->setQuery( $query );
		$database->query();
		if($database->getErrorMsg()) {
			$msg = $database->getErrorMsg();
		}

		$this->setRedirect('index.php?option=com_sermonspeaker&view=sermons', $msg);
	}

	function cancel()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$id	= JRequest::getInt('id', 0);
		$db	= &JFactory::getDBO();
		$row = &JTable::getInstance('sermons', 'Table');
		$row->checkin($id);
		$msg = JText::_('COM_SERMONSPEAKER_CANCELED');
		$this->setRedirect('index.php?option=com_sermonspeaker&view=sermons', $msg );
	}

	function resetcount()
	{
		$database	= &JFactory::getDBO();
		$id 		= JRequest::getInt('id', 0);

		$query	= "UPDATE #__sermon_sermons \n"
				. "SET hits='0' \n"
				. "WHERE id='".$id."'"
				;
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}

	$this->setRedirect("index.php?option=com_sermonspeaker&controller=sermon&task=edit&cid[]=".$id);
	}
}