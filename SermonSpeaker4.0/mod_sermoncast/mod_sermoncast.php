<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
$cat['series'] 	= (int)$params->get('sc_series_cat');
$cat['speaker']	= (int)$params->get('sc_speaker_cat');
$cat['sermon'] 	= (int)$params->get('sc_sermon_cat');
$series			= (int)$params->get('series_id');
$type			= $params->get('sc_type');
$menuitem		= (int)$params->get('sc_menuitem');
$options = '';
if ($cat['series']){
	$options .= '&series_cat='.$cat['series'];
}
if ($cat['speaker']){
	$options .= '&amp;speaker_cat='.$cat['speaker'];
}
if ($cat['sermon']){
	$options .= '&amp;sermon_cat='.$cat['sermon'];
}
if ($series){
	$options .= '&amp;series_id='.$series;
}
if ($type){
	$options .= '&amp;type='.$type;
}
if ($menuitem){
	$options .= '&amp;Itemid='.$menuitem;
}

$feedFile = JURI::root().'index.php?option=com_sermonspeaker&amp;view=feed&amp;format=raw'.$options;
?>

<div class="syndicate-module<?php echo $params->get('$moduleclass_sfx'); ?>">
<?php if ($params->get('sc_introtext')): ?>
	<p><?php echo $params->get('sc_introtext'); ?></p>
<?php endif;
if($params->get('sc_showpcast')) {
	if($params->get('sc_otherlink')) {
		$link = $params->get('sc_otherlink');
	} else {
		$u =& JURI::getInstance($feedFile);
		$u->setScheme($params->get('sc_pcast_prefix'));
		$link = $u->toString();
	}
	$otherimage = $params->get('sc_otherimage');
	if($otherimage) {
		$img = '<img src="'.$otherimage.'" border="0" alt="Podcast"/>';
	} else {
		$img = '<img src="'.JURI::root().'modules/mod_sermoncast/podcast-mini.gif" border="0" alt="Podcast"/>'; 	
	} ?>
	<a href="<?php echo htmlspecialchars($link); ?>"><?php echo $img; ?> </a><br />
<?php }
if($params->get('sc_showplink')) { ?>
	<a href="<?php echo $feedFile; ?>"><?php echo JText::_('MOD_SERMONCAST_FULLFEED'); ?></a>
	<a href="<?php echo $feedFile; ?>"><img src="<?php echo JURI::root(); ?>modules/mod_sermoncast/feed_rss.gif" border="0" alt="rss feed" /></a><br />
<?php } 
if($params->get('sc_showhelp')) { ?>
	<p><a class="modal" href="<?php echo JRoute::_('index.php?option=com_content&view=article&id='.(int)$params->get('sc_helpcontent').'&tmpl=component') ?>" rel="{handler: 'iframe', size: {x: <?php echo (int)$params->get('sc_helpwidth'); ?>, y: <?php echo (int)$params->get('sc_helpheight'); ?>}}">
	<?php echo JText::_('MOD_SERMONCAST_HELP'); ?>
	</a></p>
	<?php
} ?>
</div>