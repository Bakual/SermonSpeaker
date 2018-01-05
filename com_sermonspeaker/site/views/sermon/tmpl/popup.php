<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('stylesheet', 'com_sermonspeaker/sermonspeaker.css', array('relative' => true));
$config = array('type' => Factory::getApplication()->input->get('type', 'auto'));
$player = SermonspeakerHelperSermonspeaker::getPlayer($this->item, $config);
?>
<script type="text/javascript">
    window.onload = applyChanges();
    function applyChanges() {
        window.resizeTo(<?php echo $player->popup['width'] . ', ' . $player->popup['height']; ?>);
        document.body.style.backgroundColor = '<?php echo $this->params->get('popup_color', '#fff'); ?>';
    }
</script>
<div class="ss-sermon-container<?php echo $this->pageclass_sfx; ?>">
	<div class="popup">
		<h2><?php echo $this->item->title; ?></h2>
		<?php echo LayoutHelper::render('plugin.player', array('player' => $player, 'items' => $this->item, 'view' => 'sermon')); ?>
	</div>
</div>
