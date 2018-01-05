<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site.Layouts
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;

/**
 * @var  array        $displayData Contains the following items:
 * @var  object       $player      The player object
 * @var  array|object $items       An array of sermon objects or a single object
 * @var  object       $view        The calling view
 */
extract($displayData);

HTMLHelper::_('stylesheet', 'com_sermonspeaker/player.css', array('relative' => true));
?>
<div class="ss-player ss-<?php echo $view; ?>-player">
    <div class="col-10 mx-auto">
        <hr>
		<?php if (is_array($items) && empty($player->hideInfo)) : ?>
			<?php echo $this->sublayout('info', $items); ?>
		<?php endif; ?>
		<?php echo $player->mspace; ?>
		<?php echo $player->script; ?>
        <hr>
		<?php if ($player->toggle) : ?>
			<?php echo $this->sublayout('toggle', $items); ?>
		<?php endif; ?>
    </div>
</div>