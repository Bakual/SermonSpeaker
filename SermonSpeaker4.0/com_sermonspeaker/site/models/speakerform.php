<?php
// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/speaker.php';

/**
 * Frontendupload model.
 *
 * @package		Sermonspeaker.Administrator
 */
class SermonspeakerModelSpeakerform extends SermonspeakerModelSpeaker
{
	/**
	 * @since	1.6
	 */
	protected $context = 'speaker';

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
		$this->setState('speakerform.id', $pk);
		// Add compatibility variable for default naming conventions.
		$this->setState('form.id', $pk);

		$categoryId	= $jinput->get('catid', 0, 'int');
		$this->setState('speakerform.catid', $categoryId);

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
}