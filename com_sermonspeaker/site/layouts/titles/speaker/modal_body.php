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
use Joomla\CMS\Router\Route;

/** @var stdClass $displayData */
$url = Route::_(SermonspeakerHelperRoute::getSpeakerRoute($displayData->speaker_slug, $displayData->speaker_catid, $displayData->speaker_language));
?>
<div class="p-2">
	<?php if ($displayData->pic) : ?>
        <a href="<?php echo $url; ?>" itemprop="url">
            <img class="float-left mr-2 mb-2" src="<?php echo SermonspeakerHelperSermonspeaker::makeLink($displayData->pic); ?>" />
        </a>
	<?php endif; ?>
	<?php if ($displayData->intro) : ?>
        <div>
			<?php echo HtmlHelper::_('content.prepare', $displayData->intro, '', 'com_sermonspeaker.intro'); ?>
        </div>
	<?php endif; ?>
	<?php if ($displayData->bio) : ?>
        <div>
			<?php echo HtmlHelper::_('content.prepare', $displayData->bio, '', 'com_sermonspeaker.bio'); ?>
        </div>
	<?php endif; ?>
</div>
