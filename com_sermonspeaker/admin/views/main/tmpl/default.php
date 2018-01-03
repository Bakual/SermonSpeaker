<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

?>
<div class="row-fluid">
    <div id="j-main-container">
        <div class="card-deck m-3">
            <a class="card text-center" href="index.php?option=com_sermonspeaker&view=sermons">
                <div class="card-body">
                    <span class="fa fa-list-alt fa-4x"></span>
                    <h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_SERMONS_TITLE'); ?></h3>
                </div>
            </a>
            <a class="card text-center" href="index.php?option=com_sermonspeaker&view=series">
                <div class="card-body">
                    <span class="fa fa-object-group fa-4x"></span>
                    <h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_SERIES_TITLE'); ?></h3>
                </div>
            </a>
            <a class="card text-center" href="index.php?option=com_sermonspeaker&view=speakers">
                <div class="card-body">
                    <span class="fa fa-commenting-o fa-4x"></span>
                    <h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_SPEAKERS_TITLE'); ?></h3>
                </div>
            </a>
        </div>
        <div class="card-deck m-3">
            <a class="card text-center" href="index.php?option=com_categories&extension=com_sermonspeaker.sermons">
                <div class="card-body">
                    <span class="fa-stack fa-2x">
                        <i class="fa fa-folder-o fa-stack-2x"></i>
                        <i class="fa fa-list-alt fa-stack-1x"></i>
                    </span>
                    <h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_MENU_CATEGORIES_SERMONS'); ?></h3>
                </div>
            </a>
            <a class="card text-center" href="index.php?option=com_categories&extension=com_sermonspeaker.series">
                <div class="card-body">
                    <span class="fa-stack fa-2x">
                        <i class="fa fa-folder-o fa-stack-2x fa-2x"></i>
                        <i class="fa fa-object-group fa-stack-1x"></i>
                    </span>
                    <h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_MENU_CATEGORIES_SERIES'); ?></h3>
                </div>
            </a>
            <a class="card text-center" href="index.php?option=com_categories&extension=com_sermonspeaker.speakers">
                <div class="card-body">
                    <span class="fa-stack fa-2x">
                        <i class="fa fa-folder-o fa-stack-2x"></i>
                        <i class="fa fa-commenting-o fa-stack-1x"></i>
                    </span>
                    <h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_MENU_CATEGORIES_SPEAKERS'); ?></h3>
                </div>
            </a>
        </div>
        <div class="card-deck m-3">
            <a class="card text-center" href="index.php?option=com_sermonspeaker&view=tools">
                <div class="card-body">
                    <span class="fa fa-wrench fa-4x"></span>
                    <h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_MAIN_TOOLS'); ?></h3>
                </div>
            </a>
            <a class="card text-center" href="index.php?option=com_sermonspeaker&view=languages">
                <div class="card-body">
                    <span class="fa fa-language fa-4x"></span>
                    <h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_MAIN_LANGUAGES'); ?></h3>
                </div>
            </a>
            <a class="card text-center" href="index.php?option=com_sermonspeaker&view=help">
                <div class="card-body">
                    <span class="fa fa-question-circle fa-4x"></span>
                    <h3 class="card-title"><?php echo Text::_('COM_SERMONSPEAKER_MENU_HELP'); ?></h3>
                </div>
            </a>
        </div>
    </div>
</div>