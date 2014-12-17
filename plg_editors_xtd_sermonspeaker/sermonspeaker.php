<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Plugin.Button
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2014 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * Editorbutton for the SermonSpeaker content plugin
 *
 * @since  1.0
 */
class PlgButtonSermonspeaker extends JPlugin
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
	 * @return  $button  The HTML for the button
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
			jInsertEditorText(tag, '" . $name . "');
			SqueezeBox.close();
		}";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);

		JHtml::_('behavior.modal');

		// Use the built-in element view to select the sermon.
		$link = 'index.php?option=com_sermonspeaker&amp;view=sermons&amp;layout=modal&amp;tmpl=component&amp;' . JSession::getFormToken() . '=1';

		$button = new JObject;
		$button->modal   = true;
		$button->class   = 'btn';
		$button->link    = $link;
		$button->text    = JText::_('PLG_EDITORS-XTD_SERMONSPEAKER_BUTTON_SERMONSPEAKER');
		$button->name    = 'broadcast';
		$button->options = "{handler: 'iframe', size: {x: 800, y: 500}}";

		return $button;
	}
}
