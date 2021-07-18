<?php
// No direct access
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

/**
 * View to close a modal.
 *
 * @package        Sermonspeaker.Administrator
 *
 * @since          ?
 */
class SermonspeakerViewClose extends JViewLegacy
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