<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\View\Main;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Sermonspeaker\Component\Sermonspeaker\Administrator\Helper\SermonspeakerHelper;


defined('_JEXEC') or die;

class HtmlView extends BaseHtmlView
{
	function display($tpl = null)
	{
		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since  ?
	 */
	protected function addToolbar()
	{
		$canDo = SermonspeakerHelper::getActions();
		ToolbarHelper::title(Text::_('COM_SERMONSPEAKER'), 'speakers');

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			ToolbarHelper::preferences('com_sermonspeaker');
		}
	}
}