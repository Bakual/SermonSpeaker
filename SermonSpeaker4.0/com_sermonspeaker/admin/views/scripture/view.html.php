<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class SermonspeakerViewScripture extends JView
{
	function display( $tpl = null )
	{
		// add Javascript for Scripture
		$javascript = "function AddScripture() {
			window.parent.document.getElementById('jform_scripture').value = document.getElementById('book').value;
		}";

			
/*			
		alert(document.getElementById('jform_scripture').value);
		alert(window.parent.document.getElementById('jform_scripture').value);
		alert(window.parent.getElementById(book).value);
		alert(window.parent.SqueezeBox.getElementById(book).value);
*/
		$document =& JFactory::getDocument();
		$document->addScriptDeclaration($javascript);

		parent::display($tpl);
	}
}