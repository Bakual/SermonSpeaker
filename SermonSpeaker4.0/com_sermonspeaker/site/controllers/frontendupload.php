<?php
/**
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.controllerform');

/**
 * Frontendupload Sermonspeaker Controller
 *
 */
class SermonspeakerControllerFrontendupload extends JControllerForm
{
	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 *
	 * @return	JControllerForm
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		// Redirect View, defined as it would go to plural version otherwise
		$this->view_list = 'frontendupload';

		parent::__construct($config);
	}

	/**
	 * Method override to check if you can add a new record.
	 * Quite useless now, but may change if we add ACLs to SermonSpeaker
	 *
	 * @param	array	$data	An array of input data.
	 * @return	boolean
	 */
	protected function allowAdd($data = array())
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$categoryId	= JArrayHelper::getValue($data, 'catid', JRequest::getInt('filter_category_id'), 'int');
		$allow		= null;

		if ($categoryId) {
			// If the category has been passed in the data or URL check it.
			$allow	= $user->authorise('core.create', 'com_sermonspeaker.category.'.$categoryId);
		}

		if ($allow === null) {
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd();
		} else {
			return $allow;
		}
	}
}