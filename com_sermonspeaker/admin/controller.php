<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Access\Exception\Notallowed;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Plugin\PluginHelper;

class SermonspeakerController extends BaseController
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

		$params = ComponentHelper::getParams('com_sermonspeaker');

		if ($params->get('css_fontawesome') == '')
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_SERMONSPEAKER_NOTSAVED'), 'warning');
		}

		if (!PluginHelper::isEnabled('sermonspeaker'))
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_SERMONSPEAKER_NO_PLAYER_ENABLED'), 'warning');
		}

		return parent::display();
	}
}