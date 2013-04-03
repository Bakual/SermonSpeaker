<?php
// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/sermon.php';

/**
 * Frontendupload model.
 *
 * @package		Sermonspeaker.Administrator
 */
class SermonspeakerModelFrontendupload extends SermonspeakerModelSermon
{
	/**
	 * Get the return URL.
	 *
	 * @return	string	The return URL.
	 * @since	1.6
	 */
	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app	= JFactory::getApplication();
		$jinput	= $app->input;

		// Load state from the request.
		$pk = $jinput->get('s_id', 0, 'int');
		$this->setState('frontendupload.id', $pk);
		// Add compatibility variable for default naming conventions.
		$this->setState('form.id', $pk);

		$categoryId	= $jinput->get('catid', 0, 'int');
		$this->setState('frontendupload.catid', $categoryId);

		$return = $jinput->get('return', '', 'base64');

		if (!JUri::isInternal(base64_decode($return))) {
			$return = null;
		}

		$this->setState('return_page', base64_decode($return));

		// Load the parameters.
		$params	= $app->getParams();
		$this->setState('params', $params);

		$this->setState('layout', $jinput->get('layout'));
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_sermonspeaker.edit.frontendupload.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		} else {
			// Catch scriptures from database again because the values in UserState can't be used due to formatting.
			$data['scripture'] = array();
			if($data['id']){
				$db		= JFactory::getDBO();
				$query	= "SELECT book, cap1, vers1, cap2, vers2, text \n"
						."FROM #__sermon_scriptures \n"
						."WHERE sermon_id = ".$data['id']." \n"
						."ORDER BY ordering ASC"
						;
				$db->setQuery($query);
				$data['scripture'] = $db->loadAssocList();
			}
		}
		// Depreceated with SermonSpeaker 4.4.4. Using Ajax now for Lookup.
		// Reading ID3 Tags if the Lookup Button was pressed
		$jinput	= JFactory::getApplication()->input;
		if ($id3_file = $jinput->get('file', '', 'string')){
			if ($jinput->get('type') == 'video'){
				$data->videofile = $id3_file;
			} else {
				$data->audiofile = $id3_file;
			}
			require_once JPATH_COMPONENT_SITE.'/helpers/id3.php';
			$params	= JComponentHelper::getParams('com_sermonspeaker');

			$id3 = SermonspeakerHelperId3::getID3($id3_file, $params);
			if ($id3){
				foreach ($id3 as $key => $value){
					if ($value){
						$data->$key = $value;
					}
				}
			} else {
				JError::raiseNotice(100, JText::_('COM_SERMONSPEAKER_ERROR_ID3'));
			}
		}

		return $data;
	}
}