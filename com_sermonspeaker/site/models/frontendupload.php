<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2015 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/sermon.php';

/**
 * Model class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerModelFrontendupload extends SermonspeakerModelSermon
{
	/**
	 * Get the return URL.
	 *
	 * @return	string	The return URL.
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
	 * @param   string  $ordering   Ordering column
	 * @param   string  $direction  'ASC' or 'DESC'
	 *
	 * @return  void
	 */
	public function populateState($ordering = null, $direction = null)
	{
		$app    = JFactory::getApplication();
		$jinput = $app->input;

		// Load state from the request.
		$pk = $jinput->get('s_id', 0, 'int');
		$this->setState('frontendupload.id', $pk);

		// Add compatibility variable for default naming conventions.
		$this->setState('form.id', $pk);

		$categoryId	= $jinput->get('catid', 0, 'int');
		$this->setState('frontendupload.catid', $categoryId);

		$return = $jinput->get('return', '', 'base64');

		if (!JUri::isInternal(base64_decode($return)))
		{
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
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_sermonspeaker.edit.frontendupload.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}
		else
		{
			// Catch scriptures from database again because the values in UserState can't be used due to formatting.
			$data['scripture'] = array();

			if ($data['id'])
			{
				$db    = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query->select('book, cap1, vers1, cap2, vers2, text');
				$query->from('#__sermon_scriptures');
				$query->where('sermon_id = ' . (int) $data['id']);
				$query->order('ordering ASC');
				$db->setQuery($query);
				$data['scripture'] = $db->loadAssocList();
			}
		}

		// Deprecated with SermonSpeaker 4.4.4. Using Ajax now for Lookup.
		// Reading ID3 Tags if the Lookup Button was pressed
		$jinput	= JFactory::getApplication()->input;

		if ($id3_file = $jinput->get('file', '', 'string'))
		{
			if ($jinput->get('type') == 'video')
			{
				$data->videofile = $id3_file;
			}
			else
			{
				$data->audiofile = $id3_file;
			}

			require_once JPATH_COMPONENT_SITE . '/helpers/id3.php';
			$params = JComponentHelper::getParams('com_sermonspeaker');

			$id3 = SermonspeakerHelperId3::getID3($id3_file, $params);

			if ($id3)
			{
				foreach ($id3 as $key => $value)
				{
					if ($value)
					{
						$data->$key = $value;
					}
				}
			}
			else
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_SERMONSPEAKER_ERROR_ID3'), 'notice');
			}
		}

		$this->preprocessData('com_sermonspeaker.sermon', $data);

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.2
	 */
	public function save($data)
	{
		// Associations are not edited in frontend ATM so we have to inherit them
		if (JLanguageAssociations::isEnabled() && !empty($data['id']))
		{
			if ($associations = JLanguageAssociations::getAssociations('com_sermonspeaker', '#__sermon_sermons', 'com_sermonspeaker.sermon', $data['id']))
			{
				foreach ($associations as $tag => $associated)
				{
					$associations[$tag] = (int) $associated->id;
				}

				$data['associations'] = $associations;
			}
		}

		return parent::save($data);
	}
}
