<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site.Layouts
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die();

/** @var stdClass $displayData */
$url = Route::_(Sermonspeaker\Component\Sermonspeaker\Site\Helper\RouteHelper::getSpeakerRoute($displayData->speaker_slug, $displayData->speaker_catid, $displayData->speaker_language));
?>
<a href="<?php echo $url; ?>#sermons" class="btn btn-secondary">
    <?php echo Text::_('COM_SERMONSPEAKER_SERMONS'); ?>
</a>
<a href="<?php echo $url; ?>#series" class="btn btn-secondary">
    <?php echo Text::_('COM_SERMONSPEAKER_SERIES'); ?>
</a>
<?php if ($displayData->website and $displayData->website != 'http://') : ?>
    <a class="btn btn-secondary" href="<?php echo $displayData->website; ?>">
		<?php echo Text::_('COM_SERMONSPEAKER_FIELD_WEBSITE_LABEL'); ?>
    </a>
<?php endif; ?>
