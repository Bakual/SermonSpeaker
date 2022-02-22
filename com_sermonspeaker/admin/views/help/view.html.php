<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerViewHelp extends JViewLegacy
{
	/**
	 * The HTML code for the sidebar.
	 *
	 * @var string
	 *
	 * @since  ?
	 */
	protected $sidebar;

	/**
	 * @var  string  SermonSpeaker version
	 *
	 * @since  ?
	 */
	protected $version;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @throws Exception
	 * @since   3.4
	 * @see     JViewLegacy::loadTemplate()
	 */
	public function display($tpl = null)
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
	protected function addToolbar()
	{
		$canDo = SermonspeakerHelper::getActions();
		ToolbarHelper::title(Text::_('JHELP'), 'support sermonhelp');

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			ToolbarHelper::preferences('com_sermonspeaker');
		}
	}
}
