<?php
// No direct access
defined('_JEXEC') or die;
/**
 * View to close a modal.
 *
 * @package		Sermonspeaker.Administrator
 */
class SermonspeakerViewClose extends JViewLegacy
{
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$js	= "window.onload=function closeme(){
				window.setTimeout('parent.location.reload()', 500);
			}";

		$document = JFactory::getDocument();
		$document->addScriptDeclaration($js);
	}
}