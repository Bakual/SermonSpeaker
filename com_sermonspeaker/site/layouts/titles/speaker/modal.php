<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site.Layouts
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2015 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

$url = JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($displayData->speaker_slug));
?>
<div id="sermonspeaker-modal-speaker-<?php echo $displayData->speaker_id; ?>" class="modal hide fade">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">x</button>
		<h3>
			<?php echo $displayData->speaker_title; ?>
		</h3>
	</div>
	<div class="modal-body">
		<a href="<?php echo $url; ?>" itemprop="url">
			<img class="item-image pull-left" src="<?php echo SermonspeakerHelperSermonspeaker::makelink($displayData->pic); ?>" />
		</a>
		<?php if ($displayData->intro) : ?>
			<div>
				<?php echo JHtml::_('content.prepare', $displayData->intro, '', 'com_sermonspeaker.intro'); ?>
			</div>
		<?php endif;
		if ($displayData->bio) : ?>
			<div>
				<?php echo JHtml::_('content.prepare', $displayData->bio, '', 'com_sermonspeaker.bio'); ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="modal-footer">
		<a href="<?php echo $url . '#sermons'; ?>" class="btn">
			<?php echo JText::_('COM_SERMONSPEAKER_SERMONS'); ?>
		</a>
		<a href="<?php echo $url . '#series'; ?>" class="btn">
			<?php echo JText::_('COM_SERMONSPEAKER_SERIES'); ?>
		</a>
		<?php if ($displayData->website and $displayData->website != 'http://') : ?>
			<a class="btn" href="<?php echo $displayData->website; ?>">
				<?php echo JText::_('COM_SERMONSPEAKER_FIELD_WEBSITE_LABEL'); ?>
			</a>
		<?php endif; ?>
	</div>
</div>
