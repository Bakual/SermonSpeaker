<?php
defined('_JEXEC') or die;

/**
 * Sermonspeaker Component ID3 Helper
 */
class SermonspeakerHelperId3
{
	static function getID3($file, $params)
	{
		if (strpos($file, 'http://vimeo.com') === 0 || strpos($file, 'http://player.vimeo.com') === 0)
		{
			return self::getVimeo($file);
		}
		require_once(JPATH_COMPONENT_SITE.'/id3/getid3/getid3.php');
		$getID3 	= new getID3;
		$path		= JPATH_SITE.$file;
		$path		= str_replace('//', '/', $path);
		if(!file_exists($path))
		{
			return false;
		}
		$FileInfo	= $getID3->analyze($path);
		getid3_lib::CopyTagsToComments($FileInfo);
		$id3 = array();
		if (array_key_exists('playtime_seconds', $FileInfo))
		{
			$length	= $FileInfo['playtime_seconds'];
			$hrs = (int)($length / 3600);
			$min = (int)(($length - $hrs * 3600) / 60);
			$sec = (int)($length - $hrs * 3600 - $min * 60);
			if ($sec == '60')
			{
				$sec = 0;
				$min++;
			}
			$id3['sermon_time']	= $hrs.':'.sprintf('%02d',$min).':'.sprintf('%02d', $sec);
		}
		else
		{
			$id3['sermon_time']	= '';
		}
		if (array_key_exists('comments', $FileInfo))
		{
			if (array_key_exists('title', $FileInfo['comments']))
			{
				$id3['title']	= $FileInfo['comments']['title'][0];
			}
			else
			{
				jimport('joomla.filesystem.file');
				$id3['title']	= JFile::stripExt(JFile::getName($file));
			}
			$id3['alias'] = JApplication::stringURLSafe($id3['title']);
			if (array_key_exists('track', $FileInfo['comments']))
			{
				$id3['sermon_number']	= $FileInfo['comments']['track'][0];
			}
			else
			{
				$id3['sermon_number']	= '';
			}
			if (array_key_exists('comment', $FileInfo['comments']))
			{
				$id3['notes']		= $FileInfo['comments']['comment'][0];
				$id3['scripture']	= '';
			}
			else
			{
				$id3['notes']		= '';
				$id3['scripture']	= '';
			}
			$db = JFactory::getDBO();
			if (array_key_exists('album', $FileInfo['comments']))
			{
				$query = "SELECT id FROM #__sermon_series WHERE series_title like '".$db->escape($FileInfo['comments']['album'][0])."';";
				$db->setQuery($query);
				$id3['series_id'] 	= $db->loadResult();
			}
			else
			{
				$id3['series_id'] 	= '';
			}
			if (array_key_exists('artist', $FileInfo['comments']))
			{
				$query = "SELECT id FROM #__sermon_speakers WHERE title like '".$db->escape($FileInfo['comments']['artist'][0])."';";
				$db->setQuery($query);
				$id3['speaker_id']	= $db->loadResult();
			}
			else
			{
				$id3['speaker_id']	= '';
			}
		}
		else
		{
			jimport('joomla.filesystem.file');
			$id3['title']	= JFile::stripExt(JFile::getName($path));
			$id3['alias'] 			= JApplication::stringURLSafe($id3['title']);
			$id3['sermon_number']	= '';
			$id3['notes'] 			= '';
			$id3['scripture'] = '';
			$id3['series_id'] 		= '';
			$id3['speaker_id']		= '';
		}
		$id3['filesize']			= filesize($path);

		return $id3;
	}

	static private function getVimeo($file)
	{
		$id		= trim(strrchr($file, '/'), '/ ');
		$url	= 'http://vimeo.com/api/v2/video/'.$id.'.xml';
		$xml	= simplexml_load_file($url);
		$video	= $xml->video;
		if (is_object($video))
		{
			$duration	= (string)$video->duration;
			$hrs		= (int)($duration / 3600);
			$min		= (int)(($duration - $hrs * 3600) / 60);
			$sec		= (int)($video->duration - $hrs * 3600 - $min * 60);
			$id3['sermon_time']		= $hrs.':'.sprintf('%02d',$min).':'.sprintf('%02d', $sec);
			$id3['title']			= (string)$video->title;
			$id3['alias'] 			= JApplication::stringURLSafe($id3['title']);
			$id3['sermon_date']		= (string)$video->upload_date;
			$id3['notes'] 			= (string)$video->description;
			$id3['pic']				= $video->thumbnail_medium;
			$id3['sermon_number']	= '';
			$id3['scripture'] = '';
			$id3['series_id'] 		= '';
			$id3['speaker_id']		= '';

			return $id3;
		}
	}
}