<?php
defined('_JEXEC') or die('Restricted access');

/**
 * Sermonspeaker Component ID3 Helper
 */
class SermonspeakerHelperId3
{
	function getID3($file, $params) {
		require_once(JPATH_COMPONENT_SITE.DS.'id3'.DS.'getid3'.DS.'getid3.php');
		$getID3 	= new getID3;
		$path		= JPATH_SITE.str_replace('/', DS, $file);
		$path		= str_replace(DS.DS, DS, $path);
		$FileInfo	= $getID3->analyze($path);
		getid3_lib::CopyTagsToComments($FileInfo);

		$id3 = array();
		if (array_key_exists('playtime_string', $FileInfo)){
			$id3['sermon_time']	= $FileInfo['playtime_string'];
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
			$id3['alias'] = JFilterOutput::stringURLSafe($id3['sermon_title']);
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
			$id3['alias'] 			= JFilterOutput::stringURLSafe($id3['sermon_title']);
			$id3['sermon_number']	= '';
			$id3['notes'] 			= '';
			$id3['sermon_scripture'] = '';
			$id3['series_id'] 		= '';
			$id3['speaker_id']		= '';
		}

		return $id3;
	}
}