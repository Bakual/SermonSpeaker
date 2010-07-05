<?php
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');

$cat['series'] 	= (int)$params->get('sc_series_cat');
$cat['speaker']	= (int)$params->get('sc_speaker_cat');
$cat['sermon'] 	= (int)$params->get('sc_sermon_cat');
$otherlink 		= $params->get('otherlink');
$otherimage 	= $params->get('otherimage');
$text           = $params->get('sc_introtext');
$showPcast      = $params->get('sc_showpcast');
$showPlainlink  = $params->get('sc_showplink');
$prefix         = $params->get('pcast_prefix');
$helpcontent	= $params->get('helpcontent');
$moduleclass_sfx 	= $params->get('$moduleclass_sfx');

$feedcat = NULL;
if ($cat['series'] != 0){
	$feedcat .= '&series_cat='.$cat['series'];
}
if ($cat['speaker'] != 0){
	$feedcat .= '&speaker_cat='.$cat['speaker'];
}
if ($cat['sermon'] != 0){
	$feedcat .= '&sermon_cat='.$cat['sermon'];
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
	<a href="<?php echo JURI::root().$feedFile; ?>"><?php echo JText::_('MOD_SERMONCAST_FULLFEED'); ?></a>
	<a href="<?php echo JURI::root().$feedFile; ?>"><img src="<?php echo JURI::root(); ?>modules/mod_sermoncast/feed_rss.gif" border="0" alt="rss feed" /></a><br />
<?php } 
if($params->get('sc_showhelp') == "1") { ?>
	<p><a class="modal" href="<?php echo JRoute::_('index.php?option=com_content&view=article&id='.$helpcontent.'&tmpl=component') ?>" rel="{handler: 'iframe', size: {x: <?php echo $params->get('helpwidth'); ?>, y: <?php echo $params->get('helpheight'); ?>}}">
	<?php echo JText::_('MOD_SERMONCAST_HELP'); ?>
	</a></p>
	<?php
} ?>
</div>