<?php
defined('_JEXEC') or die;

class SermonspeakerModelFiles extends JModelLegacy
{
	public function getFiles()
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		$type = $app->getUserStateFromRequest('com_sermonspeaker.tools.filter.type', 'type', 'all', 'string');
		$this->setState('filter.type', $type);

		// Initialize variables.
		$files = array();

		switch ($type){
			case 'audio':
				$filters = array('.aac', '.m4a', '.mp3');
				break;
			case 'video':
				$filters = array('.mp4', '.mov', '.f4v', '.flv', '.3gp', '.3g2');
				break;
			default:
				$filters = array('');
				break;
		}
		$params	= JComponentHelper::getParams('com_sermonspeaker');
		$folder	= JPATH_SITE.'/'.$params->get('path');
		jimport('joomla.filesystem.folder');
		$files	= array();
		foreach($filters as $filter){
			$files	= array_merge($files, JFolder::files($folder, $filter, true, true));
		}
		sort($files);

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

		$sermons = $db->loadColumn();

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