<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2019 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  3.4
 */
class SermonspeakerViewTools extends JViewLegacy
{
	/**
	 * @var bool
	 *
	 * @since  ?
	 */
	protected $pi;

	/**
	 * @var bool
	 *
	 * @since  ?
	 */
	protected $bs;

	/**
	 * Execute and display a template script.
	 *
	 * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @throws Exception
	 * @since   3.4
	 * @see     JViewLegacy::loadTemplate()
	 */
	public function display($tpl = null)
	{
		$layout = $this->getLayout();

		// Check if PreachIt is installed
		$db       = JFactory::getDbo();
		$prefix   = $db->getPrefix();
		$tables   = $db->getTableList();
		$this->pi = in_array($prefix . 'pistudies', $tables);
		$this->bs = in_array($prefix . 'bsms_studies', $tables);

		// We don't need toolbar in the modal window.
		if ($layout !== 'time')
		{
			$this->addToolbar();
		}

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
		JToolbarHelper::title(JText::sprintf('COM_SERMONSPEAKER_TOOLBAR_TITLE', JText::_('COM_SERMONSPEAKER_MAIN_TOOLS')), 'tools');

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			JToolbarHelper::preferences('com_sermonspeaker');
		}
	}
}
