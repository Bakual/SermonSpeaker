<?php
/**
 * sh404SEF support for com_sermonspeaker component.
 * Author : Thomas Hunziker
 * contact : admin@sermonspeaker.net
 *
 * This is a SermonSpeaker sh404SEF native plugin file
 *
 */
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

// ------------------  standard plugin initialize function - don't change ---------------------------
global $sh_LANG;
$sefConfig = & shRouter::shGetConfig();  
$shLangName = '';
$shLangIso = '';
$title = array();
$shItemidString = '';
$dosef = shInitializePlugin($lang, $shLangName, $shLangIso, $option);
if ($dosef == false) return;
// ------------------  standard plugin initialize function - don't change ---------------------------

// ------------------  load language file - adjust as needed ----------------------------------------
//$shLangIso = shLoadPluginLanguage('com_sermonspeaker', $shLangIso, '_SEF_SERMONSPEAKER_TEXT_STRING');
// ------------------  load language file - adjust as needed ----------------------------------------

// remove common URL from GET vars list, so that they don't show up as query string in the URL
shRemoveFromGETVarsList('option');
shRemoveFromGETVarsList('lang');
if (!empty($Itemid))
	shRemoveFromGETVarsList('Itemid');
if (!empty($limit))
	shRemoveFromGETVarsList('limit');
if (isset($limitstart))
	shRemoveFromGETVarsList('limitstart'); // limitstart can be zero

// start by inserting the menu element title (just an idea, this is not required at all)
$task = isset($task) ? $task : null;
$Itemid = isset($Itemid) ? $Itemid : null;
$shSermonspeakerName = shGetComponentPrefix($option); 
$shSermonspeakerName = empty($shSermonspeakerName) ? getMenuTitle($option, $task, $Itemid, null, $shLangName) : $shSermonspeakerName;
$shSermonspeakerName = (empty($shSermonspeakerName) || $shSermonspeakerName == '/') ? 'SermonspeakerCom':$shSermonspeakerName;

switch ($task)
{
	default:
		$title[] = $sh_LANG[$shLangIso]['COM_SH404SEF_VIEW_SERMON'];
		if (!empty($id))
		{
			$q = 'SELECT id, title FROM #__sermon_sermons WHERE id = '.$database->Quote($id);
			$database->setQuery($q);

			// JoomFish stuff
			if (shTranslateUrl($option, $shLangName))
				$sermonTitle = $database->loadObject();
			else 
				$sermonTitle = $database->loadObject(false);

			if ($sermonTitle)
			{
				$title[] = $sermonTitle->title;
				shRemoveFromGETVarsList('id');
				shMustCreatePageId('set', true); // NEW: ask sh404sef to create a short URL for this SEF URL (pageId)
			}
		}
		shRemoveFromGETVarsList('task');
		break;
}

// ------------------  standard plugin finalize function - don't change ---------------------------  
if ($dosef)
{
	$string = shFinalizePlugin( $string, $title, $shAppendString, $shItemidString, 
		(isset($limit) ? @$limit : null), (isset($limitstart) ? @$limitstart : null), 
		(isset($shLangName) ? @$shLangName : null));
}
// ------------------  standard plugin finalize function - don't change ---------------------------
