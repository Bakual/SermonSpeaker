<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class SermonspeakerModelFiles extends JModel
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function getFiles()
	{
		// Initialize variables.
		$files = array();

		$params	= JComponentHelper::getParams('com_sermonspeaker');
		$folder	= JPATH_SITE.DS.$params->get('path');
		jimport('joomla.filesystem.folder');
		$files = JFolder::files($folder, '', true, true);

		return $files;
	}

	public function getSermons()
	{
		// Initialize variables.
		$options = array();

		$db		= JFactory::getDbo();
		$query	= "SELECT `audiofile` AS `file` FROM #__sermon_sermons WHERE `audiofile` != '' \n"
				. "UNION SELECT `videofile` FROM #__sermon_sermons WHERE `videofile` != '' ";

		$db->setQuery($query);

		$sermons = $db->loadResultArray();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		foreach($sermons as &$sermon){
			$sermon = trim($sermon, '/\\');
		}

		return $sermons;
	}
}