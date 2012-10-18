<?php
/**
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Serie Sermonspeaker Controller
 *
 */
class SermonspeakerControllerSerie extends JControllerLegacy
{
	/**
	 * Redirecting to new AJAX based download function for backward compatibility
	 */
	function download()
	{
		$app	= JFactory::getApplication();
		$id		= $app->input->get('id', 0, 'int');
		$app->redirect(JRoute::_(SermonspeakerHelperRoute::getSerieRoute($id).'&layout=download'));
	}
}