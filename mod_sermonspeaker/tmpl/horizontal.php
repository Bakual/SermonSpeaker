<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonSpeaker
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Router\Route;

/**
 * @var array                     $list
 * @var \Joomla\Registry\Registry $params
 * @var stdClass                  $module
 * @var string                    $helperMethod
 */

$i     = 0;
$count = count($list);
?>
<div class="mod-sermonspeaker">
	<div class="row">
		<?php foreach ($list as $item) : ?>
			<?php $i++; ?>
			<div class="sermonspeaker_entry<?php echo $i; ?> text-center col-md-<?php echo 12 / $count; ?>">
				<?php $link = Route::_(SermonspeakerHelperRoute::$helperMethod($item->slug, $item->catid, $item->language)); ?>
				<?php if ($item->pic) : ?>
					<a href="<?php echo $link; ?>">
						<img src="<?php echo $item->pic; ?>" class="img-thumbnail">
					</a>
				<?php endif; ?>
				<h3>
					<a href="<?php echo $link; ?>">
						<?php echo $item->title; ?>
					</a>
				</h3>
			</div>
		<?php endforeach; ?>
	</div>
</div>
