<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

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
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @see     JViewLegacy::loadTemplate()
	 * @since   3.4
	 */
	public function display($tpl = null)
	{
		SermonspeakerHelper::addSubmenu('help');

		// Get current version of SermonSpeaker
		$component  = JComponentHelper::getComponent('com_sermonspeaker');
		$extensions = JTable::getInstance('extension');
		$extensions->load($component->id);
		$manifest      = json_decode($extensions->manifest_cache);
		$this->version = $manifest->version;

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();

		return parent::display($tpl);
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
		JToolbarHelper::title(JText::_('JHELP'), 'support sermonhelp');

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			JToolbarHelper::preferences('com_sermonspeaker');
		}
	}
}
