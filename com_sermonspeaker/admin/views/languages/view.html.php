<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2019 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  5.x
 */
class SermonspeakerViewLanguages extends JViewLegacy
{
	/**
	 * @var array
	 *
	 * @since  ?
	 */
	protected $installed;

	/**
	 * @param null $tpl
	 *
	 * @return mixed|void
	 *
	 * @throws Exception
	 * @since  ?
	 */
	function display($tpl = null)
	{
		$db = Factory::getDbo();

		// Get installed language packs
		$query = $db->getQuery(true);
		$query->select('ext.extension_id, ext.name, ext.element');
		$query->from('`#__extensions` AS ext');
		$query->where('`element` LIKE "SermonSpeaker LanguagePack%"');
		$db->setQuery($query);

		$this->installed = $db->loadObjectList('element');

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
		JToolbarHelper::title(Text::_('COM_SERMONSPEAKER_MAIN_LANGUAGES'), 'comments-2 languages');

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			JToolbarHelper::preferences('com_sermonspeaker');
		}

		return;
	}
}
