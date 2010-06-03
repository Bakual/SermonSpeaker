<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sermonspeaker'.DS.'tables'); 

class SermonspeakerControllerSpeaker extends SermonspeakerController
{
	/**
	 * Custom Constructor (registers additional tasks to methods)
	 */
	function __construct( $default = array())
	{
		parent::__construct( $default );

		$this->registerTask( 'apply', 		'save');
		$this->registerTask( 'unpublish', 	'publish');
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
				JRequest::setVar('view', 'speaker');
				JRequest::setVar('edit', false);
			} break;
			case 'edit'    :
			{
				JRequest::setVar('hidemainmenu', 1);
				JRequest::setVar('layout', 'form');
				JRequest::setVar('view', 'speaker');
				JRequest::setVar('edit', true);
			} break;
		}
		parent::display();
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');
		
		$row = &JTable::getInstance('speakers', 'Table');
		$post = JRequest::get('post');
		// get the Text Area 'intro' and 'bio' again, but not full *cleaned* by JRequest.
		$post['intro'] = JRequest::getVar('intro', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['bio'] = JRequest::getVar('bio', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$success = $row->save($post);
		if (!$success) {
			JError::raiseError(500, $row->getError());
		}

		switch ($this->_task)
		{
			case 'apply':
				$msg = JText::_('SPEAKER_APPLIED');
				$link = 'index.php?option=com_sermonspeaker&controller=speaker&task=edit&cid[]='.$row->id;
				break;

			case 'save':
			default:
				$msg = JText::_('SPEAKER_SAVED');
				$link = 'index.php?option=com_sermonspeaker&view=speakers';
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

		$msg = JText::_('SPEAKERS_DELETED');
		$row = &JTable::getInstance('speakers', 'Table');

		for ($i=0, $n=count($cid); $i < $n; $i++)
		{
			if (!$row->delete($cid[$i]))
			{
				$msg .= $row->getError();
			}
		}
		$this->setRedirect('index.php?option=com_sermonspeaker&view=speakers', $msg);
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

		$msg = $publish ? JText::_('SPEAKER').' '.JText::_('PUBLISHED') : JText::_('SPEAKER').' '.JText::_('UNPUBLISHED');
		$row = &JTable::getInstance('speakers', 'Table');
		if (!$row->publish($cid,$publish)) {
			$msg = $row->getError();
		}
		
		$this->setRedirect('index.php?option=com_sermonspeaker&view=speakers', $msg);
	}

	function cancel()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		$id	= JRequest::getVar('id', 0);
		$db	= &JFactory::getDBO();
		$row = &JTable::getInstance('speakers', 'Table');
		$row->checkin($id);
		$msg = JText::_('OPERATION CANCELED');
		$this->setRedirect('index.php?option=com_sermonspeaker&view=speakers', $msg );
	}
	
	function saveorder(){
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// Initialize variables
		$db =& JFactory::getDBO();
		$cid    = JRequest::getVar('cid', array(), 'post', 'array');

		$total      = count($cid);
		$order  = JRequest::getVar('order', array(0), 'post', 'array');
		JArrayHelper::toInteger($order, array(0));

		$row        =& JTable::getInstance('speakers', 'Table');

		// update ordering values
		for( $i=0; $i < $total; $i++ )
		{
			$row->load( (int) $cid[$i] );
			// track sections
			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store())
				{
					JError::raiseError(500, $db->getErrorMsg());
				}
			}
		}//for

		$row->reorder();

		$msg    = JText::_('New ordering saved');
		$app = JFactory::getApplication();
		$app->redirect('index.php?option=com_sermonspeaker&view=speakers', $msg);
	}

	function _reOrder($direction) {
		// Check for request forgeries
		JRequest::checkToken() or jexit('Invalid Token');

		// Initialize variables
		$db = & JFactory::getDBO();
		$cid    = JRequest::getVar('cid', array(), 'post', 'array');

		if (isset( $cid[0] )){
			$row = & JTable::getInstance('speakers', 'Table');
			$row->load((int)$cid[0]);
			$row->move($direction);

			$cache = & JFactory::getCache('com_sermonspeaker');
			$cache->clean();
		}

		$app = JFactory::getApplication();
		$app->redirect('index.php?option=com_sermonspeaker&view=speakers');
	}
	
	function orderup() {
		$app = JFactory::getApplication();
		$order = $app->getUserStateFromRequest("com_sermonspeaker.speakers.filter_order_Dir",'filter_order_Dir','','word' );
		if ($order == 'desc') {
			$this->_reOrder(1);
		} else {
			$this->_reOrder(-1);
		}
	}

	function orderdown() {
		$app = JFactory::getApplication();
		$order = $app->getUserStateFromRequest("com_sermonspeaker.speakers.filter_order_Dir",'filter_order_Dir','','word' );
		if ($order == 'desc') {
			$this->_reOrder(-1);
		} else {
			$this->_reOrder(1);
		}
	}
}