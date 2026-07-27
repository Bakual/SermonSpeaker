<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\Model;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die();

/**
 * Model class for the SermonSpeaker Component
 *
 * @since  ?.?
 */
class FilesModel extends BaseDatabaseModel
{
	public function getItems()
	{
		$audio_ext = array('aac', 'm4a', 'mp3', 'wma', 'ra', 'ram', 'rm', 'rpm');
		$video_ext = array('mp4', 'mov', 'f4v', 'flv', '3gp', '3g2', 'wmv', 'rv');
		$start     = strlen(JPATH_SITE) + 1;
		$files     = $this->getFiles();
		$sermons   = $this->getSermons();
		$items     = array();

		foreach ($files as $key => $value)
		{
			if (str_starts_with($value, JPATH_SITE))
			{
				$value = substr($value, $start);
			}

			$value = str_replace('\\', '/', $value);

			if (in_array($value, $sermons))
			{
				unset($files[$key]);
				continue;
			}

			$ext                 = File::getExt($value);
			$items[$key]['file'] = (str_starts_with($value, 'http')) ? $value : '/' . $value;

			if (in_array($ext, $audio_ext))
			{
				$items[$key]['type'] = 'audio';
			}
			elseif (in_array($ext, $video_ext))
			{
				$items[$key]['type'] = 'video';
			}
			else
			{
				$items[$key]['type'] = $ext;
			}
		}

		return $items;
	}

	private function getFiles()
	{
		// Initialise variables.
		$app    = Factory::getApplication();
		$params = ComponentHelper::getParams('com_sermonspeaker');

		$type = $app->getUserStateFromRequest('com_sermonspeaker.tools.filter.type', 'type', 'all', 'string');
		$this->setState('filter.type', $type);

		switch ($type)
		{
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

		$folders[] = JPATH_SITE . '/' . $params->get('path_audio');
		$files     = array();

		// Local files
		if ($params->get('path_audio') != $params->get('path_video'))
		{
			$folders[] = JPATH_SITE . '/' . $params->get('path_video');
		}

		foreach ($folders as $folder)
		{
			foreach ($filters as $filter)
			{
				$files = array_merge($files, Folder::files($folder, $filter, true, true));
			}
		}

		sort($files);

		return $files;
	}

	private function getSermons()
	{
		$db    = $this->getDatabase();
		$query = "SELECT `audiofile` AS `file` FROM #__sermon_sermons WHERE `audiofile` != '' \n"
			. "UNION SELECT `videofile` FROM #__sermon_sermons WHERE `videofile` != '' ";

		$db->setQuery($query);

		try
		{
			$sermons = $db->loadColumn();

		}
		catch (\Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'ERROR');
			$sermons = array();
		}

		foreach ($sermons as &$sermon)
		{
			$sermon = trim($sermon, '/\\');
		}

		return $sermons;
	}

	public function getCategory()
	{
		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		$query->select('a.id, a.title');
		$query->from('#__categories AS a');
		$query->where('a.parent_id > 0');
		$query->where('extension = "com_sermonspeaker.sermons"');
		$query->where('a.published = 1');
		$query->order('a.lft');

		$db->setQuery($query);
		$items = $db->loadObjectList();

		foreach ($items as $item)
		{
			if ($item->title == 'Uncategorized')
			{
				return $item->id;
			}
		}

		return $items[0]->id;
	}
}
