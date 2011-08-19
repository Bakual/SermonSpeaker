<?php
/**
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Tools Sermonspeaker Controller
 */
class SermonspeakerControllerTools extends JController
{
	public function order(){
		// Check for request forgeries
		JRequest::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
		$db		= JFactory::getDBO();
		$query	= "SET @c := 0";
		$db->setQuery($query);
		$db->query();
		$query	= "UPDATE #__sermon_sermons SET ordering = ( SELECT @c := @c + 1 ) ORDER BY sermon_date ASC, id ASC;";
		$db->setQuery($query);
		$db->query();
		$error = $db->getErrorMsg();
		if ($error){
			$this->setMessage('Error: '.$error, 'error');
		} else {
			$this->setMessage('Successfully reordered the sermons');
		}
		$this->setRedirect('index.php?option=com_sermonspeaker&view=tools');
	}

	public function write_id3(){
		// Check for request forgeries
		JRequest::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
		$app	= JFactory::getApplication();
		$db =& JFactory::getDBO();
		$query	= "SELECT audiofile, videofile, sermons.created_by, sermons.catid, sermon_title, name, series_title, YEAR(sermon_date) AS date, notes, sermon_number \n"
				. "FROM #__sermon_sermons AS sermons \n"
				. "LEFT JOIN #__sermon_speakers AS speakers ON speaker_id = speakers.id \n"
				. "LEFT JOIN #__sermon_series AS series ON series_id = series.id \n"
				;
		$db->setQuery($query);
		$items	= $db->loadObjectList();
		$user	= JFactory::getUser();
		foreach($items as $item){
			$canEdit	= $user->authorise('core.edit', 'com_sermonspeaker.category.'.$item->catid);
			$canEditOwn	= $user->authorise('core.edit.own', 'com_sermonspeaker.category.'.$item->catid) && $item->created_by == $user->id;
			if ($canEdit || $canEditOwn){
				$files[]	= $item->audiofile;
				$files[]	= $item->videofile;
				require_once(JPATH_COMPONENT_SITE.DS.'id3'.DS.'getid3'.DS.'getid3.php');
				$getID3		= new getID3;
				$getID3->setOption(array('encoding'=>'UTF-8'));
				require_once(JPATH_COMPONENT_SITE.DS.'id3'.DS.'getid3'.DS.'write.php');
				$writer		= new getid3_writetags;
				$writer->tagformats		= array('id3v2.3');
				$writer->overwrite_tags	= true;
				$writer->tag_encoding	= 'UTF-8';
				$TagData = array(
					'title'   => array($item->sermon_title),
					'artist'  => array($item->name),
					'album'   => array($item->series_title),
					'year'    => array($item->date),
					'comment' => array($item->notes),
					'track'   => array($item->sermon_number),
				);
				$writer->tag_data = $TagData;
				foreach ($files as $file){
					if (!$file){
						continue;
					}
					$path		= JPATH_SITE.str_replace('/', DS, $file);
					$path		= str_replace(DS.DS, DS, $path);
					if(!is_writable($path)){
						continue;
					}
					$writer->filename	= $path;
					if ($writer->WriteTags()) {
						$app->enqueueMessage('Successfully wrote tags to "'.$file.'"');
						if (!empty($writer->warnings)){
							$app->enqueueMessage('There were some warnings: '.implode(', ', $writer->errors), 'notice');
							$writer->warnings = array();
						}
					} else {
						$app->enqueueMessage('Failed to write tags to "'.$file.'"! '.implode(', ', $writer->errors), 'error');
						$writer->errors	= array();
					}
				}
			} else {
				$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR').' - '.$item->sermon_title, 'error');
				continue;
			}
		}
		$app->redirect('index.php?option=com_sermonspeaker&view=tools');
		return;
	}
}
