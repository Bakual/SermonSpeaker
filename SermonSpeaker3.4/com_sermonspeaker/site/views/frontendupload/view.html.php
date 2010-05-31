<?php
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the SermonSpeaker Component
 */
class SermonspeakerViewFrontendupload extends JView
{
	function display($tpl = null)
	{
		global $option;
		
		JHTML::stylesheet('sermonspeaker.css', 'components/com_sermonspeaker/');

		$params	=& JComponentHelper::getParams('com_sermonspeaker');
		$session	=& JFactory::getSession();
		
		// get the allowed Usergroups from Settings
		$groups = $params->get('fu_usergroup');

		// check if groups are defined and frontendupload enabled and logged in user authorized
		if ($groups != "" && $params->get('fu_enable') == "1") {
			// creating the ACLs based on the config
			$auth =& JFactory::getACL();
			if (is_array($groups)){
				foreach ($groups as $group){
					$auth->addACL('com_sermonspeaker', 'display', 'users', $auth->get_group_name($group));
				}
			} elseif ($groups != ''){
				$auth->addACL('com_sermonspeaker', 'display', 'users', $auth->get_group_name($groups));
			}
			// get the logged in user
			$user =& JFactory::getUser();
			
			if ($user->authorize('com_sermonspeaker', 'display')) {
				$session->set('loggedin','loggedin');
				header('HTTP/1.1 303 See Other');
				header('Location: index.php?option=com_sermonspeaker&view=fu_step_1');
				return;
			}
		}
		
		// check if taskname is defined and frontendupload enabled and delivered taskname correct
		if ($params->get('fu_taskname') != "" && $params->get('fu_enable') == "1") {
			$frup	= JRequest::getVar('frup',"");
			$pwd 	= JFilterInput::clean(JRequest::getVar('pwd'),string);
			if ($params->get('fu_taskname') == $frup) {
				if ($pwd) {
					// Form was submitted
					if ($pwd == $params->get('fu_pwd')) {
						$session->set('loggedin','loggedin');
						header('HTTP/1.1 303 See Other');
						header('Location: index.php?option=com_sermonspeaker&view=fu_step_1');
						return;
					} else {
						header('HTTP/1.1 303 See Other');
						header('Location: index.php?option=com_sermonspeaker&view=frontendupload&frup='.$frup);
						return;
					}
				} else {
					// display Form
					$this->assignRef('frup',$frup);
					parent::display($tpl);
				}
			} else {
				header('HTTP/1.1 303 See Other');
				header('Location: index.php');
				return;
			}
		} else {
			header('HTTP/1.1 303 See Other');
			header('Location: index.php');
			return;
		}
	}	
}