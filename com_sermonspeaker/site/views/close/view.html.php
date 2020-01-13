<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2019 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * View to close a modal
 *
 * @since  5
 */
class SermonspeakerViewClose extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @since ?
	 */
	public function display($tpl = null)
	{
		$js = "window.onload=function closeme(){
				window.setTimeout('parent.location.reload()', 500);
			}";

		$document = JFactory::getDocument();
		$document->addScriptDeclaration($js);

		return;
	}
}
