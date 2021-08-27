<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

/**
 * @var array                     $list
 * @var \Joomla\Registry\Registry $params
 * @var stdClass                  $module
 * @var string                    $helperMethod
 */

$i     = 0;
$count = count($list);
$id    = 'sermonspeakerCarousel' . $module->id;

HTMLHelper::_('bootstrap.carousel');
?>
<div id="<?php echo $id; ?>" class="mod-sermonspeaker carousel carousel-dark slide px-5 pb-4" data-bs-ride="carousel">
	<div class="carousel-indicators">
		<?php for ($j = 0; $j < $count; $j++): ?>
			<button type="button" data-bs-target="#<?php echo $id; ?>" data-bs-slide-to="<?php echo $j; ?>" <?php echo (!$j) ? 'class="active" aria-current="true"' : ''; ?> aria-label="Slide <?php echo $j + 1; ?>>"></button>
		<?php endfor; ?>
	</div>
	<div class="carousel-inner">
		<?php foreach ($list as $i => $item) : ?>
			<?php $link = Route::_(SermonspeakerHelperRoute::$helperMethod($item->slug, $item->catid, $item->language)); ?>
			<div class="carousel-item sermonspeaker_entry<?php echo $i; ?> item <?php echo ($i) ? '' : 'active'; ?>">
				<h4>
					<a href="<?php echo $link; ?>"><?php echo $item->title; ?></a>
				</h4>
				<div style="clear:left;"></div>
				<?php if (strlen($item->tooltip) > 0) : ?>
					<div>
						<?php echo HTMLHelper::_('content.prepare', $item->tooltip); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<button class="carousel-control-prev" type="button" data-bs-target="#<?php echo $id; ?>" data-bs-slide="prev">
		<span class="carousel-control-prev-icon" aria-hidden="true"></span>
		<span class="visually-hidden">Previous</span>
	</button>
	<button class="carousel-control-next" type="button" data-bs-target="#<?php echo $id; ?>" data-bs-slide="next">
		<span class="carousel-control-next-icon" aria-hidden="true"></span>
		<span class="visually-hidden">Next</span>
	</button>
</div>
