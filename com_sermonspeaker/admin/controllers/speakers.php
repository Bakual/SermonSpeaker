<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

/**
 * Speakers list controller class.
 *
 * @package        SermonSpeaker.Administrator
 *
 * @since          3.4
 */
class SermonspeakerControllerSpeakers extends AdminController
{
	/**
	 * Method to set the default speaker. Copied from template styles
	 *
	 * @since    1.6
	 */
	public function setDefault()
	{
		// Check for request forgeries
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$pks = $this->input->post->get('cid', array(), 'array');
		$pks = ArrayHelper::toInteger($pks);

		try
		{
			if (empty($pks))
			{
				throw new Exception(Text::_('JERROR_NO_ITEMS_SELECTED'));
			}

			// Pop off the first element.
			$id    = array_shift($pks);
			$model = $this->getModel();
			$model->setDefault($id);
			$this->setMessage(Text::_('COM_SERMONSPEAKER_SUCCESS_HOME_SET'));

		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}

		$this->setRedirect('index.php?option=com_sermonspeaker&view=speakers');
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  SermonspeakerModelSpeaker|boolean  Model object on success; otherwise false on failure.
	 *
	 * @since 3.4
	 */
	public function getModel($name = 'Speaker', $prefix = 'SermonspeakerModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Method to unset the default speaker. Copied from template styles
	 *
	 * @since    5.8.0
	 */
	public function unsetDefault()
	{
		// Check for request forgeries
		Session::checkToken('request') or jexit(Text::_('JINVALID_TOKEN'));

		$pks = $this->input->post->get('cid', array(), 'array');
		$pks = ArrayHelper::toInteger($pks);

		try
		{
			if (empty($pks))
			{
				throw new Exception(Text::_('JERROR_NO_ITEMS_SELECTED'));
			}

			// Pop off the first element.
			$id    = array_shift($pks);
			$model = $this->getModel();
			$model->unsetDefault($id);
			$this->setMessage(Text::_('COM_SERMONSPEAKER_SUCCESS_HOME_UNSET'));
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}

		$this->setRedirect('index.php?option=com_sermonspeaker&view=speakers');
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return    void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		$pks   = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');

		// Sanitize the input
		$pks   = ArrayHelper::toInteger($pks);
		$order = ArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		Factory::getApplication()->close();
	}
}