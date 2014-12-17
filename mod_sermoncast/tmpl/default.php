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
	<?php if ($params->get('sc_introtext')) : ?>
		<p><?php echo $params->get('sc_introtext'); ?></p>
	<?php endif; ?>
	<?php if ($params->get('sc_showpcast')) : ?>
		<?php if ($params->get('sc_otherlink')) : ?>
			<?php $link = $params->get('sc_otherlink'); ?>
		<?php else: ?>
			<?php $uri = JURI::getInstance($feedFile); ?>
			<?php $uri->setScheme($params->get('sc_pcast_prefix')); ?>
			<?php $link = $uri->toString(); ?>
		<?php endif; ?>
		<?php $img = $params->get('sc_otherimage'); ?>
		<?php if (!$img) : ?>
			<?php if ($img = $params->get('logo')): ?>
				<?php $img = JURI::root() . 'media/com_sermonspeaker/logo/' . $img; ?>
			<?php endif; ?>
		<?php endif; ?>
		<?php if (!$img) : ?>
			<?php $img = JURI::root() . 'modules/mod_sermoncast/podcast-mini.gif'; ?>
		<?php endif; ?>
		<a href="<?php echo htmlspecialchars($link); ?>">
			<img src="<?php echo $img; ?>" border="0" alt="Podcast" />
		</a><br />
	<?php endif; ?>
	<?php if ($params->get('sc_showplink')) : ?>
		<a href="<?php echo $feedFile; ?>"><?php echo JText::_('MOD_SERMONCAST_FULLFEED'); ?></a>
		<a href="<?php echo $feedFile; ?>"><img src="<?php echo JURI::root(); ?>modules/mod_sermoncast/feed_rss.gif" border="0" alt="rss feed" /></a><br />
	<?php endif; ?>
	<?php if ($params->get('sc_showhelp')) : ?>
		<?php $url = JRoute::_('index.php?option=com_content&view=article&tmpl=component&id=' . (int) $params->get('sc_helpcontent')); ?>
		<?php $rel = "{handler: 'iframe', size: {x: " . (int) $params->get('sc_helpwidth') . ', y: ' . (int) $params->get('sc_helpheight') . '}}'; ?>
		<p><a class="modal" href="<?php echo $url; ?>" rel="<?php echo $rel; ?>">
			<?php echo JText::_('MOD_SERMONCAST_HELP'); ?>
		</a></p>
	<?php endif; ?>
</div>
