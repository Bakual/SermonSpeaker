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
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 *
	 * @return	JControllerForm
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		// Redirect View, defined as it would go to plural version otherwise
		$this->view_list = 'frontendupload';

		parent::__construct($config);
	}

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

	protected function postSaveHook(JModel &$model, $validData = array()){
		$recordId = $model->getState($this->context.'.id');
		$params	= JComponentHelper::getParams('com_sermonspeaker');
		if($params->get('write_id3', 0)){
			$app	= JFactory::getApplication();
			$app->enqueueMessage($this->setMessage(''));
			return $this->write_id3($recordId);
		}

		return;
	}

	public function write_id3($id){
		$app	= JFactory::getApplication();
		if (!$id){
			$app->redirect('index.php?option=com_sermonspeaker&view=frontendupload', JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			return;
		}
		$db =& JFactory::getDBO();
		$query	= "SELECT audiofile, videofile, sermons.created_by, sermons.catid, sermon_title, name, series_title, YEAR(sermon_date) AS date, notes, sermon_number \n"
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