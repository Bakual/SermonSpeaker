<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Plugin.Button
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Session\Session;

/**
 * Editorbutton for the SermonSpeaker content plugin
 *
 * @since  1.0
 */
class PlgButtonSermonspeaker extends CMSPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * Renders the button and adds the JS
	 *
	 * @param   string  $name  Name of the element
	 *
	 * @return  string  The HTML for the button
	 *
	 * @since ?
	 */
	public function onDisplay($name)
	{
		$js = "
		function jSelectSermon(id, title, catid, link, mode) {
			if (mode) {
				var tag = '{sermonspeaker '+id+','+mode+'}';
			} else {
				var tag = '{sermonspeaker '+id+'}';
			}
			window.parent.Joomla.editors.instances['" . $name . "'].replaceSelection(tag);
			if (window.parent.Joomla.Modal) {
				window.parent.Joomla.Modal.getCurrent().close();
			}
		}";

		$doc = Factory::getDocument();
		$doc->addScriptDeclaration($js);

		// Use the built-in element view to select the sermon.
		$link = 'index.php?option=com_sermonspeaker&amp;view=sermons&amp;layout=modal&amp;tmpl=component&amp;'
			. Session::getFormToken() . '=1&amp;editor=' . $name;

		$button          = new CMSObject;
		$button->modal   = true;
		$button->link    = $link;
		$button->text    = Text::_('PLG_EDITORS-XTD_SERMONSPEAKER_BUTTON_SERMONSPEAKER');
		$button->name    = $this->_type . '_' . $this->_name;
		$button->icon    = 'comment';
		$button->options = [
			'height'     => '300px',
			'width'      => '800px',
			'bodyHeight' => '70',
			'modalWidth' => '80',
		];

		return $button;
	}
}
