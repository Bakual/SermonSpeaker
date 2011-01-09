<?php
defined('_JEXEC') or die('Restricted access');

/**
 * Sermonspeaker Component ID3 Helper
 */
class SermonspeakerHelperId3
{
	function getID3($file) {
		require_once(JPATH_COMPONENT_SITE.DS.'id3'.DS.'getid3'.DS.'getid3.php');
		$getID3 	= new getID3;
		$path		= JPATH_SITE.str_replace('/', DS, $file);
		$path		= str_replace(DS.DS, DS, $path);
		$FileInfo	= $getID3->Analyze($path);

		$id3 = array();
		if (array_key_exists('playtime_string', $FileInfo)){
			$id3['sermon_time']		= $FileInfo['playtime_string'];
		}
		if (array_key_exists('comments', $FileInfo)){
			if (array_key_exists('title', $FileInfo['comments'])){
				$id3['sermon_title']	= $FileInfo['comments']['title'][0];
			} else {
				jimport('joomla.filesystem.file');
				$id3['sermon_title']	= JFile::stripExt(JFile::getName($file));
			}
			$id3['alias'] = JFilterOutput::stringURLSafe($id3['sermon_title']);
			if (array_key_exists('track_number', $FileInfo['comments'])){
				$id3['sermon_number']	= $FileInfo['comments']['track_number'][0]; // ID3v2 Tag
			} elseif (array_key_exists('track', $FileInfo['comments'])) {
				$id3['sermon_number']	= $FileInfo['comments']['track'][0]; // ID3v1 Tag
			}

			if (array_key_exists('comments', $FileInfo['comments'])){
				if ($params->get('fu_id3_comments') == 'ref'){
					if ($FileInfo['comments']['comments'][0] != ""){
						$id3['sermon_scripture'] = $FileInfo['comments']['comments'][0]; // ID3v2 Tag
					} else {
						$id3['sermon_scripture'] = $FileInfo['comments']['comment'][0]; // ID3v1 Tag
					}
				} else {
					if ($FileInfo['comments']['comments'][0] != ""){
						$id3['notes'] = $FileInfo['comments']['comments'][0]; // ID3v2 Tag
					} else {
						$id3['notes'] = $FileInfo['comments']['comment'][0]; // ID3v1 Tag
					}
				}
			}
			$db =& JFactory::getDBO();
			if (array_key_exists('album', $FileInfo['comments'])){
				$query = "SELECT id FROM #__sermon_series WHERE series_title like '".$FileInfo['comments']['album'][0]."';";
				$db->setQuery($query);
				$id3['series_id'] 	= $db->loadResult();
			}
			if (array_key_exists('artist', $FileInfo['comments'])){
				$query = "SELECT id FROM #__sermon_speakers WHERE name like '".$FileInfo['comments']['artist'][0]."';";
				$db->setQuery($query);
				$id3['speaker_id']	= $db->loadResult();
			}
		}

		return $id3;
	}
}