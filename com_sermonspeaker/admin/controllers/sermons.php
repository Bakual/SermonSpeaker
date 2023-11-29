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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

/**
 * Series list controller class.
 *
 * @package        SermonSpeaker.Administrator
 *
 * @since          3.4
 */
class SermonspeakerControllerSermons extends AdminController
{
	/**
	 * SermonspeakerControllerSermons constructor.
	 *
	 * @param   array  $config
	 *
	 * @since 3.4
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Define standard task mappings.
		$this->registerTask('podcast_unpublish', 'podcast_publish');
	}

	/**
	 * Publish/Unpiblish the podcast state
	 *
	 * @return void
	 *
	 * @since ?
	 */
	function podcast_publish()
	{
		// Check for request forgeries
		Session::checkToken() or die(Text::_('JINVALID_TOKEN'));

		// Get items to podcast from the request.
		$cid   = Factory::getApplication()->input->get('cid', array(), 'array');
		$data  = array('podcast_publish' => 1, 'podcast_unpublish' => 0);
		$task  = $this->getTask();
		$value = ArrayHelper::getValue($data, $task, 0, 'int');

		if (empty($cid))
		{
			Factory::getApplication()->enqueueMessage(Text::_($this->text_prefix . '_NO_ITEM_SELECTED'), 'warning');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			$cid = ArrayHelper::toInteger($cid);

			// Podcast the items.
			if (!$model->podcast($cid, $value))
			{
				Factory::getApplication()->enqueueMessage($model->getError(), 'warning');
			}
			else
			{
				$ntext = $this->text_prefix;
				$ntext .= ($value == 1) ? '_N_ITEMS_PODCASTED' : '_N_ITEMS_UNPODCASTED';

				$this->setMessage(Text::plural($ntext, count($cid)));
			}
		}

		$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  SermonspeakerModelSermon|boolean  Model object on success; otherwise false on failure.
	 *
	 * @since   4.5
	 */
	public function getModel($name = 'Sermon', $prefix = 'SermonspeakerModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
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