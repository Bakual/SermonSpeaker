<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2019 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Sermonspeaker Component ID3 Helper
 *
 * @since  3.4
 */
class SermonspeakerHelperId3
{
	/**
	 * Read ID3 tags from file
	 *
	 * @param   string $file   Path to the file
	 * @param   object $params Params, deprecated
	 *
	 * @return array|bool
	 *
	 * @since ?
	 */
	public static function getID3($file, $params = null)
	{
		if (strpos($file, 'http://vimeo.com') === 0 || strpos($file, 'http://player.vimeo.com') === 0)
		{
			return self::getVimeo($file);
		}

		require_once JPATH_COMPONENT_SITE . '/id3/getid3/getid3.php';
		$getID3 = new getID3;
		$path = JPATH_SITE . $file;
		$path = str_replace('//', '/', $path);

		if (!file_exists($path))
		{
			return false;
		}

		$FileInfo = $getID3->analyze($path);
		getid3_lib::CopyTagsToComments($FileInfo);
		$id3 = array();

		if (array_key_exists('playtime_seconds', $FileInfo))
		{
			$length = $FileInfo['playtime_seconds'];
			$hrs = (int) ($length / 3600);
			$min = (int) (($length - $hrs * 3600) / 60);
			$sec = (int) ($length - $hrs * 3600 - $min * 60);

			if ($sec == '60')
			{
				$sec = 0;
				$min++;
			}

			$id3['sermon_time'] = $hrs . ':' . sprintf('%02d', $min) . ':' . sprintf('%02d', $sec);
		}
		else
		{
			$id3['sermon_time'] = '';
		}

		if (array_key_exists('audio', $FileInfo))
		{
			$audio = array();
			$audio['channelmode'] = (array_key_exists('channelmode', $FileInfo['audio'])) ? $FileInfo['audio']['channelmode'] : '';
			$audio['bitrate'] = (array_key_exists('bitrate', $FileInfo['audio'])) ? $FileInfo['audio']['bitrate'] . ' bps' : '';
			$audio['sample_rate'] = (array_key_exists('sample_rate', $FileInfo['audio'])) ? $FileInfo['audio']['sample_rate'] . ' Hz' : '';

			$id3['audio'] = $audio;
		}

		if (array_key_exists('comments', $FileInfo))
		{
			if (array_key_exists('title', $FileInfo['comments']))
			{
				$id3['title'] = end($FileInfo['comments']['title']);
			}
			else
			{
				$id3['title'] = JFile::stripExt(basename($file));
			}

			$id3['alias'] = JApplicationHelper::stringURLSafe($id3['title']);

			if (array_key_exists('track', $FileInfo['comments']))
			{
				$id3['sermon_number'] = $FileInfo['comments']['track'][0];
			}
			else
			{
				$id3['sermon_number'] = '';
			}

			if (array_key_exists('year', $FileInfo['comments']) && array_key_exists('date', $FileInfo['comments']))
			{
				$ddmm = $FileInfo['comments']['date'][0];
				$id3['sermon_date'] = $FileInfo['comments']['year'][0] . '-' . substr($ddmm, 2, 2) . '-' . substr($ddmm, 0, 2);

				if (array_key_exists('time', $FileInfo['comments']))
				{
					$hhmm = $FileInfo['comments']['time'][0];
					$id3['sermon_date'] .= ' ' . substr($hhmm, 0, 2) . ':' . substr($hhmm, 2, 2) . ':00';
				}
			}
			else
			{
				$id3['sermon_date'] = '';
			}

			if (array_key_exists('comment', $FileInfo['comments']))
			{
				$id3['notes'] = end($FileInfo['comments']['comment']);
				$id3['scripture'] = '';
			}
			else
			{
				$id3['notes'] = '';
				$id3['scripture'] = '';
			}

			$db = JFactory::getDbo();

			if (array_key_exists('album', $FileInfo['comments']))
			{
				$query = $db->getQuery(true);
				$query->select('id');
				$query->from('#__sermon_series');
				$query->where('title like ' . $db->quote($db->escape(end($FileInfo['comments']['album']))));
				$db->setQuery($query);
				$id3['series_id'] = $db->loadResult();

				if (!$id3['series_id'])
				{
					$id3['not_found']['series'] = end($FileInfo['comments']['album']);
				}
			}
			else
			{
				$id3['series_id'] = '';
			}

			if (array_key_exists('artist', $FileInfo['comments']))
			{
				$query = $db->getQuery(true);
				$query->select('id');
				$query->from('#__sermon_speakers');
				$query->where('title like ' . $db->quote($db->escape(end($FileInfo['comments']['artist']))));
				$db->setQuery($query);
				$id3['speaker_id'] = $db->loadResult();

				if (!$id3['speaker_id'])
				{
					$id3['not_found']['speakers'] = end($FileInfo['comments']['artist']);
				}
			}
			else
			{
				$id3['speaker_id'] = '';
			}
		}
		else
		{
			$id3['title'] = JFile::stripExt(basename($path));
			$id3['alias'] = JApplicationHelper::stringURLSafe($id3['title']);
			$id3['sermon_number'] = '';
			$id3['notes'] = '';
			$id3['scripture'] = '';
			$id3['series_id'] = '';
			$id3['speaker_id'] = '';
		}

		$id3['filesize'] = filesize($path);

		return $id3;
	}

	/**
	 * Get Vimeo data
	 *
	 * @param   string $file Path to the file
	 *
	 * @return  array|bool  Array of Vimeo informations
	 *
	 * @since ?
	 */
	private static function getVimeo($file)
	{
		$id = trim(strrchr($file, '/'), '/ ');
		$url = 'http://vimeo.com/api/v2/video/' . $id . '.xml';
		$xml = simplexml_load_file($url);
		/** @var SimpleXMLElement $video */
		$video = $xml->video;

		if (is_object($video))
		{
			$duration = (string) $video->duration;
			$hrs = (int) ($duration / 3600);
			$min = (int) (($duration - $hrs * 3600) / 60);
			$sec = (int) ($video->duration - $hrs * 3600 - $min * 60);
			$id3['sermon_time'] = $hrs . ':' . sprintf('%02d', $min) . ':' . sprintf('%02d', $sec);
			$id3['title'] = (string) $video->title;
			$id3['alias'] = JApplicationHelper::stringURLSafe($id3['title']);
			$id3['sermon_date'] = (string) $video->upload_date;
			$id3['notes'] = (string) $video->description;
			$id3['pic'] = $video->thumbnail_medium;
			$id3['sermon_number'] = '';
			$id3['scripture'] = '';
			$id3['series_id'] = '';
			$id3['speaker_id'] = '';

			return $id3;
		}

		return false;
	}
}
