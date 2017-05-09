<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Controller\Controller;
use Joomla\CMS\Access\Exception\Notallowed;

class SermonspeakerController extends Controller
{
	protected $default_view = 'main';

	public function display($cachable = false, $urlparams = false)
	{
		$view   = $this->input->get('view', 'main');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');
		$views  = array('sermon', 'serie', 'speaker');

		// Check for edit form.
		if (in_array($view, $views) && $layout == 'edit' && !$this->checkEditId('com_sermonspeaker.edit.' . $view, $id))
		{
			throw new Notallowed($this->app->getLanguage()->_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$params = JComponentHelper::getParams('com_sermonspeaker');

		if ($params->get('css_icomoon') == '')
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_SERMONSPEAKER_NOTSAVED'), 'warning');
		}

		if (!JPluginHelper::isEnabled('sermonspeaker'))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_SERMONSPEAKER_NO_PLAYER_ENABLED'), 'warning');
		}

		return parent::display();
	}
}