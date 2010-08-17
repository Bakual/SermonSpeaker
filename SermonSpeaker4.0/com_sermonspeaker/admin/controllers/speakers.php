<?php
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Speakers list controller class.
 *
 * @package		SermonSpeaker.Administrator
 */
class SermonspeakerControllerSpeakers extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 */
	public function &getModel($name = 'Speaker', $prefix = 'SermonspeakerModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}