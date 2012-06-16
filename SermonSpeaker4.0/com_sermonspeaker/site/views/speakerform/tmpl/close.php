<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
$js	= "window.onload=function closeme(){
		parent.location.reload();
	}";

$document = JFactory::getDocument();
$document->addScriptDeclaration($js);
?>