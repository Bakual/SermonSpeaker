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
		$this->setRedirect('index.php?option=com_sermonspeaker&view=sermons');
	}

	public function seriesorder(){
		// Check for request forgeries
		JRequest::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
		$db		= JFactory::getDBO();
		$query	= "SET @c := 0";
		$db->setQuery($query);
		$db->query();
		$query	= "UPDATE #__sermon_series SET ordering = ( SELECT @c := @c + 1 ) ORDER BY series_title ASC, id ASC;";
		$db->setQuery($query);
		$db->query();
		$error = $db->getErrorMsg();
		if ($error){
			$this->setMessage('Error: '.$error, 'error');
		} else {
			$this->setMessage('Successfully reordered the series');
		}
		$this->setRedirect('index.php?option=com_sermonspeaker&view=series');
	}

	public function speakersorder(){
		// Check for request forgeries
		JRequest::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
		$db		= JFactory::getDBO();
		$query	= "SET @c := 0";
		$db->setQuery($query);
		$db->query();
		$query	= "UPDATE #__sermon_speakers SET ordering = ( SELECT @c := @c + 1 ) ORDER BY name ASC, id ASC;";
		$db->setQuery($query);
		$db->query();
		$error = $db->getErrorMsg();
		if ($error){
			$this->setMessage('Error: '.$error, 'error');
		} else {
			$this->setMessage('Successfully reordered the speakers');
		}
		$this->setRedirect('index.php?option=com_sermonspeaker&view=speakers');
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
				$TagData['comment'] = array(strip_tags(JHTML::_('content.prepare', $item->notes)));

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

	public function delete(){
		// Check for request forgeries
		JRequest::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
		$app	= JFactory::getApplication();
		$file	= JRequest::getVar('file');
		$file	= JPATH_SITE.$file;
		jimport('joomla.filesystem.file');
		if(JFile::exists($file)){
			JFile::delete($file);
			$app->enqueueMessage($file.' deleted!');
		} else {
			$app->enqueueMessage($file.' not found!', 'error');
		}
		$app->redirect('index.php?option=com_sermonspeaker&view=tools');
		return;
	}

	public function piimport(){
		// Check for request forgeries
		JRequest::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
		$app	= JFactory::getApplication();
		$db		=& JFactory::getDBO();
		$params	= &JComponentHelper::getParams('com_sermonspeaker');
		$tag 	= $params->get('plugin_tag');

		// Get Data from PreachIt
		// Get Studies
		$query	= $db->getQuery(true);
		$query->from('`#__pistudies` AS a');
		$query->select('a.study_date, a.study_name, a.study_alias, a.study_description');
		$query->select('a.study_book, a.ref_ch_beg, a.ref_ch_end, a.ref_vs_beg, a.ref_vs_end');
		$query->select('a.study_book2, a.ref_ch_beg2, a.ref_ch_end2, a.ref_vs_beg2, a.ref_vs_end2');
		$query->select('CONCAT_WS(":", a.dur_hrs, a.dur_mins, a.dur_secs) AS duration');
		$query->select('a.published, a.hits, a.user');
		// Join over the series.
		$query->select('b.series_name');
		$query->join('LEFT', '#__piseries AS b ON b.id = a.series');
		// Join over the teachers.
		$query->select('c.teacher_name');
		$query->join('LEFT', '#__piteachers AS c ON c.id = a.teacher');
		// Join over the audio path.
		$query->select("IF (d.server != '', CONCAT('http://', CONCAT_WS('/', d.server, d.folder, a.audio_link)), "
					."IF (LEFT(d.folder, 7) = 'http://', CONCAT(d.folder, '/', a.audio_link), CONCAT('/', d.folder, '/', a.audio_link))) AS audiofile");
		$query->join('LEFT', '#__pifilepath AS d ON d.id = a.audio_folder');
		// Join over the video path.
		$query->select("IF (e.server != '', CONCAT('http://', CONCAT_WS('/', e.server, e.folder, a.video_link)), "
					."IF (LEFT(e.folder, 7) = 'http://', CONCAT(e.folder, '/', a.video_link), CONCAT('/', e.folder, '/', a.video_link))) AS videofile");
		$query->join('LEFT', '#__pifilepath AS e ON e.id = a.video_folder');
		// Join over the study pic path.
		$query->select("IF (f.server != '', CONCAT('http://', CONCAT_WS('/', f.server, f.folder, a.imagelrg)), "
					."IF (LEFT(f.folder, 7) = 'http://', CONCAT(f.folder, '/', a.imagelrg), CONCAT('/', f.folder, '/', a.imagelrg))) AS study_pic");
		$query->join('LEFT', '#__pifilepath AS f ON f.id = a.image_folderlrg');
		// Join over the study notes path.
		$query->select("IF (g.server != '', CONCAT('http://', CONCAT_WS('/', g.server, g.folder, a.notes_link)), "
					."IF (LEFT(g.folder, 7) = 'http://', CONCAT(g.folder, '/', a.notes_link), CONCAT('/', g.folder, '/', a.notes_link))) AS addfile");
		$query->join('LEFT', '#__pifilepath AS g ON g.id = a.notes_folder');
		$db->setQuery($query);
		$studies	= $db->loadObjectList();
		if ($db->getErrorMsg()){
			$app->enqueueMessage($db->getErrorMsg(), 'error');
		}

		// Store the Series
		$query	= "INSERT INTO #__sermon_series \n"
				."(series_title, alias, series_description, state, ordering, created_by, created, avatar) \n"
				."SELECT a.series_name, a.series_alias, a.series_description, a.published, a.ordering, a.user, NOW(), \n"
				."IF (b.server != '', CONCAT('http://', CONCAT_WS('/', b.server, b.folder, a.series_image_lrg)), "
				."IF (LEFT(b.folder, 7) = 'http://', CONCAT(b.folder, '/', a.series_image_lrg), CONCAT('/', b.folder, '/', a.series_image_lrg))) \n"
				."FROM #__piseries AS a \n"
				."LEFT JOIN #__pifilepath AS b ON b.id = a.image_folderlrg \n";
		$db->setQuery($query);
		$db->query();
		if ($db->getErrorMsg()){
			$app->enqueueMessage($db->getErrorMsg(), 'error');
		} else {
			$app->enqueueMessage($db->getAffectedRows().' series migrated!');
		}

		// Store the Speakers
		$query	= "INSERT INTO #__sermon_speakers \n"
				."(name, alias, website, intro, state, ordering, created_by, created, pic) \n"
				."SELECT a.teacher_name, a.teacher_alias, a.teacher_website, a.teacher_description, a.published, a.ordering, a.user, NOW(), \n"
				."IF (b.server != '', CONCAT('http://', CONCAT_WS('/', b.server, b.folder, a.teacher_image_lrg)), "
				."IF (LEFT(b.folder, 7) = 'http://', CONCAT(b.folder, '/', a.teacher_image_lrg), CONCAT('/', b.folder, '/', a.teacher_image_lrg))) \n"
				."FROM #__piteachers AS a \n"
				."LEFT JOIN #__pifilepath AS b ON b.id = a.image_folderlrg \n";
		$db->setQuery($query);
		$db->query();
		if ($db->getErrorMsg()){
			$app->enqueueMessage($db->getErrorMsg(), 'error');
		} else {
			$app->enqueueMessage($db->getAffectedRows().' speakers migrated!');
		}

		// Prepare and Store the Sermons for SermonSpeaker
		$count = 0;
		foreach ($studies as $study){
			// Prepare Scripture
			$scripture	= array();
			if ($study->study_book){
				$bible['book']		= (int)$study->study_book;
				$bible['cap1']		= (int)$study->ref_ch_beg;
				$bible['vers1']		= (int)$study->ref_vs_beg;
				$bible['cap2']		= (int)$study->ref_ch_end;
				$bible['vers2']		= (int)$study->ref_vs_end;
				$bible['ordering']	= 1;
				$scripture[]	= $bible;
			}
			if ($study->study_book2){
				$bible['book']		= (int)$study->study_book2;
				$bible['cap1']		= (int)$study->ref_ch_beg2;
				$bible['vers1']		= (int)$study->ref_vs_beg2;
				$bible['cap2']		= (int)$study->ref_ch_end2;
				$bible['vers2']		= (int)$study->ref_vs_end2;
				$bible['ordering']	= 2;
				$scripture[]	= $bible;
			}

			$query	= "INSERT INTO #__sermon_sermons \n"
					."(`audiofile`, `videofile`, `picture`, `sermon_title`, `alias`, `sermon_date`, `sermon_time`, `notes`, `state`, `hits`, `created_by`, `addfile`, `podcast`, `created`) \n"
					.'VALUES ('.$db->quote($study->audiofile).','.$db->quote($study->videofile).','.$db->quote($study->study_pic).','.$db->quote($study->study_name).','.$db->quote($study->study_alias).','.$db->quote($study->study_date).','.$db->quote($study->duration).','.$db->quote($study->study_description).','.$db->quote($study->published).','.$db->quote($study->hits).','.$db->quote($study->user).','.$db->quote($study->addfile).', 1, NOW())';
			$db->setQuery($query);
			$db->query();
			if ($db->getErrorMsg()){
				$app->enqueueMessage($db->getErrorMsg(), 'error');
				break;
			}
			$id	= $db->insertid();

			foreach($scripture as $passage){
				// Insert Scriptures
				$query	= "INSERT INTO #__sermon_scriptures \n"
						."(`book`, `cap1`, `vers1`, `cap2`, `vers2`, `text`, `ordering`, `sermon_id`) \n"
						."VALUES ('".$passage['book']."','".$passage['cap1']."','".$passage['vers1']."','".$passage['cap2']."','".$passage['vers2']."','','".$passage['ordering']."','".$id."')";
				$db->setQuery($query);
				$db->query();
				if ($db->getErrorMsg()){
					$app->enqueueMessage($db->getErrorMsg(), 'error');
					break;
				}
			}

			// Update Speaker
			if ($study->teacher_name){
				$query	= "UPDATE #__sermon_sermons \n"
						."SET `speaker_id` = (SELECT `id` FROM #__sermon_speakers WHERE `name` = ".$db->quote($study->teacher_name)." LIMIT 1) \n"
						."WHERE `id` = ".$db->quote($id);
				$db->setQuery($query);
				$db->query();
				if ($db->getErrorMsg()){
					$app->enqueueMessage($db->getErrorMsg(), 'error');
				}
			}

			// Update Series
			if ($study->series_name){
				$query	= "UPDATE #__sermon_sermons \n"
						."SET `series_id` = (SELECT `id` FROM #__sermon_series WHERE `series_title` = ".$db->quote($study->series_name)." LIMIT 1) \n"
						."WHERE `id` = ".$db->quote($id);
				$db->setQuery($query);
				$db->query();
				if ($db->getErrorMsg()){
					$app->enqueueMessage($db->getErrorMsg(), 'error');
				}
			}

			$count++;
		}
		$app->enqueueMessage($count.' sermons migrated!');

		$app->redirect('index.php?option=com_sermonspeaker&view=tools');
		return;
	}

	// Function to adjust the sermon time
	public function time(){
		// Check for request forgeries
		JRequest::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
		$app	= JFactory::getApplication();
		$db		=& JFactory::getDBO();
		$mode	= JRequest::getVar('submit');

		if(isset($mode['diff'])){
			$diff	= JRequest::getFloat('diff');
			$mins	= abs(($diff - intval($diff))*60);
			$hrs	= abs(intval($diff));
			$minus	= ($diff < 0) ? '-' : '';
			$query	= "UPDATE #__sermon_sermons \n"
					."SET sermon_date = DATE_ADD(sermon_date, INTERVAL '".$minus.$hrs.":".$mins."' HOUR_MINUTE) \n"
					."WHERE sermon_date != '0000-00-00 00:00:00' \n"
					."AND state = 1";
			$db->setQuery($query);
			$db->query();
			if ($db->getErrorMsg()){
				$app->enqueueMessage($db->getErrorMsg(), 'error');
			} else {
				if ($minus){
					$app->enqueueMessage('Successfully substracted '.$hrs.' hours and '.$mins.' minutes from the sermon date!');
				} else {
					$app->enqueueMessage('Successfully added '.$hrs.' hours and '.$mins.' minutes to the sermon date!');
				}
			}
		} elseif (isset($mode['time'])){
			$time	= JRequest::getString('time');
			$config = JFactory::getConfig();
			$user	= JFactory::getUser();
			$date	= JFactory::getDate($time, $user->getParam('timezone', $config->get('offset')));
			$date->setTimeZone(new DateTimeZone('UTC'));
			$t_utc	= $date->format('H:i:s', true);
			$query	= "UPDATE #__sermon_sermons \n"
					."SET sermon_date = CONCAT(DATE(sermon_date), ' ".$t_utc."') \n"
					."WHERE sermon_date != '0000-00-00 00:00:00' \n"
					."AND state = 1";
			$db->setQuery($query);
			$db->query();
			if ($db->getErrorMsg()){
				$app->enqueueMessage($db->getErrorMsg(), 'error');
			} else {
				$app->enqueueMessage('Successfully set time to '.$time.' for each sermon date!');
			}
		}

		$app->redirect('index.php?option=com_sermonspeaker&view=tools');
		return;
	}
}
