<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonCast
 * @author      Sven Lauch <sven@eyesup.eu>
 * @copyright   © 2020 - Sven Lauch
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('stylesheet', 'com_sermonspeaker/font.css', array('relative' => true));
?>
<div class="syndicate-module">
	<?php if ($params->get('sc_introtext')) : ?>
        <p><?php echo $params->get('sc_introtext'); ?></p>
	<?php endif; ?>
    <div class="d-grid gap-2 sc_subscript">
		<?php if ($params->get('sc_showpcast')) : ?>
			<?php if ($params->get('sc_otherlink')) : ?>
				<?php $link = $params->get('sc_otherlink'); ?>
			<?php else : ?>
				<?php $link = Route::_($feedFile, true, 0, true); ?>
				<?php $uri = Uri::getInstance($link); ?>
				<?php $uri->setScheme($params->get('sc_pcast_prefix')); ?>
				<?php $link = $uri->toString(); ?>
			<?php endif; ?>
            <a class="btn btn-primary sc-podcastLink" href="<?php echo $link; ?>">
                <span class="spicon-sermonspeakerpodcast"> </span>
				<?php echo Text::_('MOD_SERMONCAST_SUBSCRIBE_PODCAST'); ?>
            </a>
		<?php endif; ?>
		<?php if ($params->get('sc_showplink')) : ?>
            <a class="btn btn-primary sc-feedLink" href="<?php echo Route::_($feedFile); ?>">
                <span class="spicon-sermonspeakerfeed"> </span>
                <?php echo Text::_('MOD_SERMONCAST_SUBSCRIBE_FEED'); ?>
            </a>
		<?php endif; ?>
	    <?php if ($params->get('sc_showhelp')) : ?>
		    <?php $modalParams = array(); ?>
		    <?php $modalParams['closeButton'] = false; ?>
		    <?php $modalParams['url'] = Route::_('index.php?option=com_content&view=article&tmpl=component&id=' . (int) $params->get('sc_helpcontent')); ?>
		    <?php $modalParams['bodyHeight'] = 70; ?>
		    <?php $modalParams['modalWidth'] = 80; ?>
		    <?php echo HTMLHelper::_('bootstrap.renderModal', 'sc_modal', $modalParams); ?>
            <p>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-target="#sc_modal" data-bs-toggle="modal">
				    <?php echo Text::_('MOD_SERMONCAST_HELP'); ?>
                </button>
            </p>
	    <?php endif; ?>
    </div>
</div>
