<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Serie controller class.
 *
 * @package		SermonSpeaker.Administrator
 */
class SermonspeakerControllerSermon extends JControllerForm
{
	/**
	 * Method override to check if you can add a new record.
	 * Quite useless now, but may change if we add ACLs to SermonSpeaker
	 *
	 * @param	array	$data	An array of input data.
	 * @return	boolean
	 */
	protected function allowAdd($data = array())
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$categoryId	= JArrayHelper::getValue($data, 'catid', JRequest::getInt('filter_category_id'), 'int');
		$allow		= null;

		if ($categoryId) {
			// If the category has been passed in the data or URL check it.
			$allow	= $user->authorise('core.create', 'com_sermonspeaker.category.'.$categoryId);
		}

		if ($allow === null) {
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd();
		} else {
			return $allow;
		}
	}

	/**
	 * Method to check if you can add a new record.
	 * Quite useless now, but may change if we add ACLs to SermonSpeaker
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
		$user		= JFactory::getUser();
		$userId		= $user->get('id');

		// Check general edit permission first.
		if ($user->authorise('core.edit', 'com_content.article.'.$recordId)) {
			return true;
		}

		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', 'com_content.article.'.$recordId)) {
			// Now test the owner is the user.
			$ownerId	= (int) isset($data['created_by']) ? $data['created_by'] : 0;
			if (empty($ownerId) && $recordId) {
				// Need to do a lookup from the model.
				$record		= $this->getModel()->getItem($recordId);

				if (empty($record)) {
					return false;
				}

				$ownerId = $record->created_by;
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId) {
				return true;
			}
		}

		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}

	public function reset()
	{
		$app	= JFactory::getApplication();
		$db		= JFactory::getDBO();
		$id 	= JRequest::getInt('id', 0);
		if (!$id){
			$app->redirect('index.php?option=com_sermonspeaker&view=sermons', JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			return;
		}
		$model 	= $this->getModel();
		$item 	= $model->getItem($id);
		$user	= JFactory::getUser();
		$canEdit	= $user->authorise('core.edit', 'com_sermonspeaker.category.'.$item->catid);
		$canEditOwn	= $user->authorise('core.edit.own', 'com_sermonspeaker.category.'.$item->catid) && $item->created_by == $user->id;
		if ($canEdit || $canEditOwn){
			$query	= "UPDATE #__sermon_sermons \n"
					. "SET hits='0' \n"
					. "WHERE id='".$id."'"
					;
			$db->setQuery($query);
			if (!$db->query()) {
				echo "<script> alert('".$db->getErrorMsg()."'); window.history.go(-1); </script>\n";
				exit();
			}
			$app->redirect('index.php?option=com_sermonspeaker&view=sermons', JText::sprintf('COM_SERMONSPEAKER_RESET_OK', JText::_('COM_SERMONSPEAKER_SERMON'), $item->sermon_title));
			return;
		} else {
			$app->redirect('index.php?option=com_sermonspeaker&view=sermons', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			return;
		}
	}

	protected function postSaveHook(JModel &$model, $validData = array()){
		$recordId = $model->getState($this->context.'.id');
		$params	= JComponentHelper::getParams('com_sermonspeaker');

		$app	= JFactory::getApplication();
		$db		= JFactory::getDBO();
		$query	= "DELETE FROM #__sermon_scriptures \n"
				."WHERE sermon_id = ".$recordId
				;
		$db->setQuery($query);
		$db->query();
		$i	= 1;
		foreach ($validData['scripture'] as $scripture){
			$item	= explode('|', $scripture);
			$query	= "INSERT INTO #__sermon_scriptures \n"
					."(`book`,`cap1`,`vers1`,`cap2`,`vers2`,`text`,`ordering`,`sermon_id`) \n"
					."VALUES ('".(int)$item[0]."','".(int)$item[1]."','".(int)$item[2]."','".(int)$item[3]."','".(int)$item[4]."',".$db->quote($item[5]).",'".$i."','".$recordId."')"
					;
			$db->setQuery($query);
			$db->query();
			$i++;
		}
		if($params->get('write_id3', 0)){
			$app	= JFactory::getApplication();
			$app->enqueueMessage($this->setMessage(''));
			return $this->write_id3($recordId);
		}

		return;
	}

	public function id3(){
		$id = JRequest::getInt('id');
		$this->write_id3($id);
		$app	= JFactory::getApplication();
		$app->redirect('index.php?option=com_sermonspeaker&view=sermon&layout=edit&id='.$id);
		return;
	}

	public function write_id3($id){
		$app	= JFactory::getApplication();
		if (!$id){
			$app->redirect('index.php?option=com_sermonspeaker&view=sermons', JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			return;
		}
		$db =& JFactory::getDBO();
		$query	= "SELECT audiofile, videofile, sermons.created_by, sermons.catid, sermon_title, name, series_title, YEAR(sermon_date) AS date, notes, sermon_number, picture \n"
				. "FROM #__sermon_sermons AS sermons \n"
				. "LEFT JOIN #__sermon_speakers AS speakers ON speaker_id = speakers.id \n"
				. "LEFT JOIN #__sermon_series AS series ON series_id = series.id \n"
				. "WHERE sermons.id='".$id."'"
				;
		$db->setQuery($query);
		$item	= $db->loadObject();
		$user	= JFactory::getUser();
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
					$app->enqueueMessage('Successfully wrote id3 tags to "'.$file.'"');
					if (!empty($writer->warnings)) {
						JError::raiseNotice(100, 'There were some warnings:<br>'.implode(', ', $writer->warnings));
					}
				} else {
					JError::raiseWarning(100, 'Failed to write id3 tags to "'.$file.'"! '.implode(', ', $writer->errors));
				}
			}
			return true;
		} else {
			$app->redirect('index.php?option=com_sermonspeaker&view=sermons', JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			return;
		}
	}
}