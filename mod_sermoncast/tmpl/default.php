<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonCast
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/**
 * @var \Joomla\Registry\Registry $params
 * @var stdClass                  $module
 * @var string                    $feedFile
 */
?>
<div class="syndicate-module">
	<?php if ($params->get('sc_introtext')) : ?>
		<p><?php echo $params->get('sc_introtext'); ?></p>
	<?php endif; ?>
	<?php if ($params->get('sc_showpcast')) : ?>
		<?php if ($params->get('sc_otherlink')) : ?>
			<?php $link = $params->get('sc_otherlink'); ?>
		<?php else: ?>
			<?php $link = Route::_($feedFile, true, 0, true); ?>
			<?php $uri = Uri::getInstance($link); ?>
			<?php $uri->setScheme($params->get('sc_pcast_prefix')); ?>
			<?php $link = $uri->toString(); ?>
		<?php endif; ?>
		<?php $img = $params->get('sc_otherimage'); ?>
		<?php if (!$img && ($img = $params->get('logo'))) : ?>
			<?php $img = Uri::root() . 'media/com_sermonspeaker/logo/' . $img; ?>
		<?php endif; ?>
		<?php if (!$img) : ?>
			<?php $img = Uri::root() . 'media/mod_sermoncast/images/podcast-mini.gif'; ?>
		<?php endif; ?>
		<a href="<?php echo $link; ?>">
			<img src="<?php echo $img; ?>" alt="Podcast"/>
		</a><br/>
	<?php endif; ?>
	<?php if ($params->get('sc_showplink')) : ?>
		<a href="<?php echo Route::_($feedFile); ?>"><?php echo Text::_('MOD_SERMONCAST_FULLFEED'); ?></a>
		<a href="<?php echo Route::_($feedFile); ?>"><img
					src="<?php echo Uri::root(); ?>media/mod_sermoncast/images/feed_rss.gif" border="0" alt="rss feed"/></a>
		<br/>
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
