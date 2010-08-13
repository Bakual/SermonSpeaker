<?php
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Series list controller class.
 *
 * @package		SermonSpeaker.Administrator
 */
class SermonspeakerControllerSeries extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 */
	public function &getModel($name = 'Serie', $prefix = 'SermonspeakerModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}