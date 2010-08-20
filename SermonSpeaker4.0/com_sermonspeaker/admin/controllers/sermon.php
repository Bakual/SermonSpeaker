<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Serie controller class.
 *
 * @package		SermonSpeaker.Administrator
 */
class SermonspeakerControllerSermon extends JControllerForm
{
	/**
	 * Method override to check if you can add a new record.
	 * Quite useless now, but may change if we add ACLs to SermonSpeaker
	 *
	 * @param	array	$data	An array of input data.
	 * @return	boolean
	 */
	protected function allowAdd($data = array())
	{
		return true;
	}

	/**
	 * Method to check if you can add a new record.
	 * Quite useless now, but may change if we add ACLs to SermonSpeaker
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		return true;
	}

	public function resetcount()
	{
		$database	= &JFactory::getDBO();
		$id 		= JRequest::getInt('id', 0);

		$query	= "UPDATE #__sermon_sermons \n"
				. "SET hits='0' \n"
				. "WHERE id='".$id."'"
				;
		$database->setQuery($query);
		if (!$database->query()) {
			echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			exit();
		}

	$this->setRedirect('index.php?option=com_sermonspeaker&task=sermon.edit&id='.$id);
	}
}