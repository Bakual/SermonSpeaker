<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

JHtml::_('bootstrap.tooltip');

$user = JFactory::getUser();

if (!$this->item->pic) {
	$this->item->pic = 'media/com_sermonspeaker/images/' . $this->params->get('defaultpic', 'nopict.jpg');
}
?>
<div class="ss-speaker-container<?php echo htmlspecialchars($this->params->get('pageclass_sfx')); ?>" >
	<div class="<?php echo ($this->item->state) ? '' : 'system-unpublished'; ?>">
		<div class="page-header">
			<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->item->slug, $this->item->catid, $this->item->language)); ?>" target="_parent">
				<h2><?php echo $this->item->title; ?></h2>
			</a>
			<?php echo JLayoutHelper::render('blocks.state_info', array('item' => $this->item, 'show' => $user->authorise('core.edit', 'com_sermonspeaker'))); ?>
		</div>
		<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->item->slug, $this->item->catid, $this->item->language)); ?>">
			<img class="img-polaroid pull-left item-image" src="<?php echo SermonspeakerHelperSermonspeaker::makeLink($this->item->pic); ?>">
		</a>
		<?php if (in_array('speaker:intro', $this->columns) and $this->item->intro) : ?>
			<div>
				<?php echo JHtml::_('content.prepare', $this->item->intro, '', 'com_sermonspeaker.intro'); ?>
			</div>
		<?php endif;

		if (in_array('speaker:bio', $this->columns) and $this->item->bio) : ?>
			<div>
				<?php echo JHtml::_('content.prepare', $this->item->bio, '', 'com_sermonspeaker.bio'); ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="clearfix"></div>
	<?php if ($this->sermons): ?>
		<a class="badge hasTooltip" title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS_SERMONSLINK_HOOVER'); ?>" href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->item->slug, $this->item->catid, $this->item->language)); ?>" target="_parent">
			<?php echo JText::_('COM_SERMONSPEAKER_SERMONS'); ?></a>&nbsp;
	<?php endif;

	if ($this->series) : ?>
		<a class="badge hasTooltip" title="<?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS_SERIESLINK_HOOVER'); ?>" href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($this->item->slug, $this->item->catid, $this->item->language) . '#series'); ?>" target="_parent">
			<?php echo JText::_('COM_SERMONSPEAKER_SERIES'); ?></a>&nbsp;
	<?php endif;

	if ($this->item->website and $this->item->website != 'http://') : ?>
		<a class="badge" href="<?php echo $this->item->website; ?>" target="_blank">
			<?php echo JText::_('COM_SERMONSPEAKER_FIELD_WEBSITE_LABEL'); ?></a>
	<?php endif; ?>
</div>
