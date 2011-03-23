<?php
/**
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.controllerform');

/**
 * Frontendupload Sermonspeaker Controller
 *
 */
class SermonspeakerControllerFrontendupload extends JControllerForm
{
	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 *
	 * @return	JControllerForm
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		// Redirect View, defined as it would go to plural version otherwise
		$this->view_list = 'frontendupload';

		parent::__construct($config);
	}

	/**
	 * Method override to check if you can add a new record.
	 * Quite useless now, but may change if we add ACLs to SermonSpeaker
	 *
	 * @param	array	$data	An array of input data.
	 * @return	boolean
	 */
	protected function allowAdd($data = array())
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$categoryId	= JArrayHelper::getValue($data, 'catid', JRequest::getInt('filter_category_id'), 'int');
		$allow		= null;

		if ($categoryId) {
			// If the category has been passed in the data or URL check it.
			$allow	= $user->authorise('core.create', 'com_sermonspeaker.category.'.$categoryId);
		}

		if ($allow === null) {
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd();
		} else {
			return $allow;
		}
	}

	function blubb(){
		// Check for request forgeries
		JRequest::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
		JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sermonspeaker'.DS.'tables');
		$table = JTable::getInstance('Sermon', 'SermonspeakerTable');

		// Reorder the articles within the category so the new article is first
		if (empty($table->id)) {
			$table->reorder('catid = '.(int) $table->catid.' AND state >= 0');
		}

		// Initialise variables.
		$app		= JFactory::getApplication();
		$params		= $app->getParams();


		$db =& JFactory::getDBO();
		$sql['speaker_id'] 			= JRequest::getInt('speaker_id', '', 'POST');
		$sql['series_id'] 			= JRequest::getInt('series_id', '', 'POST');
		$sql['audiofile']			= $db->getEscaped(JRequest::getString('audiofile', '', 'POST'));
		$sql['videofile']			= $db->getEscaped(JRequest::getString('videofile', '', 'POST'));
		$sql['sermon_title']		= $db->getEscaped(JRequest::getString('sermon_title', '', 'POST'));
		$sql['alias']				= JRequest::getString('alias', $sql['sermon_title'], 'POST');
		$sql['alias']				= $db->getEscaped(JFilterOutput::stringURLSafe($sql['alias']));
		$sql['sermon_number']		= JRequest::getInt('sermon_number', '', 'POST');
		$sql['sermon_scripture']	= $db->getEscaped(JRequest::getString('sermon_scripture', '', 'POST'));
		$sql['sermon_date']			= $db->getEscaped(JRequest::getString('sermon_date', '', 'POST'));
		// making sure that the time is valid formatted
		$tarr = explode(':',JRequest::getString('sermon_time', '', 'POST'));
		foreach ($tarr as $tar){
			$tar = (int)$tar;
			$tar = str_pad($tar, 2, '0', STR_PAD_LEFT);
		}
		if (count($tarr) == 2) {
			$sql['sermon_time'] = '00:'.$tarr[0].':'.$tarr[1];
		} elseif (count($tarr) == 3) {
			$sql['sermon_time'] = $tarr[0].':'.$tarr[1].':'.$tarr[2];
		}
		$sql['notes']		= $db->getEscaped(JRequest::getVar('notes', '', '', 'STRING', JREQUEST_ALLOWHTML));
		$sql['state']		= JRequest::getInt('state', '0', 'POST');
		$sql['podcast']		= JRequest::getInt('podcast', '0', 'POST');
		$user =& JFactory::getUser();
		$sql['created_by']	= $user->id;
		$sql['created']		= date('Y-m-d');
		$sql['catid']		= JRequest::getInt('catid', '0', 'POST');
		$sql['addfile']		= $db->getEscaped(JRequest::getString('addfile_choice', JRequest::getString('addfile_text', '', 'POST'), 'POST'));
		$sql['addfileDesc']	= $db->getEscaped(JRequest::getString('addfileDesc', '', 'POST'));

		$keys	= implode('`,`', array_keys($sql));
		$values = implode("','", $sql);
		$query	= "INSERT INTO #__sermon_sermons \n"
				. "(`".$keys."`) \n"
				. "VALUES ('".$values."')";
		$db->setQuery($query);
		if (!$db->query()) { die("SQL error".$db->stderr(true)); }
		$app->redirect(JRoute::_('index.php?view=frontendupload'), JText::_('COM_SERMONSPEAKER_FU_UPSAVEDOK'));
		return;
	}
}