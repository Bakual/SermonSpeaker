<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Model class for the SermonSpeaker Component
 *
 * @since  ?.?
 */
class SermonspeakerModelFiles extends JModelLegacy
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
			if (strpos($value, JPATH_SITE) === 0)
			{
				$value = substr($value, $start);
			}

			$value = str_replace('\\', '/', $value);

			if (in_array($value, $sermons))
			{
				unset($files[$key]);
				continue;
			}

			$ext                 = JFile::getExt($value);
			$items[$key]['file'] = (strpos($value, 'http') === 0) ? $value : '/' . $value;

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
		$app    = JFactory::getApplication();
		$params = JComponentHelper::getParams('com_sermonspeaker');

		$type = $app->getUserStateFromRequest('com_sermonspeaker.tools.filter.type', 'type', 'all', 'string');
		$this->setState('filter.type', $type);

		switch ($type)
		{
			case 'audio':
				$filters = array('.aac', '.m4a', '.mp3', '.wma');
				$mode    = $params->get('path_mode_audio', 0);

				break;
			case 'video':
				$filters = array('.mp4', '.mov', '.f4v', '.flv', '.3gp', '.3g2', '.wmv');
				$mode    = $params->get('path_mode_video', 0);

				break;
			default:
				$filters = array('');

				if (!$mode = $params->get('path_mode_audio', 0))
				{
					$mode = $params->get('path_mode_video', 0);
				}

				break;
		}

		$folders[] = JPATH_SITE . '/' . $params->get('path_audio');
		$files     = array();

		if ($mode == 2)
		{
			// Add missing constant in PHP < 5.5
			defined('CURL_SSLVERSION_TLSv1') or define('CURL_SSLVERSION_TLSv1', 1);

			// Amazon S3

			// AWS access info
			$awsAccessKey = $params->get('s3_access_key');
			$awsSecretKey = $params->get('s3_secret_key');
			$bucket       = $params->get('s3_bucket');
			$s3 = (new \Aws\Sdk)->createMultiRegionS3([
				'version'     => '2006-03-01',
				'credentials' => [
					'key'    => $awsAccessKey,
					'secret' => $awsSecretKey,
				],
			]);

			$bucket_contents = $s3->listObjects(['Bucket' => $bucket])['Contents'];

			foreach ($bucket_contents as $file)
			{
				$files[] = $s3->getObjectUrl($bucket, $file['Key']);;
			}
		}

		// Local files
		if ($params->get('path_audio') != $params->get('path_video'))
		{
			$folders[] = JPATH_SITE . '/' . $params->get('path_video');
		}

		foreach ($folders as $folder)
		{
			foreach ($filters as $filter)
			{
				$files = array_merge($files, JFolder::files($folder, $filter, true, true));
			}
		}

		sort($files);

		return $files;
	}

	private function getSermons()
	{
		$db    = JFactory::getDbo();
		$query = "SELECT `audiofile` AS `file` FROM #__sermon_sermons WHERE `audiofile` != '' \n"
			. "UNION SELECT `videofile` FROM #__sermon_sermons WHERE `videofile` != '' ";

		$db->setQuery($query);

		try
		{
			$sermons = $db->loadColumn();

		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'ERROR');
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
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id, a.title');
		$query->from('#__categories AS a');
		$query->where('a.parent_id > 0');
		$query->where('extension = "com_sermonspeaker"');
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