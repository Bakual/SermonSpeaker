<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\Controller;

use Joomla\CMS\Access\Exception\Notallowed;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Plugin\PluginHelper;

defined('_JEXEC') or die;

class DisplayController extends BaseController
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

		if ($params->get('path_mode_audio') || $params->get('path_mode_video'))
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_SERMONSPEAKER_MODE_DEPRECATED'), 'warning');
		}

		if (!$params->get('path_mode_audio') && !str_contains($params->get('path_audio'), ':/'))
		{
			Factory::getApplication()->enqueueMessage(Text::sprintf('COM_SERMONSPEAKER_WRONG_FILEPATH', Text::_('COM_SERMONSPEAKER_FIELD_PATH_AUDIO_LABEL')), 'warning');
		}

		if (!$params->get('path_mode_video') && !str_contains($params->get('path_video'), ':/'))
		{
			Factory::getApplication()->enqueueMessage(Text::sprintf('COM_SERMONSPEAKER_WRONG_FILEPATH', Text::_('COM_SERMONSPEAKER_FIELD_PATH_VIDEO_LABEL')), 'warning');
		}

		if (!str_contains($params->get('path_addfile'), ':/'))
		{
			Factory::getApplication()->enqueueMessage(Text::sprintf('COM_SERMONSPEAKER_WRONG_FILEPATH', Text::_('COM_SERMONSPEAKER_FIELD_ADDFILEPATH_LABEL')), 'warning');
		}

		if (!str_contains($params->get('path_avatar'), ':/'))
		{
			Factory::getApplication()->enqueueMessage(Text::sprintf('COM_SERMONSPEAKER_WRONG_FILEPATH', Text::_('COM_SERMONSPEAKER_PARAM_PATH_AVATAR')), 'warning');
		}

		if (!str_contains($params->get('path_speakerpic'), ':/'))
		{
			Factory::getApplication()->enqueueMessage(Text::sprintf('COM_SERMONSPEAKER_WRONG_FILEPATH', Text::_('COM_SERMONSPEAKER_PARAM_PATH_SPEAKERPIC_LABEL')), 'warning');
		}

		if (!str_contains($params->get('path_sermonpic'), ':/'))
		{
			Factory::getApplication()->enqueueMessage(Text::sprintf('COM_SERMONSPEAKER_WRONG_FILEPATH', Text::_('COM_SERMONSPEAKER_PARAM_PATH_SERMONPIC_LABEL')), 'warning');
		}

		if (!PluginHelper::isEnabled('sermonspeaker'))
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_SERMONSPEAKER_NO_PLAYER_ENABLED'), 'warning');
		}

		return parent::display();
	}
}