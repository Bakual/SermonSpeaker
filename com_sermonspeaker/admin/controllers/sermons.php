<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

/**
 * Series list controller class.
 *
 * @package        SermonSpeaker.Administrator
 *
 * @since          3.4
 */
class SermonspeakerControllerSermons extends JControllerAdmin
{
	/**
	 * SermonspeakerControllerSermons constructor.
	 *
	 * @param array $config
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
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string $name   The model name. Optional.
	 * @param   string $prefix The class prefix. Optional.
	 * @param   array  $config Configuration array for model. Optional.
	 *
	 * @return  SermonspeakerModelSermon|boolean  Model object on success; otherwise false on failure.
	 *
	 * @since   4.5
	 */
	public function &getModel($name = 'Sermon', $prefix = 'SermonspeakerModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
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
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to podcast from the request.
		$cid   = JFactory::getApplication()->input->get('cid', array(), 'array');
		$data  = array('podcast_publish' => 1, 'podcast_unpublish' => 0);
		$task  = $this->getTask();
		$value = Joomla\Utilities\ArrayHelper::getValue($data, $task, 0, 'int');

		if (empty($cid))
		{
			JFactory::getApplication()->enqueueMessage(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), 'warning');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			$cid = Joomla\Utilities\ArrayHelper::toInteger($cid);

			// Podcast the items.
			if (!$model->podcast($cid, $value))
			{
				JFactory::getApplication()->enqueueMessage($model->getError(), 'warning');
			}
			else
			{
				$ntext = $this->text_prefix;
				$ntext .= ($value == 1) ? '_N_ITEMS_PODCASTED' : '_N_ITEMS_UNPODCASTED';

				$this->setMessage(JText::plural($ntext, count($cid)));
			}
		}

		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
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
		$pks   = Joomla\Utilities\ArrayHelper::toInteger($pks);
		$order = Joomla\Utilities\ArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}
}