<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site.Layouts
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2014 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * $item    object     The sermon item
 * $params  JRegistry  The item params
 */
extract($displayData);
?>
<?php if (isset($item->speaker_state) and !$item->speaker_state) : ?>
	<span itemprop="name"><?php echo $item->speaker_title; ?></span>
<?php else :
	if ($params->get('speakerpopup', 1)) :
		$url   = JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->speaker_slug) . '&layout=popup&tmpl=component');
		$modal = ' class="modal" rel="{handler:\'iframe\', size: {x: 700, y: 500}}"';
	else :
		$url   = JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->speaker_slug));
		$modal = '';
	endif; ?>
	<a href="<?php echo $url; ?>"<?php echo $modal; ?> itemprop="url">
		<?php if ($item->pic) :
			JHtmlBootstrap::popover();
			$popover = htmlspecialchars('<img src="' . SermonspeakerHelperSermonspeaker::makeLink($item->pic) . '" />'); ?>
			<meta itemprop="image" content="<?php echo SermonspeakerHelperSermonspeaker::makeLink($item->pic, true); ?>" />
			<span class="hasPopover" data-html="true" data-placement="top" data-title="<?php echo $item->speaker_title; ?>" data-content="<?php echo $popover; ?>">
				<span itemprop="name"><?php echo $item->speaker_title; ?></span>
			</span>
		<?php else : ?>
			<span itemprop="name"><?php echo $item->speaker_title; ?></span>
		<?php endif; ?>
	</a>
<?php endif;
