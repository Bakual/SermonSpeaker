<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

JHtml::_('stylesheet', 'com_sermonspeaker/sermonspeaker.css', array('relative' => true));
$config = array('type' => JFactory::getApplication()->input->get('type', 'auto'));
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
		<?php
		echo $player->mspace;
		echo $player->script;

		if ($player->toggle) : ?>
            <div class="row">
                <div class="mx-auto btn-group">
                    <button type="button" onclick="Video()" class="btn btn-secondary" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_VIDEO'); ?>">
                        <span class="fa fa-film fa-4x"></span>
                    </button>
                    <button type="button" onclick="Audio()" class="btn btn-secondary" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_AUDIO'); ?>">
                        <span class="fa fa-music fa-4x"></span>
                    </button>
                </div>
            </div>
		<?php endif; ?>
	</div>
</div>
