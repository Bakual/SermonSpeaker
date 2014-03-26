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
<?php if (empty($item->speaker_state)) : ?>
	<span itemprop="name"><?php echo $item->speaker_title; ?></span>
<?php else :
	if ($params->get('speakerpopup', 1)) :
		$url = JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->id) . '&layout=popup&tmpl=component'); ?>
		<a href="<?php echo $url; ?>" class="modal" rel="{handler:'iframe', size: {x: 700, y: 500}}" itemprop="url">
	<?php else : ?>
		<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->id)); ?>" itemprop="url">
	<?php endif;
	$text = '<span itemprop="name">' . $item->speaker_title . '</span>';
	if ($item->pic) : ?>
		<meta itemprop="image" content="<?php echo SermonspeakerHelperSermonspeaker::makeLink($item->pic, true); ?>" />
		<?php $tooltip = '<img src="' . SermonspeakerHelperSermonspeaker::makeLink($item->pic) . '" alt="' . $item->speaker_title . '" />';
		echo JHtml::tooltip($tooltip, $item->speaker_title, '', $text);
	else :
		echo $text;
	endif; ?></a>
<?php endif; ?>
