<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
error_reporting(E_ERROR | E_WARNING | E_PARSE);

global $cur_template;

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');

$feedcat 		= $params->get('feedcat');
$otherlink 		= $params->get('otherlink');
$otherimage 	= $params->get('otherimage');
$text           = $params->get('introtext');
$showPcast      = $params->get('showpcast');
$showPlainlink  = $params->get('showplink');
$prefix         = $params->get('pcast_prefix');
$helpcontent	= $params->get('helpcontent');
$moduleclass_sfx 	= $params->get('$moduleclass_sfx');

$t_path = JURI::root().'templates'.DS.$cur_template.DS.'images'.DS;
$d_path	= JURI::root().'images'.DS.'M_images'.DS;

$cat = NULL;
if ($feedcat){ $cat = '&cat='.(int)$feedcat; }

$feedFile = "index.php?option=com_sermonspeaker&view=feed&feed=RSS2.0&tmpl=component".$cat;

if($showPcast) {
	$u =& JURI::getInstance(JURI::root());
	$host = $u->getHost();
	$pcast = $prefix.$host.'/'.ltrim($feedFile,'/');
} else {
	$pcast = JURI::root().$feedFile;
}
?>

<div class="syndicate<?php echo $moduleclass_sfx; ?>" align="center">
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