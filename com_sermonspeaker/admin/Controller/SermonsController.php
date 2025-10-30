<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\Controller;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Input\Input;
use Joomla\Utilities\ArrayHelper;
use Sermonspeaker\Component\Sermonspeaker\Administrator\Model\SermonModel;

defined('_JEXEC') or die;

/**
 * Series list controller class.
 *
 * @package        SermonSpeaker.Administrator
 *
 * @since          3.4
 */
class SermonsController extends AdminController
{
	/**
	 * Constructor.
	 *
	 * @param   array                 $config   An optional associative array of configuration settings.
	 *                                          Recognized key values include 'name', 'default_task', 'model_path', and
	 *                                          'view_path' (this list is not meant to be comprehensive).
	 * @param   ?MVCFactoryInterface  $factory  The factory.
	 * @param   ?CMSApplication       $app      The Application for the dispatcher
	 * @param   ?Input                $input    Input
	 *
	 * @since   3.4
	 */
	public function __construct($config = [], ?MVCFactoryInterface $factory = null, $app = null, $input = null)
	{
		parent::__construct($config, $factory, $app, $input);

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
		$cid   = $this->app->input->get('cid', array(), 'array');
		$data  = array('podcast_publish' => 1, 'podcast_unpublish' => 0);
		$task  = $this->getTask();
		$value = ArrayHelper::getValue($data, $task, 0, 'int');

		if (empty($cid))
		{
			$this->app->enqueueMessage(Text::_($this->text_prefix . '_NO_ITEM_SELECTED'), 'warning');
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
				$this->app->enqueueMessage($model->getError(), 'warning');
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
	 * @return  SermonModel|boolean  Model object on success; otherwise false on failure.
	 *
	 * @since   4.5
	 */
	public function getModel($name = 'Sermon', $prefix = 'Administrator', $config = array('ignore_request' => true))
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
		$this->app->close();
	}
}