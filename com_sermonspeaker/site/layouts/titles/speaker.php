<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site.Layouts
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * @var  array                    $displayData Contains the following items:
 * @var  object                   $item        The sermon item
 * @var  Joomla\Registry\Registry $params      The item params
 * @var  bool                     $legacy      Set if coming from SermonspeakerHelperSermonspeaker::SpeakerTooltip()
 *                                             Item only contains speaker_title, speaker_slug and pic in this case
 */
extract($displayData);

if (!isset($legacy))
{
	$legacy = false;
}
?>
<?php if (!$legacy and !$item->speaker_state) : ?>
	<span itemprop="name"><?php echo $item->speaker_title; ?></span>
<?php else :
	if ($params->get('speakerpopup', 1)) :
		if ($legacy) :
			// Mootools variant
			JHtml::_('behavior.modal');
			$url   = JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->speaker_slug, $item->speaker_catid, $item->speaker_language) . '&layout=popup&tmpl=component'); ?>
			<a href="<?php echo $url; ?>" class="modal" rel="{handler:'iframe', size: {x: 700, y: 500}}" itemprop="url">
		<?php else :
			echo $this->sublayout('modal', $item); ?>
			<a href="#sermonspeaker-modal-speaker-<?php echo $item->speaker_id; ?>" data-toggle="modal">
		<?php endif;
	else : ?>
		<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->speaker_slug, $item->speaker_catid, $item->speaker_language)); ?>" itemprop="url">
	<?php endif; ?>
		<?php if ($item->pic) :
			JHtmlBootstrap::popover();
			$popover = htmlspecialchars('<img src="' . SermonspeakerHelperSermonspeaker::makeLink($item->pic) . '" />'); ?>
			<meta itemprop="image" content="<?php echo SermonspeakerHelperSermonspeaker::makeLink($item->pic, true); ?>" />
			<span class="hasPopover" data-html="true" data-placement="top" data-title="<?php echo $item->speaker_title; ?>" data-content="<?php echo $popover; ?>">
				<span itemprop="name"><?php echo $item->speaker_title; ?></span></span><?php
		else : ?>
			<span itemprop="name"><?php echo $item->speaker_title; ?></span><?php
		endif; ?></a>
<?php endif;
