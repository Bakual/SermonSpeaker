<?php
/**
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Serie Sermonspeaker Controller
 *
 */
class SermonspeakerControllerSerie extends JControllerLegacy
{
	function download()
	{
		$id = JRequest::getInt('id');
		if (!$id)
		{
			$response = array(
				'status' => '0',
				'msg' => JText::_('I have no clue what you want to download...')
			);
			echo json_encode($response);
			return;
		}

		$db = JFactory::getDBO();
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());

		// Checking if file needs to be prepared
		$query	= $db->getQuery(true);
		$query->select('zip_content');
		$query->from('#__sermon_series');
		$query->where('id = '.$id);
		$query->where('zip_progress = 100');
		$query->where('zip_created > modified');
		$query->where('zip_created > (SELECT modified FROM #__sermon_sermons WHERE series_id = '.$id.' ORDER BY modified DESC LIMIT 1)');
		$db->setQuery($query);
		$zip_content = $db->loadResult();

		$query	= $db->getQuery(true);
		$query->select('sermons.id, sermons.audiofile, sermons.videofile, series.series_title');
		$query->from('#__sermon_sermons as sermons');
		$query->join('INNER', '#__sermon_series AS series ON series.id = sermons.series_id');
		$query->join('LEFT', '#__sermon_speakers AS speakers ON speakers.id = sermons.speaker_id');
		$query->join('LEFT', '#__categories AS c_sermons ON c_sermons.id = sermons.catid');
		$query->join('LEFT', '#__categories AS c_speaker ON c_speaker.id = speakers.catid');
		$query->join('LEFT', '#__categories AS c_series ON c_series.id = series.catid');
		$query->where('sermons.series_id = '.$id);
		$query->where('(series.catid = 0 OR (c_series.access IN ('.$groups.') AND c_series.published = 1))');
		$query->where('(sermons.catid = 0 OR (c_sermons.access IN ('.$groups.') AND c_sermons.published = 1))');
		$query->where('(sermons.speaker_id = 0 OR speakers.catid = 0 OR (c_speaker.access IN ('.$groups.') AND c_speaker.published = 1))');
		$db->setQuery($query);
		$rows = $db->loadAssocList();
		jimport('joomla.filesystem.file');
		$files = array();
		$content = array();
		foreach ($rows as $row)
		{
			if ($row['audiofile'] && (substr($row['audiofile'], 0, 7) != 'http://') && JFile::exists(JPATH_BASE.'/'.$row['audiofile']))
			{
				$file['path'] = JPATH_BASE.'/'.$row['audiofile'];
				$slash = strrpos($row['audiofile'], '/');
				if ($slash !== false)
				{
					$file['name'] = substr($row['audiofile'], $slash + 1);
				}
				else
				{
					$file['name'] = $row['audiofile'];
				}
				$files[] = $file;
				$content[] = 'a'.$row['id'];
			}
			if ($row['videofile'] && (substr($row['videofile'], 0, 7) != 'http://') && JFile::exists(JPATH_BASE.'/'.$row['videofile']))
			{
				$file['path'] = JPATH_BASE.'/'.$row['videofile'];
				$slash = strrpos($row['videofile'], '/');
				if ($slash !== false)
				{
					$file['name'] = substr($row['videofile'], $slash + 1);
				}
				else
				{
					$file['name'] = $row['videofile'];
				}
				$files[] = $file;
				$content[] = 'v'.$row['id'];
			}
		}

		// Prepare filename and path
		$params	= JComponentHelper::getParams('com_sermonspeaker');
		$folder	= trim($params->get('path'), '/');

		$name = JFile::makeSafe($rows[0]['series_title']);
		$name = str_replace(' ', '_', $name); // Replace spaces in filename as long as makeSafe doesn't do this.

		// Check if filename has more chars than only underscores, making a new filename based on series id if not.
		if (!$name || (count_chars($name, 3) == '_'))
		{
			$name = 'series-'.$id;
		}
		$filename	= JPATH_BASE.'/'.$folder.'/series/'.$name.'.zip';

		// Compare to saved zip and if file exists, then skip the creating
		$content = implode(',', $content);
		if (JFile::exists($filename) && ($content == $zip_content))
		{
			$response = array(
				'status' => '1',
				'msg' => JURI::root().$folder.'/series/'.$name.'.zip'
			);
			echo json_encode($response);
			return;
		}

		if ($count = count($files))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::exists($folder.'/series'))
			{
				JFolder::create($folder.'/series');
			}
			$zip = new ZipArchive();
			if ($zip->open($filename, ZIPARCHIVE::OVERWRITE)!==TRUE)
			{
				$response = array(
					'status' => '0',
					'msg' => JText::_('I cannot open the file: ['.$filename.']')
				);
				echo json_encode($response);
				return;
			}
			ignore_user_abort(true);
			$i = 0;
			foreach ($files as $file)
			{
				if (JFile::exists($folder.'/series/stop.txt'))
				{
					$response = array(
						'status' => '0',
						'msg' => JText::_('I found the file [stop.txt] in the directory and thus terminated the script')
					);
					echo json_encode($response);
					return;
				}
				set_time_limit(0);
				$zip->addFile($file['path'], $file['name']);
				$i++;
				if ($i < $count)
				{
					$query = "UPDATE #__sermon_series SET `zip_progress` = ". (int)100/$count*$i ." WHERE `id` = ".$id;
				}
				else
				{
					$query	= $db->getQuery(true);
					$query->update('#__sermon_series');
					$query->set('`zip_progress` = 100');
					$query->set('`zip_created` = "'.JFactory::getDate()->toSql().'"');
					$query->set('`zip_content` = "'.$content.'"');
					$query->where('`id` = '.$id);
				}
				$db->setQuery($query);
				$db->query();
			}
			$zip->close();

			$response = array(
				'status' => '1',
				'msg' => JURI::root().$folder.'/series/'.$name.'.zip'
			);
		}
		else
		{
			$response = array(
				'status' => '0',
				'msg' => JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS'))
			);
		}
		echo json_encode($response);
		return;
	}

	function checkprogress()
	{
		$id = JRequest::getInt('id');
		if(!$id)
		{
			$response = array(
				'status' => '0',
				'msg' => JText::_('I have no clue what you want to download...')
			);
			echo json_encode($response);
			return;
		}

		$db = JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->select('zip_progress');
		$query->from('#__sermon_series');
		$query->where('`id` = '.$id);
		$db->setQuery($query);
		$progress = $db->loadResult();

		$response = array(
			'status' => '1',
			'msg' => $progress
		);
		echo json_encode($response);
		return;
	}
}