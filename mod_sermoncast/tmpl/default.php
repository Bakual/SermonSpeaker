<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonCast
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2014 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.modal');
?>
<div class="syndicate-module<?php echo $params->get('$moduleclass_sfx'); ?>">
<?php
if ($params->get('sc_introtext')) : ?>
	<p><?php echo $params->get('sc_introtext'); ?></p>
<?php endif;

if ($params->get('sc_showpcast')) :
	if($params->get('sc_otherlink')) :
		$link = $params->get('sc_otherlink');
	else:
		$u = JURI::getInstance($feedFile);
		$u->setScheme($params->get('sc_pcast_prefix'));
		$link = $u->toString();
	endif;
	$otherimage = $params->get('sc_otherimage');

	if ($otherimage) :
		$img = '<img src="' . $otherimage . '" border="0" alt="Podcast"/>';
	else:
		$img = '<img src="' . JURI::root() . 'modules/mod_sermoncast/podcast-mini.gif" border="0" alt="Podcast"/>';
	endif; ?>
	<a href="<?php echo htmlspecialchars($link); ?>"><?php echo $img; ?> </a><br />
<?php endif;

if ($params->get('sc_showplink')) : ?>
	<a href="<?php echo $feedFile; ?>"><?php echo JText::_('MOD_SERMONCAST_FULLFEED'); ?></a>
	<a href="<?php echo $feedFile; ?>"><img src="<?php echo JURI::root(); ?>modules/mod_sermoncast/feed_rss.gif" border="0" alt="rss feed" /></a><br />
<?php endif;

if ($params->get('sc_showhelp')) :
	$url = JRoute::_('index.php?option=com_content&view=article&tmpl=component&id=' . (int) $params->get('sc_helpcontent'));
	$rel = "{handler: 'iframe', size: {x: " . (int) $params->get('sc_helpwidth') . ', y: ' . (int) $params->get('sc_helpheight') . '}}'; ?>
	<p><a class="modal" href="<?php echo $url; ?>" rel="<?php echo $rel; ?>">
	<?php echo JText::_('MOD_SERMONCAST_HELP'); ?>
	</a></p>
<?php endif; ?>
</div>
