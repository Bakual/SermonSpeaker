<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  5.x
 */
class SermonspeakerViewLanguages extends JViewLegacy
{
	/**
	 * @var  string  Site to fetch data from
	 *
	 * @since  ?
	 */
	protected $site;

	/**
	 * @var  string component name
	 *
	 * @since  ?
	 */
	protected $prefix;

	/**
	 * @var  SimpleXMLElement  The XML file
	 *
	 * @since  ?
	 */
	protected $xml;

	/**
	 * @var  array  The known installed languages
	 *
	 * @since  ?
	 */

	protected $languages;

	/**
	 * @var array
	 *
	 * @since  ?
	 */
	protected $manifest;

	/**
	 * @var array
	 *
	 * @since  ?
	 */
	protected $installed;

	/**
	 * @var string
	 *
	 * @since  ?
	 */
	protected $sidebar;

	/**
	 * @param null $tpl
	 *
	 * @return mixed|void
	 *
	 * @since  ?
	 */
	function display($tpl = null)
	{
		SermonspeakerHelper::addSubmenu('languages');

		/* Settings for XML parsing */
		$url          = 'http://www.sermonspeaker.net/languages.raw';
		$this->site   = 'http://www.sermonspeaker.net';
		$this->prefix = strtoupper(JApplicationHelper::getComponentName());

		/* Loading XML and installed languages */
		$this->xml       = simplexml_load_file($url);
		$this->languages = JFactory::getLanguage()->getKnownLanguages();

		// Get extension info
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "' . $this->prefix . '"');
		$this->manifest = json_decode($db->loadResult(), true);

		// Get installed language packs
		$query = $db->getQuery(true);
		$query->select('ext.name, ext.manifest_cache, ext.element');
		$query->from('`#__extensions` AS ext');
		$query->where('`element` LIKE "' . (string) $this->xml->extension_name . '%"');
		$db->setQuery($query);
		$this->installed = $db->loadObjectList('element');
		foreach ($this->installed as $item)
		{
			$data = json_decode($item->manifest_cache);
			if ($data)
			{
				foreach ($data as $key => $value)
				{
					if ($key == 'type')
					{
						// Ignore the type field
						continue;
					}
					elseif ($key == 'creationDate')
					{
						$date = explode('.', $value);
						if (count($date) == 3)
						{
							$value = $date[2] . '-' . $date[1] . '-' . $date[0];
						}
					}
					$item->$key = $value;
				}
			}
		}

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
		JToolbarHelper::title(JText::_('COM_SERMONSPEAKER_MAIN_LANGUAGES'), 'comments-2 languages');

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			JToolbarHelper::preferences('com_sermonspeaker');
		}

		return;
	}
}