<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;

/**
 * View to close a modal.
 *
 * @package        Sermonspeaker.Administrator
 *
 * @since          ?
 */
class SermonspeakerViewClose extends HtmlView
{
	/**
	 * Display the view
	 *
	 * @param   null  $tpl
	 *
	 * @return mixed|void
	 * @since  ?
	 *
	 */
	public function display($tpl = null)
	{
		$js = "window.onload=function closeme(){
				window.setTimeout('parent.location.reload()', 500);
			}";

		$document = Factory::getDocument();
		$document->addScriptDeclaration($js);
	}
}