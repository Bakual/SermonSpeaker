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
		$query	= "SELECT audiofile, videofile, sermons.created_by, sermons.catid, sermon_title, name, series_title, YEAR(sermon_date) AS date, notes, sermon_number, picture \n"
				. "FROM #__sermon_sermons AS sermons \n"
				. "LEFT JOIN #__sermon_speakers AS speakers ON speaker_id = speakers.id \n"
				. "LEFT JOIN #__sermon_series AS series ON series_id = series.id \n"
				;
		$db->setQuery($query);
		$items	= $db->loadObjectList();
		$user	= JFactory::getUser();
		require_once(JPATH_COMPONENT_SITE.DS.'id3'.DS.'getid3'.DS.'getid3.php');
		$getID3		= new getID3;
		$getID3->setOption(array('encoding'=>'UTF-8'));
		require_once(JPATH_COMPONENT_SITE.DS.'id3'.DS.'getid3'.DS.'write.php');
		$writer		= new getid3_writetags;
		$writer->tagformats		= array('id3v2.3');
		$writer->overwrite_tags	= true;
		$writer->tag_encoding	= 'UTF-8';
		foreach($items as $item){
			$canEdit	= $user->authorise('core.edit', 'com_sermonspeaker.category.'.$item->catid);
			$canEditOwn	= $user->authorise('core.edit.own', 'com_sermonspeaker.category.'.$item->catid) && $item->created_by == $user->id;
			if ($canEdit || $canEditOwn){
				$files		= array();
				$files[]	= $item->audiofile;
				$files[]	= $item->videofile;
				$TagData = array(
					'title'   => array($item->sermon_title),
					'artist'  => array($item->name),
					'album'   => array($item->series_title),
					'year'    => array($item->date),
					'track'   => array($item->sermon_number),
				);
				$params	= JComponentHelper::getParams('com_sermonspeaker');
				$comments = ($params->get('fu_id3_comments', 'notes')) ? $item->notes : $item->sermon_scripture;
				$TagData['comment'] = array(strip_tags(JHTML::_('content.prepare', $comments)));

				JImport('joomla.filesystem.file');
				// Adding the picture to the id3 tags, taken from getID3 Demos -> demo.write.php
				if ($item->picture && (substr($item->picture, 0, 7) != 'http://')) {
					ob_start();
					$pic = $item->picture;
					if (substr($pic, 0, 1) == '/') {
						$pic = substr($pic, 1);
					}
					$pic = JPATH_ROOT.DS.$pic;
					if ($fd = fopen($pic, 'rb')) {
						ob_end_clean();
						$APICdata = fread($fd, filesize($pic));
						fclose ($fd);
						$image = getimagesize($pic);
						if (($image[2] > 0) && ($image[2] < 4)) { // 1 = gif, 2 = jpg, 3 = png
							$TagData['attached_picture'][0]['data']          = $APICdata;
							$TagData['attached_picture'][0]['picturetypeid'] = 0;
							$TagData['attached_picture'][0]['description']   = JFile::getName($pic);
							$TagData['attached_picture'][0]['mime']          = $image['mime'];
						}
					} else {
						$errormessage = ob_get_contents();
						ob_end_clean();
						JError::raiseNotice(100, 'Couldn\'t open the picture: '.$pic);
					}
				}
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
