<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonCast
 * @author      Sven Lauch <sven@eyesup.eu>
 * @copyright   (C) 2014 - Sven Lauch
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
	<ul class="sc_subscript unstyled">
		<?php if ($params->get('sc_showpcast')) : ?>
			<?php if ($params->get('sc_otherlink')) : ?>
				<?php $link = $params->get('sc_otherlink'); ?>
			<?php else : ?>
				<?php $uri = JURI::getInstance($feedFile); ?>
				<?php $uri->setScheme($params->get('sc_pcast_prefix')); ?>
				<?php $link = $uri->toString(); ?>
			<?php endif; ?>
			<li>
				<a class="btn sc-podcastLink" href="<?php echo htmlspecialchars($link); ?>">
					<span class="spicon-sermonspeakerpodcast"> </span>
					<?php echo JText::_('MOD_SERMONCAST_SUBSCRIBE_PODCAST'); ?>
				</a>
			</li>
		<?php endif; ?>
		<?php if ($params->get('sc_showplink')) : ?>
			<li>
				<a class="btn sc-feedLink" href="<?php echo $feedFile; ?>">
					<span class="spicon-sermonspeakerfeed"> </span>
					<?php echo JText::_('MOD_SERMONCAST_SUBSCRIBE_FEED'); ?>
				</a>
			</li>
		<?php endif; ?>
		<?php if ($params->get('sc_showhelp')) : ?>
			<?php $url = JRoute::_('index.php?option=com_content&view=article&tmpl=component&id=' . (int) $params->get('sc_helpcontent')); ?>
			<?php $rel = "{handler: 'iframe', size: {x: " . (int) $params->get('sc_helpwidth') . ', y: ' . (int) $params->get('sc_helpheight') . '}}'; ?>
			<li>
				<a class="modal" href="<?php echo $url; ?>" rel="<?php echo $rel; ?>">
					<?php echo JText::_('MOD_SERMONCAST_HELP'); ?>
				</a>
			</li>
		<?php endif; ?>
	</ul>
</div>
