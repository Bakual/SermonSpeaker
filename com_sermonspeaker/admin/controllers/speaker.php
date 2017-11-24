<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

/**
 * Speaker controller class.
 *
 * @package        SermonSpeaker.Administrator
 *
 * @since          3.4
 */
class SermonspeakerControllerSpeaker extends JControllerForm
{
	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param    array $data An array of input data.
	 *
	 * @return    boolean
	 *
	 * @since 3.4
	 */
	protected function allowAdd($data = array())
	{
		$user       = JFactory::getUser();
		$categoryId = Joomla\Utilities\ArrayHelper::getValue($data, 'catid', $this->input->get('filter_category_id'), 'int');
		$allow      = null;

		if ($categoryId)
		{
			// If the category has been passed in the data or URL check it.
			$allow = $user->authorise('core.create', $this->option . '.category.' . $categoryId);
		}

		if ($allow === null)
		{
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd($data);
		}
		else
		{
			return $allow;
		}
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * @param   array  $data An array of input data.
	 * @param   string $key  The name of the key for the primary key.
	 *
	 * @return  boolean
	 *
	 * @since 3.4
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;

		if (!$recordId)
		{
			return parent::allowEdit($data, $key);
		}

		// Need to do a lookup from the model.
		/** @var SermonspeakerModelSpeaker $model */
		$model      = $this->getModel();
		$record     = $model->getItem($recordId);
		$categoryId = (int) $record->catid;

		if (!$categoryId)
		{
			// No category set, fall back to component permissions
			return parent::allowEdit($data, $key);
		}

		$user = JFactory::getUser();

		// The category has been set. Check the category permissions.
		if ($user->authorise('core.edit', $this->option . '.category.' . $categoryId))
		{
			return true;
		}

		// Fallback on edit.own.
		if ($user->authorise('core.edit.own', $this->option . '.category.' . $categoryId))
		{
			return ($record->created_by == $user->id);
		}

		return false;
	}

	/**
	 * Reset hit counters
	 *
	 * @return  void
	 *
	 * @since ?
	 */
	public function reset()
	{
		$app    = JFactory::getApplication();
		$db     = JFactory::getDbo();
		$id     = $this->input->get('id', 0, 'int');

		if (!$id)
		{
			$app->enqueueMessage(JText::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
			$app->redirect('index.php?option=com_sermonspeaker&view=speakers');

			return;
		}

		/** @var SermonspeakerModelSpeaker $model */
		$model      = $this->getModel();
		$item       = $model->getItem($id);
		$user       = JFactory::getUser();
		$canEdit    = $user->authorise('core.edit', 'com_sermonspeaker.category.' . $item->catid);
		$canEditOwn = $user->authorise('core.edit.own', 'com_sermonspeaker.category.' . $item->catid) && $item->created_by == $user->id;

		if ($canEdit || $canEditOwn)
		{
			$query = "UPDATE #__sermon_speakers \n"
				. "SET hits='0' \n"
				. "WHERE id='" . $id . "'";
			$db->setQuery($query);
			$db->execute();
			$app->enqueueMessage(JText::sprintf('COM_SERMONSPEAKER_RESET_OK', JText::_('COM_SERMONSPEAKER_SPEAKER'), $item->title));
		}
		else
		{
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
		}

		$app->redirect('index.php?option=com_sermonspeaker&view=speakers');

		return;
	}

	/**
	 * Method to run batch operations.
	 *
	 * @param   object $model The model.
	 *
	 * @return  boolean     True if successful, false otherwise and internal error is set.
	 *
	 * @since   1.7
	 */
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model = $this->getModel('Speaker', '', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_sermonspeaker&view=speakers' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}

	/**
	 * @param null $recordId
	 * @param null $urlVar
	 *
	 * @return string
	 *
	 * @since ?
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = null)
	{
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);
		$modal  = $this->input->get('modal', 0, 'int');
		$return = $this->getReturnPage();

		if ($modal)
		{
			$append .= '&tmpl=component';
		}

		if ($return)
		{
			$append .= '&return=' . base64_encode($return);
		}

		return $append;
	}

	/**
	 * Get the return URL.
	 *
	 * If a "return" variable has been passed in the request
	 *
	 * @return    string    The return URL.
	 * @since    1.6
	 */
	protected function getReturnPage()
	{
		$return = $this->input->get('return', '', 'base64');

		if (empty($return) || !JUri::isInternal(base64_decode($return)))
		{
			return false;
		}
		else
		{
			return base64_decode($return);
		}
	}

	/**
	 * Method to save a record.
	 *
	 * @param    string $key    The name of the primary key of the URL variable.
	 * @param    string $urlVar The name of the URL variable if different from the primary key (sometimes required to
	 *                          avoid router collisions).
	 *
	 * @return    Boolean    True if successful, false otherwise.
	 * @since    1.6
	 */
	public function save($key = null, $urlVar = 'id')
	{
		$result = parent::save($key, $urlVar);

		// If ok, redirect to the return page.
		if ($result && ($return = $this->getReturnPage()))
		{
			$this->setRedirect($return);
		}

		return $result;
	}
}