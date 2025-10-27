<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\View\Help;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Sermonspeaker\Component\Sermonspeaker\Administrator\Helper\SermonspeakerHelper;

defined('_JEXEC') or die;

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * The HTML code for the sidebar.
	 *
	 * @var string
	 *
	 * @since  ?
	 */
	protected string $sidebar;

	/**
	 * @var  string  SermonSpeaker version
	 *
	 * @since  ?
	 */
	protected string $version;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @throws \Exception
	 * @since   3.4
	 * @see     HtmlView::loadTemplate()
	 */
	public function display($tpl = null): void
	{
		// Get current version of SermonSpeaker
		$component  = ComponentHelper::getComponent('com_sermonspeaker');
		$extensions = Table::getInstance('extension');
		$extensions->load($component->id);
		$manifest      = json_decode($extensions->manifest_cache);
		$this->version = $manifest->version;

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
	protected function addToolbar(): void
	{
		$canDo = SermonspeakerHelper::getActions();
		ToolbarHelper::title(Text::_('JHELP'), 'support sermonhelp');

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			ToolbarHelper::preferences('com_sermonspeaker');
		}
	}
}
