<?php
/**
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * File Sermonspeaker Controller
 * Copied and adapted from File Media Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @since		1.6
 */
class SermonspeakerControllerScript extends JControllerLegacy
{
	public function createAutomatic()
	{
		$this->addModelPath(JPATH_COMPONENT_ADMINISTRATOR.'/models');
		require_once (JPATH_COMPONENT_ADMINISTRATOR.'/controllers/tools.php');
		$admin_controller = new SermonspeakerControllerTools;
		$admin_controller->createAutomatic();
		$this->setRedirect(JURI::root());
		return;
	}
}
