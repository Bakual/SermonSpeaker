<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Site\Helper;

use getID3;
use getid3_lib;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Joomla\Filesystem\File;

defined('_JEXEC') or die();

/**
 * Sermonspeaker Component ID3 Helper
 *
 * @since  3.4
 */
class Id3Helper
{
	/**
	 * Read ID3 tags from file
	 *
	 * @param   string       $file    Path to the file
	 *
	 * @return array|bool
	 *
	 * @since ?
	 */
	public static function getID3(string $file): bool|array
	{
		// Load Composer Autoloader
		require_once(JPATH_ADMINISTRATOR . '/components/com_sermonspeaker/vendor/autoload.php');

		$getID3 = new getID3;
		$path   = JPATH_SITE . '/' . $file;
		$path   = str_replace('//', '/', $path);

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
			$hrs    = (int) ($length / 3600);
			$min    = (int) (($length - $hrs * 3600) / 60);
			$sec    = (int) ($length - $hrs * 3600 - $min * 60);

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
			$audio                = array();
			$audio['channelmode'] = (array_key_exists('channelmode', $FileInfo['audio'])) ? $FileInfo['audio']['channelmode'] : '';
			$audio['bitrate']     = (array_key_exists('bitrate', $FileInfo['audio'])) ? $FileInfo['audio']['bitrate'] . ' bps' : '';
			$audio['sample_rate'] = (array_key_exists('sample_rate', $FileInfo['audio'])) ? $FileInfo['audio']['sample_rate'] . ' Hz' : '';

			$id3['audio'] = $audio;
		}

		if (array_key_exists('comments', $FileInfo))
		{
			if (array_key_exists('title', $FileInfo['comments']))
			{
				$id3['title'] = $FileInfo['comments']['title'][0];
			}
			else
			{
				$id3['title'] = File::stripExt(basename($file));
			}

			$id3['alias'] = ApplicationHelper::stringURLSafe($id3['title']);

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
				$ddmm               = $FileInfo['comments']['date'][0];
				$id3['sermon_date'] = $FileInfo['comments']['year'][0] . '-' . substr($ddmm, 2, 2) . '-' . substr($ddmm, 0, 2);

				if (array_key_exists('time', $FileInfo['comments']))
				{
					$hhmm               = $FileInfo['comments']['time'][0];
					$id3['sermon_date'] .= ' ' . substr($hhmm, 0, 2) . ':' . substr($hhmm, 2, 2) . ':00';
				}
			}
			elseif (array_key_exists('year', $FileInfo['comments']))
			{
				$date = $FileInfo['comments']['year'][0];

				// Check how much information is stored in that field. Can be anything from only a year up to full datetime.
				$length = strlen($date);
				$id3['sermon_date'] = match ($length)
				{
					4 => $date . '-01-01 00:00:00',
					8 => substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2) . ' 00:00:00',
					10 => $date . ' 00:00:00',
					16 => substr($date, 0, 10) . ' ' . substr($date, 11, 5) . ':00',
					19 => substr($date, 0, 10) . ' ' . substr($date, 11, 8),
					default => '',
				};
			}
			else
			{
				$id3['sermon_date'] = '';
			}

			if (array_key_exists('comment', $FileInfo['comments']))
			{
				$id3['notes']     = $FileInfo['comments']['comment'][0];
			}
			else
			{
				$id3['notes']     = '';
			}

			$id3['scripture'] = '';

			if (array_key_exists('album', $FileInfo['comments']))
			{
				$album  = (string) $FileInfo['comments']['album'][0];
				$result = self::findByTitle('#__sermon_series', $album);

				if ($result)
				{
					$id3['series_id']    = $result['id'];
					$id3['series_title'] = $result['title'];
				}
				else
				{
					$id3['not_found']['series'] = $FileInfo['comments']['album'][0];
					$id3['series_id']    = '';
					$id3['series_title'] = '';
				}
			}
			else
			{
				$id3['series_id']    = '';
				$id3['series_title'] = '';
			}

			if (array_key_exists('artist', $FileInfo['comments']))
			{
				$artist = (string) $FileInfo['comments']['artist'][0];
				$result = self::findByTitle('#__sermon_speakers', $artist);

				if ($result)
				{
					$id3['speaker_id']    = $result['id'];
					$id3['speaker_title'] = $result['title'];
				}
				else
				{
					$id3['not_found']['speakers'] = $FileInfo['comments']['artist'][0];
					$id3['speaker_id']    = '';
					$id3['speaker_title'] = '';
				}
			}
			else
			{
				$id3['speaker_id']    = '';
				$id3['speaker_title'] = '';
			}
		}
		else
		{
			$id3['title']         = File::stripExt(basename($path));
			$id3['alias']         = ApplicationHelper::stringURLSafe($id3['title']);
			$id3['sermon_number'] = '';
			$id3['notes']         = '';
			$id3['scripture']     = '';
			$id3['series_id']     = '';
			$id3['speaker_id']    = '';
		}

		$id3['filesize'] = filesize($path);

		return $id3;
	}

	/**
	 * Sucht einen Datensatz (id, title) anhand des Titels.
	 *
	 * @param   string  $table  Tabellenname mit #__-Präfix (fest im Code, nie aus Benutzereingaben)
	 * @param   string  $title  Gesuchter Titel
	 *
	 * @return  array|null
	 */
	private static function findByTitle(string $table, string $title): ?array
	{
		$db = Factory::getContainer()->get(DatabaseInterface::class);

		$query = $db->createQuery()
			->select($db->quoteName(['id', 'title']))
			->from($db->quoteName($table))
			->where($db->quoteName('title') . ' = :title')
			->bind(':title', $title, ParameterType::STRING)
			->setLimit(1);

		$db->setQuery($query);

		return $db->loadAssoc() ?: null;
	}
}
