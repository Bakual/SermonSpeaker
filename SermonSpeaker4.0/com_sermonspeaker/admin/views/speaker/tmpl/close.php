<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
$js	= "window.onload=function closeme(){
		window.setTimeout('parent.location.reload()', 500);
	}";

$document = JFactory::getDocument();
$document->addScriptDeclaration($js);
?>