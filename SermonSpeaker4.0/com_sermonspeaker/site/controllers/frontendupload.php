<?php
/**
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.controllerform');

/**
 * Frontendupload Sermonspeaker Controller
 *
 */
class SermonspeakerControllerFrontendupload extends JControllerForm
{
	/**
	 * @since	1.6
	 */
	protected $view_item = 'frontendupload';

	/**
	 * @since	1.6
	 */
	protected $view_list = 'sermons';

	/**
	 * Method to add a new record.
	 *
	 * @return	boolean	True if the article can be added, false if not.
	 * @since	1.6
	 */
	public function add()
	{
		if (!parent::add()) {
			// Redirect to the return page.
			$this->setRedirect($this->getReturnPage());
		}
	}

	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param	array	$data	An array of input data.
	 * @return	boolean
	 * @since	1.6
	 */
	protected function allowAdd($data = array())
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$categoryId	= JArrayHelper::getValue($data, 'catid', JFactory::getApplication()->input->get('id', 0, 'int'), 'int');
		$allow		= null;

		if ($categoryId) {
			// If the category has been passed in the URL check it.
			$allow	= $user->authorise('core.create', $this->option.'.category.'.$categoryId);
		}

		if ($allow === null) {
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd($data);
		} else {
			return $allow;
		}
	}

	/**
	 * Method to check if you can edit a record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
		$categoryId = 0;

		if ($recordId) {
			$categoryId = (int) $this->getModel()->getItem($recordId)->catid;
		}

		if ($categoryId) {
			// The category has been set. Check the category permissions.
			return JFactory::getUser()->authorise('core.edit', $this->option.'.category.'.$categoryId);
		} else {
			// Since there is no asset tracking, revert to the component permissions.
			return parent::allowEdit($data, $key);
		}
	}

	/**
	 * Method to cancel an edit.
	 *
	 * @param	string	$key	The name of the primary key of the URL variable.
	 *
	 * @return	Boolean	True if access level checks pass, false otherwise.
	 * @since	1.6
	 */
	public function cancel($key = 's_id')
	{
		parent::cancel($key);

		// Redirect to the return page.
		$this->setRedirect($this->getReturnPage());
	}

	/**
	 * Method to edit an existing record.
	 *
	 * @param	string	$key	The name of the primary key of the URL variable.
	 * @param	string	$urlVar	The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return	Boolean	True if access level check and checkout passes, false otherwise.
	 * @since	1.6
	 */
	public function edit($key = null, $urlVar = 's_id')
	{
		$result = parent::edit($key, $urlVar);

		return $result;
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param	string	$name	The model name. Optional.
	 * @param	string	$prefix	The class prefix. Optional.
	 * @param	array	$config	Configuration array for model. Optional.
	 *
	 * @return	object	The model.
	 * @since	1.5
	 */
	public function getModel($name = 'frontendupload', $prefix = '', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param	int		$recordId	The primary key id for the item.
	 * @param	string	$urlVar		The name of the URL variable for the id.
	 *
	 * @return	string	The arguments to append to the redirect URL.
	 * @since	1.6
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 's_id')
	{
		$jinput	= JFactory::getApplication()->input;
		$jinput->set('layout', 'default');
		$append = parent::getRedirectToItemAppend($recordId, 's_id');
		$itemId	= $jinput->get('Itemid', 0, 'int');
		$return	= $this->getReturnPage();

		if ($itemId) {
			$append .= '&Itemid='.$itemId;
		}

		if ($return) {
			$append .= '&return='.base64_encode($return);
		}

		return $append;
	}

	/**
	 * Get the return URL.
	 *
	 * If a "return" variable has been passed in the request
	 *
	 * @return	string	The return URL.
	 * @since	1.6
	 */
	protected function getReturnPage()
	{
		$return = JFactory::getApplication()->input->get('return', '', 'base64');

		if (empty($return) || !JUri::isInternal(base64_decode($return))) {
			return JURI::base();
		}
		else {
			return base64_decode($return);
		}
	}

	/**
	 * Function that allows child controller access to model data after the data has been saved.
	 *
	 * @param	JModel	$model		The data model object.
	 * @param	array	$validData	The validated data.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function postSaveHook($model, $validData = array())
	{
		$task = $this->getTask();

		if ($task == 'save') {
			$this->setRedirect(JRoute::_('index.php?option=com_sermonspeaker&view=sermons', false));
		}

		$recordId = $model->getState($this->context.'.id');
		$params	= JComponentHelper::getParams('com_sermonspeaker');

		$app	= JFactory::getApplication();
		$db		= JFactory::getDBO();

		// Scriptures
		$query	= "DELETE FROM #__sermon_scriptures \n"
				."WHERE sermon_id = ".$recordId
				;
		$db->setQuery($query);
		$db->query();
		$i = 1;
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

		// Tags
		$query	= "DELETE FROM #__sermon_sermons_tags \n"
				."WHERE sermon_id = ".$recordId
				;
		$db->setQuery($query);
		$db->query();
		foreach ($validData['tags'] as $tag){
			$query	= "INSERT INTO #__sermon_sermons_tags \n"
					."(`sermon_id`,`tag_id`) \n"
					."VALUES ('".$recordId."','".(int)$tag."')"
					;
			$db->setQuery($query);
			$db->query();
		}

		// ID3
		if($params->get('write_id3', 0)){
			$app	= JFactory::getApplication();
			$app->enqueueMessage($this->setMessage(''));
			return $this->write_id3($recordId);
		}
	}

	/**
	 * Method to save a record.
	 *
	 * @param	string	$key	The name of the primary key of the URL variable.
	 * @param	string	$urlVar	The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return	Boolean	True if successful, false otherwise.
	 * @since	1.6
	 */
	public function save($key = null, $urlVar = 's_id')
	{
		$result = parent::save($key, $urlVar);

		return $result;
	}

	/**
	 * Method to write the ID3 tags to the file.
	 *
	 * @param	int		$id		The id of the record.
	 *
	 * @return	Boolean	True if successful, false otherwise.
	 * @since	1.6
	 */
	public function write_id3($id){
		$app	= JFactory::getApplication();
		if (!$id){
			$app->redirect('index.php?option=com_sermonspeaker&view=frontendupload', JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
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
			require_once(JPATH_COMPONENT_SITE.'/id3/getid3/getid3.php');
			$getID3		= new getID3;
			$getID3->setOption(array('encoding'=>'UTF-8'));
			require_once(JPATH_COMPONENT_SITE.'/id3/getid3/write.php');
			$writer		= new getid3_writetags;
			$writer->speakerformats		= array('id3v2.3');
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
				$pic = JPATH_ROOT.'/'.$pic;
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
				$path		= JPATH_SITE.$file;
				$path		= str_replace('//', '/', $path);
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