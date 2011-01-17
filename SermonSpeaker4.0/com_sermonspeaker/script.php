<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class Com_SermonspeakerInstallerScript {

	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent) {
		$parent->getParent()->setRedirectURL('index.php?option=com_sermonspeaker');
	}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent) {
		echo JText::_('COM_SERMONSPEAKER_UNINSTALL_TEXT');
	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent) {
		echo JText::_('COM_SERMONSPEAKER_UPDATE_TEXT');
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent) {
		echo JText::sprintf('COM_SERMONSPEAKER_PREFLIGHT', $type);
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
 	function postflight($type, $parent) {
		echo JText::sprintf('COM_SERMONSPEAKER_POSTFLIGHT', $type);
	}
}