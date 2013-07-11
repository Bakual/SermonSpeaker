<?php
// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/serie.php';

/**
 * Frontendupload model.
 *
 * @package		Sermonspeaker.Administrator
 */
class SermonspeakerModelSerieform extends SermonspeakerModelSerie
{
	/**
	 * @since	1.6
	 */
	protected $context = 'serie';

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
		$this->setState('serieform.id', $pk);
		// Add compatibility variable for default naming conventions.
		$this->setState('form.id', $pk);

		$categoryId	= $jinput->get('catid', 0, 'int');
		$this->setState('serieform.catid', $categoryId);

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
		// Prevent deleting multilang associations
		$app = JFactory::getApplication();
		$assoc = $app->item_associations;
		$app->item_associations = 0;
		$result = parent::save($data);
		$app->item_associations = $assoc;

		return $result;
	}
}