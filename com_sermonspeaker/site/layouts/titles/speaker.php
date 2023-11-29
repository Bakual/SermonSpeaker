<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site.Layouts
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

/**
 * @var  array                    $displayData Contains the following items:
 * @var  object                   $item        The sermon item
 * @var  Joomla\Registry\Registry $params      The item params
 */
extract($displayData);
?>
<?php if (!$item->speaker_state) : ?>
	<span itemprop="name"><?php echo $item->speaker_title; ?></span>
<?php else : ?>
	<?php if ($params->get('speakerpopup', 1)) : ?>
		<?php echo HTMLHelper::_(
				'bootstrap.renderModal',
				'sermonspeaker-modal-speaker-' . $item->speaker_id,
				array(
						'title'  => $item->speaker_title,
						'footer' => $this->sublayout('modal_footer', $item),
				),
				$this->sublayout('modal_body', $item)
		); ?>
		<a href="#sermonspeaker-modal-speaker-<?php echo $item->speaker_id; ?>" data-bs-toggle="modal">
	<?php else : ?>
		<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSpeakerRoute($item->speaker_slug, $item->speaker_catid, $item->speaker_language)); ?>" itemprop="url">
	<?php endif; ?>
	<?php if ($item->pic) : ?>
		<?php HTMLHelper::_('bootstrap.popover', '.speakerPopover', ['trigger' => 'hover focus']); ?>
		<meta itemprop="image" content="<?php echo SermonspeakerHelperSermonspeaker::makeLink($item->pic, true); ?>"/>
		<span class="speakerPopover" title="<?php echo $item->speaker_title; ?>"
			  data-bs-content="<?php echo htmlspecialchars('<img src="' . SermonspeakerHelperSermonspeaker::makeLink($item->pic) . '" />'); ?>">
			<span itemprop="name"><?php echo $item->speaker_title; ?></span></span></a>
	<?php else : ?>
		<span itemprop="name"><?php echo $item->speaker_title; ?></span></a>
	<?php endif; ?>
<?php endif;
