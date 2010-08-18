<?php
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Series list controller class.
 *
 * @package		SermonSpeaker.Administrator
 */
class SermonspeakerControllerSermons extends JControllerAdmin
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Define standard task mappings.
		$this->registerTask('podcast_unpublish',	'podcast_publish');
	}

	/**
	 * Proxy for getModel.
	 */
	public function &getModel($name = 'Sermon', $prefix = 'SermonspeakerModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	function podcast_publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to podcast from the request.
		$cid	= JRequest::getVar('cid', array(), '', 'array');
		$data	= array('podcast_publish' => 1, 'podcast_unpublish' => 0);
		$task 	= $this->getTask();
		$value	= JArrayHelper::getValue($data, $task, 0, 'int');

		if (empty($cid)) {
			JError::raiseWarning(500, JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
		} else {
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			JArrayHelper::toInteger($cid);

			// Podcast the items.
			if (!$model->podcast($cid, $value)) {
				JError::raiseWarning(500, $model->getError());
			} else {
				if ($value == 1) {
					$ntext = $this->text_prefix.'_N_ITEMS_PODCASTED';
				} else if ($value == 0) {
					$ntext = $this->text_prefix.'_N_ITEMS_UNPODCASTED';
				}
				$this->setMessage(JText::plural($ntext, count($cid)));
			}
		}

		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
	}
}