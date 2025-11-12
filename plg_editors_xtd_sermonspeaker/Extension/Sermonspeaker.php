<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Plugin.Button
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Plugin\EditorsXtd\Sermonspeaker\Extension;

use Joomla\CMS\Editor\Button\Button;
use Joomla\CMS\Event\Editor\EditorButtonsSetupEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Session\Session;
use Joomla\Event\SubscriberInterface;

defined('_JEXEC') or die();

/**
 * Editorbutton for the SermonSpeaker content plugin
 *
 * @since  1.0
 */
final class Sermonspeaker extends CMSPlugin implements SubscriberInterface
{
	/**
	 * Returns an array of events this subscriber will listen to.
	 *
	 * @return array
	 *
	 * @since   7.0.0
	 */
	public static function getSubscribedEvents(): array
	{
		return ['onEditorButtonsSetup' => 'onEditorButtonsSetup'];
	}

	/**
	 * Renders the button and adds the JS
	 *
	 * @param string $name Name of the element
	 *
	 * @return  string  The HTML for the button
	 *
	 * @since 7.0.0
	 */
	public function onEditorButtonsSetup(EditorButtonsSetupEvent $event): void
	{
		$subject  = $event->getButtonsRegistry();
		$disabled = $event->getDisabledButtons();
		$name     = $event->getEditorId();

		if (\in_array($this->_name, $disabled))
		{
			return;
		}

		$this->loadLanguage();

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

		$doc = Factory::getApplication()->getDocument();
		$doc->addScriptDeclaration($js);

		// Use the built-in element view to select the sermon.
		$link = 'index.php?option=com_sermonspeaker&amp;view=sermons&amp;layout=modal&amp;tmpl=component&amp;'
			. Session::getFormToken() . '=1&amp;editor=' . $name;

		$button = new Button(
			$this->_name,
			[
				'action' => 'modal',
				'link'   => $link,
				'text'   => Text::_('PLG_EDITORS-XTD_SERMONSPEAKER_BUTTON_SERMONSPEAKER'),
				'icon'   => 'comment',
				'name'   => $this->_type . '_' . $this->_name,
				'option' => [
					'height'     => '300px',
					'width'      => '800px',
					'bodyHeight' => '70',
					'modalWidth' => '80',
				],
			]
		);

		if ($button)
		{
			$subject->add($button);
		}
	}
}
