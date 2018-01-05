<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site.Layouts
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
?>
<div class="row">
    <div class="mx-auto btn-group">
        <button type="button" onclick="Video()" class="btn btn-secondary"
                title="<?php echo Text::_('COM_SERMONSPEAKER_SWITCH_VIDEO'); ?>">
            <span class="fa fa-film fa-4x"></span>
        </button>
        <button type="button" onclick="Audio()" class="btn btn-secondary"
                title="<?php echo Text::_('COM_SERMONSPEAKER_SWITCH_AUDIO'); ?>">
            <span class="fa fa-music fa-4x"></span>
        </button>
    </div>
</div>
