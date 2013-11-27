<?php
defined('_JEXEC') or die;

class SermonspeakerModelFiles extends JModelLegacy
{
	public function getItems()
	{
		$audio_ext	= array('aac', 'm4a', 'mp3', 'wma', 'ra', 'ram', 'rm', 'rpm');
		$video_ext	= array('mp4', 'mov', 'f4v', 'flv', '3gp', '3g2', 'wmv', 'rv');
		$start		= strlen(JPATH_SITE)+1;

		$files		= $this->getFiles();
		$sermons	= $this->getSermons();

		$items = array();
		foreach ($files as $key => $value)
		{
			$value = substr($value, $start);
			if (in_array($value, $sermons))
			{
				unset($files[$key]);
				continue;
			}
			$ext = JFile::getExt($value);
			$items[$key]['file'] = '/'.$value;
			if(in_array($ext, $audio_ext)){$items[$key]['type'] = 'audio';}
			elseif(in_array($ext, $video_ext)){$items[$key]['type'] = 'video';}
			else{$items[$key]['type'] = $ext;}
		}

		return $items;
	}

	private function getFiles()
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		$type = $app->getUserStateFromRequest('com_sermonspeaker.tools.filter.type', 'type', 'all', 'string');
		$this->setState('filter.type', $type);

		// Initialize variables.
		$files = array();

		switch ($type){
			case 'audio':
				$filters = array('.aac', '.m4a', '.mp3', '.wma');
				break;
			case 'video':
				$filters = array('.mp4', '.mov', '.f4v', '.flv', '.3gp', '.3g2', '.wmv');
				break;
			default:
				$filters = array('');
				break;
		}
		$params	= JComponentHelper::getParams('com_sermonspeaker');
		$folders[]	= JPATH_SITE.'/'.$params->get('path_audio');
		if ($params->get('path_audio') != $params->get('path_video'))
		{
			$folders[]	= JPATH_SITE.'/'.$params->get('path_video');
		}
		jimport('joomla.filesystem.folder');
		$files	= array();
		foreach ($folders as $folder)
		{
			foreach($filters as $filter){
				$files	= array_merge($files, JFolder::files($folder, $filter, true, true));
			}
		}
		sort($files);

		return $files;
	}

	private function getSermons()
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

	public function getCategory()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id, a.title');
		$query->from('#__categories AS a');
		$query->where('a.parent_id > 0');
		$query->where('extension = "com_sermonspeaker"');
		$query->where('a.published = 1');
		$query->order('a.lft');

		$db->setQuery($query);
		$items = $db->loadObjectList();

		foreach($items as $item)
		{
			if ($item->title == 'Uncategorized')
			{
				return $item->id;
			}
		}

		return $items[0]->id;
	}
}