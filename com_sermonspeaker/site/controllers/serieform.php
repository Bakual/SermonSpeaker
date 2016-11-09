<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Controller class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerControllerSerieform extends JControllerForm
{
	protected $context = 'serie';

	protected $view_item = 'serieform';

	protected $view_list = 'series';

	/**
	 * Method to add a new record
	 *
	 * @return  boolean  True if the article can be added, false if not
	 *
	 * @since ?
	 */
	public function add()
	{
		$return = parent::add();

		if (!$return)
		{
			// Redirect to the return page.
			$this->setRedirect($this->getReturnPage());
		}

		return $return;
	}

	/**
	 * Method override to check if you can add a new record
	 *
	 * @param   array  $data  An array of input data
	 *
	 * @return  boolean
	 *
	 * @since ?
	 */
	protected function allowAdd($data = array())
	{
		$user       = JFactory::getUser();
		$categoryId = Joomla\Utilities\ArrayHelper::getValue($data, 'catid', JFactory::getApplication()->input->get('filter_category_id'), 'int');
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
	 * Method to check if you can add a new record
	 *
	 * @param   array   $data  An array of input data
	 * @param   string  $key   The name of the key for the primary key
	 *
	 * @return  boolean
	 *
	 * @since ?
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;

		if (!$recordId)
		{
			return false;
		}

		// Need to do a lookup from the model.
		$record = $this->getModel()->getItem($recordId);
		$categoryId = (int) $record->catid;

		if ($categoryId)
		{
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
		}
		else
		{
			// Since there is no asset tracking, revert to the component permissions.
			return parent::allowEdit($data, $key);
		}

		return false;
	}

	/**
	 * Method to cancel an edit
	 *
	 * @param   string  $key  The name of the primary key of the URL variable
	 *
	 * @return  Boolean  True if access level checks pass, false otherwise
	 *
	 * @since ?
	 */
	public function cancel($key = 's_id')
	{
		$return = parent::cancel($key);

		// Redirect to the return page.
		$this->setRedirect($this->getReturnPage());

		return $return;
	}

	/**
	 * Method to edit an existing record
	 *
	 * @param   string  $key     The name of the primary key of the URL variable
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions)
	 *
	 * @return  Boolean  True if access level check and checkout passes, false otherwise
	 *
	 * @since ?
	 */
	public function edit($key = null, $urlVar = 's_id')
	{
		$result = parent::edit($key, $urlVar);

		return $result;
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional
	 * @param   string  $prefix  The class prefix. Optional
	 * @param   array   $config  Configuration array for model. Optional
	 *
	 * @return  object  The model
	 *
	 * @since ?
	 */
	public function getModel($name = 'serieform', $prefix = '', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Gets the URL arguments to append to an item redirect
	 *
	 * @param   int     $recordId  The primary key id for the item
	 * @param   string  $urlVar    The name of the URL variable for the id
	 *
	 * @return  string  The arguments to append to the redirect URL
	 *
	 * @since ?
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = null)
	{
		$jinput = JFactory::getApplication()->input;
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);
		$itemId = $jinput->get('Itemid', 0, 'int');
		$modal  = $jinput->get('modal', 0, 'int');
		$return = $this->getReturnPage();

		if ($itemId)
		{
			$append .= '&Itemid=' . $itemId;
		}

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
	 * Get the return URL
	 *
	 * If a "return" variable has been passed in the request
	 *
	 * @return  string  The return URL
	 *
	 * @since ?
	 */
	protected function getReturnPage()
	{
		$return = JFactory::getApplication()->input->get('return', '', 'base64');

		if (empty($return) || !JUri::isInternal(base64_decode($return)))
		{
			return JUri::base();
		}
		else
		{
			return base64_decode($return);
		}
	}

	/**
	 * Function that allows child controller access to model data after the data has been saved
	 *
	 * @param \JModel|\JModelLegacy $model     The data model object
	 * @param   array               $validData The validated data
	 *
	 * @since ?
	 */
	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
		$task = $this->getTask();

		if ($task == 'save')
		{
			$this->setRedirect(JRoute::_('index.php?option=com_sermonspeaker&view=series', false));
		}
	}

	/**
	 * Method to save a record
	 *
	 * @param   string  $key     The name of the primary key of the URL variable
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions)
	 *
	 * @return  Boolean  True if successful, false otherwise
	 *
	 * @since ?
	 */
	public function save($key = null, $urlVar = 's_id')
	{
		$result = parent::save($key, $urlVar);

		// If ok, redirect to the return page
		if ($result)
		{
			$this->setRedirect($this->getReturnPage());
		}

		return $result;
	}
}
