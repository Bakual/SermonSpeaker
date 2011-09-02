<?php
defined('_JEXEC') or die('Restricted access');

/**
 * Sermonspeaker Component ID3 Helper
 */
class SermonspeakerHelperId3
{
	function getID3($file, $params) {
		if (($params->get('path_mode_video', 0) == 1) && (strpos($file, 'http://vimeo.com') === 0 || (strpos($file, 'http://player.vimeo.com') === 0))){
			return SermonspeakerHelperId3::getVimeo($file);
		}
		require_once(JPATH_COMPONENT_SITE.DS.'id3'.DS.'getid3'.DS.'getid3.php');
		$getID3 	= new getID3;
		$path		= JPATH_SITE.str_replace('/', DS, $file);
		$path		= str_replace(DS.DS, DS, $path);
		if(!file_exists($path)){
			return false;
		}
		$FileInfo	= $getID3->analyze($path);
		getid3_lib::CopyTagsToComments($FileInfo);
		$id3 = array();
		if (array_key_exists('playtime_seconds', $FileInfo)){
			$lenght	= $FileInfo['playtime_seconds'];
			$hrs = floor($lenght / 3600);
			$min = floor(($lenght - $hrs * 3600) / 60);
			$sec = round($lenght - $hrs * 3600 - $min * 60);
			$id3['sermon_time']	= $hrs.':'.sprintf('%02d',$min).':'.sprintf('%02d', $sec);
		} else {
			$id3['sermon_time']	= '';
		}
		if (array_key_exists('comments', $FileInfo)){
			if (array_key_exists('title', $FileInfo['comments'])){
				$id3['sermon_title']	= $FileInfo['comments']['title'][0];
			} else {
				jimport('joomla.filesystem.file');
				$id3['sermon_title']	= JFile::stripExt(JFile::getName($file));
			}
			$id3['alias'] = JApplication::stringURLSafe($id3['sermon_title']);
			if (array_key_exists('track', $FileInfo['comments'])){
				$id3['sermon_number']	= $FileInfo['comments']['track'][0];
			} else {
				$id3['sermon_number']	= '';
			}
			if (array_key_exists('comment', $FileInfo['comments'])){
				if ($params->get('fu_id3_comments') == 'ref'){
					$id3['sermon_scripture'] = $FileInfo['comments']['comment'][0];
					$id3['notes'] 			 = '';
				} else {
					$id3['notes']			 = $FileInfo['comments']['comment'][0];
					$id3['sermon_scripture'] = '';
				}
			} else {
				$id3['notes']			 = '';
				$id3['sermon_scripture'] = '';
			}
			$db =& JFactory::getDBO();
			if (array_key_exists('album', $FileInfo['comments'])){
				$query = "SELECT id FROM #__sermon_series WHERE series_title like '".$FileInfo['comments']['album'][0]."';";
				$db->setQuery($query);
				$id3['series_id'] 	= $db->loadResult();
			} else {
				$id3['series_id'] 	= '';
			}
			if (array_key_exists('artist', $FileInfo['comments'])){
				$query = "SELECT id FROM #__sermon_speakers WHERE name like '".$FileInfo['comments']['artist'][0]."';";
				$db->setQuery($query);
				$id3['speaker_id']	= $db->loadResult();
			} else {
				$id3['speaker_id']	= '';
			}
		} else {
			jimport('joomla.filesystem.file');
			$id3['sermon_time']		= '';
			$id3['sermon_title']	= JFile::stripExt(JFile::getName($path));
			$id3['alias'] 			= JApplication::stringURLSafe($id3['sermon_title']);
			$id3['sermon_number']	= '';
			$id3['notes'] 			= '';
			$id3['sermon_scripture'] = '';
			$id3['series_id'] 		= '';
			$id3['speaker_id']		= '';
		}

		return $id3;
	}
	private function getVimeo($file){
		$id		= trim(strrchr($file, '/'), '/ ');
		$url	= 'http://vimeo.com/api/v2/video/'.$id.'.xml';
		$xml	= simplexml_load_file($url);
		$video	= $xml->video;
		if (is_object($video)){
			$minutes	= floor((string)$video->duration / 60);
			$hours		= floor($minutes / 60);
			$seconds	= $video->duration - $minutes * 60;
			$id3['sermon_time']		= $hours.':'.$minutes.':'.$seconds;
			$id3['sermon_title']	= $video->title;
			$id3['alias'] 			= JApplication::stringURLSafe($id3['sermon_title']);
			$id3['sermon_date']		= $video->upload_date;
			$id3['notes'] 			= $video->description;
			$id3['pic']				= $video->thumbnail_medium;
			$id3['sermon_number']	= '';
			$id3['sermon_scripture'] = '';
			$id3['series_id'] 		= '';
			$id3['speaker_id']		= '';

			return $id3;
		}
	}
}