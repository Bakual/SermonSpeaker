<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
error_reporting(E_ERROR | E_WARNING | E_PARSE);

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');

$cat['series'] 	= $params->get('series_cat');
$cat['speaker']	= $params->get('speaker_cat');
$cat['sermon'] 	= $params->get('sermon_cat');
$otherlink 		= $params->get('otherlink');
$otherimage 	= $params->get('otherimage');
$text           = $params->get('introtext');
$showPcast      = $params->get('showpcast');
$showPlainlink  = $params->get('showplink');
$prefix         = $params->get('pcast_prefix');
$helpcontent	= $params->get('helpcontent');
$moduleclass_sfx 	= $params->get('$moduleclass_sfx');

$feedcat = NULL;
if ($cat['series'] != 0){
	$feedcat .= '&series_cat='.(int)$cat['series'];
}
if ($cat['speaker'] != 0){
	$feedcat .= '&speaker_cat='.(int)$cat['speaker'];
}
if ($cat['sermon'] != 0){
	$feedcat .= '&sermon_cat='.(int)$cat['sermon'];
}

$feedFile = "index.php?option=com_sermonspeaker&amp;view=feed".$feedcat;

if($showPcast) {
	$u =& JURI::getInstance(JURI::root());
	$host = $u->getHost();
	$pcast = $prefix.$host.'/'.ltrim($feedFile,'/');
} else {
	$pcast = JURI::root().$feedFile;
}
?>

<div class="syndicate <?php echo $moduleclass_sfx; ?>" align="center">
<?php
echo '<p>'.$text.'</p>';
if($otherlink != '') {
	$link = $otherlink;
} else {
	$link = $pcast;
}
if($showPcast) {
	if($otherimage != '') {
		$img = '<img src="'.$otherimage.'" border="0" alt="Podcast"/>';
	} else {
		$img = '<img src="'.JURI::root().'modules/mod_sermoncast/podcast-mini.gif" border="0" alt="Podcast"/>'; 	
	} ?>
	<a href="<?php echo $link; ?>"><?php echo $img ?> </a><br />
	<?php 
}
if($showPlainlink) { ?>
	<a href="<?php echo JURI::root().$feedFile; ?>"><?php echo JText::_('FULLFEED'); ?></a>
	<a href="<?php echo JURI::root().$feedFile; ?>"><img src="<?php echo JURI::root(); ?>modules/mod_sermoncast/feed_rss.gif" border="0" alt="rss feed" /></a><br />
<?php } 
if($params->get('showhelp') == "1") { ?>
	<p><a class="modal" href="<?php echo JRoute::_('index.php?option=com_content&view=article&id='.$helpcontent.'&tmpl=component') ?>" rel="{handler: 'iframe', size: {x: <?php echo $params->get('helpwidth'); ?>, y: <?php echo $params->get('helpheight'); ?>}}">
	<?php echo JText::_('SC_HELP'); ?>
	</a></p>
	<?php
} ?>
</div>