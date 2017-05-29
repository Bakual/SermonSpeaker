<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonCast
 * @author      Sven Lauch <sven@eyesup.eu>
 * @copyright   © 2016 - Sven Lauch
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

JHtml::_('bootstrap.tooltip');
JHtml::_('stylesheet', 'com_sermonspeaker/font.css', array('relative' => true));
?>
<div class="syndicate-module<?php echo $params->get('$moduleclass_sfx'); ?>">
	<?php if ($params->get('sc_introtext')) : ?>
		<p><?php echo $params->get('sc_introtext'); ?></p>
	<?php endif; ?>
	<p class="sc_subscript">
		<?php if ($params->get('sc_showpcast')) : ?>
			<?php if ($params->get('sc_otherlink')) : ?>
				<?php $link = $params->get('sc_otherlink'); ?>
			<?php else : ?>
				<?php $uri = JUri::getInstance($feedFile); ?>
				<?php $uri->setScheme($params->get('sc_pcast_prefix')); ?>
				<?php $link = $uri->toString(); ?>
			<?php endif; ?>
				<a class="btn sc-podcastLink btn-block" style="text-align: left; padding-left:10px;padding-right:10px;" href="<?php echo htmlspecialchars($link); ?>">
					<span class="spicon-sermonspeakerpodcast"> </span>
					<?php echo JText::_('MOD_SERMONCAST_SUBSCRIBE_PODCAST'); ?>
				</a>
		<?php endif; ?>
		<?php if ($params->get('sc_showplink')) : ?>
				<a class="btn sc-feedLink btn-block" style="text-align: left; padding-left:10px;padding-right:10px;" href="<?php echo $feedFile; ?>">
					<span class="spicon-sermonspeakerfeed"> </span>
					<?php echo JText::_('MOD_SERMONCAST_SUBSCRIBE_FEED'); ?>
				</a>
			</p>
		<?php endif; ?>
		<?php if ($params->get('sc_showhelp')) : ?>
			<?php $modalParams = array(); ?>
			<?php $modalParams['closeButton'] = false; ?>
			<?php $modalParams['url'] = JRoute::_('index.php?option=com_content&view=article&tmpl=component&id=' . (int) $params->get('sc_helpcontent')); ?>
			<?php $modalParams['bodyHeight'] = 70; ?>
			<?php $modalParams['modalWidth'] = 80; ?>
			<?php echo JHtml::_('bootstrap.renderModal', 'sc_modal', $modalParams); ?>
			<p>
				<a class="modal" href="#sc_modal" data-toggle="modal" >
					<?php echo JText::_('MOD_SERMONCAST_HELP'); ?>
				</a>
			</p>
		<?php endif; ?>
</div>
