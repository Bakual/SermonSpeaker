<?php
/**
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Tools Sermonspeaker Controller
 */
class SermonspeakerControllerTools extends JController
{
	function order(){
		// Check for request forgeries
		JRequest::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
		$db		= JFactory::getDBO();
		$query	= "SET @c := 0";
		$db->setQuery($query);
		$db->query();
		$query	= "UPDATE #__sermon_sermons SET ordering = ( SELECT @c := @c + 1 ) ORDER BY sermon_date ASC, id ASC;";
		$db->setQuery($query);
		$db->query();
		$error = $db->getErrorMsg();
		if ($error){
			$this->setMessage('Error: '.$error, 'error');
		} else {
			$this->setMessage('Successfully reordered the sermons');
		}
		$this->setRedirect('index.php?option=com_sermonspeaker&view=tools');
	}
}
