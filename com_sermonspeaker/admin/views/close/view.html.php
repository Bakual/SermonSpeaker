<?php
// No direct access
defined('_JEXEC') or die;
/**
 * View to close a modal.
 *
 * @package		Sermonspeaker.Administrator
 *
 * @since  ?
 */
class SermonspeakerViewClose extends JViewLegacy
{
	/**
	 * Display the view
	 *
	 * @since  ?
	 *
	 * @param null $tpl
	 *
	 * @return mixed|void
	 */
	public function display($tpl = null)
	{
		$js	= "window.onload=function closeme(){
				window.setTimeout('parent.location.reload()', 500);
			}";

		$document = JFactory::getDocument();
		$document->addScriptDeclaration($js);

		return;
	}
}