<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Sermonspeaker\Component\Sermonspeaker\Site\Helper\RouteHelper;

defined('_JEXEC') or die();

$mode            = ($params->get('archive_switch') == 'month');
$state           = (int) $params->get('state', 1);
$catid           = $params->get('sermon_cat');

$dateformat = $mode ? 'F, Y' : 'Y';
$url        = RouteHelper::getSermonsRoute($catid);
?>
<ul class="sermonarchive mod-list">
	<?php foreach ($list as $item) : ?>
		<?php $url = $url . '&year=' . $item->year . '&month=' . $item->month; ?>
		<?php if ($state == 2) : ?>
			<?php $url .= '&state=2'; ?>
		<?php endif; ?>
		<?php $link = Route::_($url); ?>
        <li><a href="<?php echo $link; ?>"><?php echo HTMLHelper::date($item->date, $dateformat); ?></a></li>
	<?php endforeach; ?>
</ul>
