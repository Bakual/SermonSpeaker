<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2014 - Thomas Hunziker
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
	function display( $tpl = null )
	{
		SermonspeakerHelper::addSubmenu('languages');

		/* Settings for XML parsing */
		$url				= 'http://www.sermonspeaker.net/languages.raw';
		$this->site			= 'http://www.sermonspeaker.net';
		$this->prefix		= strtoupper(JApplicationHelper::getComponentName());

		/* Loading XML and installed languages */
		$this->xml			= simplexml_load_file($url);
		$this->languages	= JFactory::getLanguage()->getKnownLanguages();

		// Get extension info
		$db		= JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "'.$this->prefix.'"');
		$this->manifest		= json_decode($db->loadResult(), true);

		// Get installed language packs
		$query	= $db->getQuery(true);
		$query->select('ext.name, ext.manifest_cache, ext.element');
		$query->from('`#__extensions` AS ext');
		$query->where('`element` LIKE "'.$this->xml->extension_name.'%"');
		$db->setQuery($query);
		$this->installed	= $db->loadObjectList('element');
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
							$value = $date[2].'-'.$date[1].'-'.$date[0];
						}
					}
					$item->$key = $value;
				}
			}
		}

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		$canDo 	= SermonspeakerHelper::getActions();
		JToolBarHelper::title(JText::_('COM_SERMONSPEAKER_MAIN_LANGUAGES'), 'comments-2 languages');
		if ($canDo->get('core.admin')) {
			JToolbarHelper::divider();
			JToolBarHelper::preferences('com_sermonspeaker', 650, 900);
		}
	}
}