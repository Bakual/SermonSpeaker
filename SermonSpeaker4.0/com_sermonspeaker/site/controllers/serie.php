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
	function download(){
		$id = JRequest::getInt('id');
		if (!$id){
			die("<html><body OnLoad=\"javascript: alert('I have no clue what you want to download...');history.back();\" bgcolor=\"#F0F0F0\"></body></html>");
		}
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->authorisedLevels());
		$db =& JFactory::getDBO();
		$query = "SELECT audiofile, videofile, series_title \n"
				."FROM #__sermon_sermons as sermons \n"
				."LEFT JOIN #__sermon_speakers AS speakers ON speakers.id = sermons.speaker_id \n"
				."JOIN #__sermon_series AS series ON series.id = sermons.series_id \n"
				."LEFT JOIN #__categories AS c_sermons ON c_sermons.id = sermons.catid \n"
				."LEFT JOIN #__categories AS c_speaker ON c_speaker.id = speakers.catid \n"
				."LEFT JOIN #__categories AS c_series ON c_series.id = series.catid \n"
				."WHERE sermons.series_id = ".$id." \n"
				."AND (series.catid = 0 OR (c_series.access IN (".$groups.") AND c_series.published = 1)) \n"
				."AND (sermons.catid = 0 OR (c_sermons.access IN (".$groups.") AND c_sermons.published = 1)) \n"
				."AND (sermons.speaker_id = 0 OR speakers.catid = 0 OR (c_speaker.access IN (".$groups.") AND c_speaker.published = 1)) \n"
				;
		$db->setQuery($query);
		$rows = $db->loadAssocList();
		jimport('joomla.filesystem.file');
		$files = array();
		foreach ($rows as $row){
			if ($row['audiofile'] && (substr($row['audiofile'], 0, 7) != 'http://') && JFile::exists(JPATH_BASE.'/'.$row['audiofile'])){
				$file['path'] = JPATH_BASE.'/'.$row['audiofile'];
				$slash = strrpos($row['audiofile'], '/');
				if ($slash !== false) {
					$file['name'] = substr($row['audiofile'], $slash + 1);
				} else {
					$file['name'] = $row['audiofile'];
				}
				$files[] = $file;
			}
			if ($row['videofile'] && (substr($row['videofile'], 0, 7) != 'http://') && JFile::exists(JPATH_BASE.'/'.$row['videofile'])){
				$file['path'] = JPATH_BASE.'/'.$row['videofile'];
				$slash = strrpos($row['videofile'], '/');
				if ($slash !== false) {
					$file['name'] = substr($row['videofile'], $slash + 1);
				} else {
					$file['name'] = $row['videofile'];
				}
				$files[] = $file;
			}
		}
		if (count($files)){
			jimport('joomla.filesystem.folder');
			$name = JFile::makeSafe($rows[0]['series_title']);
			$name = str_replace(' ', '_', $name); // Replace spaces in filename as long as makeSafe doesn't do this.

			// Check if filename has more chars than only underscores, making a new filename based on series id if not.
			if (!$name || (count_chars($name, 3) == '_')) {
				$name = 'series-'.$id;
			}

			$params		= JComponentHelper::getParams('com_sermonspeaker');
			$folder		= $params->get('path');
			$folder		= trim($folder, '/');
			if (!JFolder::exists($folder.'/series')){
				JFolder::create($folder.'/series');
			}
			$filename	= JPATH_BASE.str_replace('//', '/', '/'.$folder.'/series').'/'.$name.'.zip';
			$zip = new ZipArchive();
			if ($zip->open($filename, ZIPARCHIVE::OVERWRITE)!==TRUE) {
				die("cannot open <$filename>\n");
			}
			ignore_user_abort(true);
			foreach ($files as $file){
				if (JFile::exists($folder.'/series/stop.txt')){die('found stop.txt in directory and thus terminating script');}
				set_time_limit(0);
				$zip->addFile($file['path'], $file['name']);
			}
			$zip->close();
			$app = JFactory::getApplication();
			$app->redirect(JURI::root().str_replace('//', '/', $folder.'/series/'.$name.'.zip'));
		} else {
			JError::raiseNotice(100, JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS')));
		}
	}
}