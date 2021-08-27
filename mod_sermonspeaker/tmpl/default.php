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

if ($params->get('tooltip'))
{
	HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');
}

$level = 1;
?>
<ul class="mod-sermonspeaker mod-list">
	<?php foreach ($list as $item) : ?>
		<?php if ($item->level > $level) : ?>
			<ul>
		<?php elseif ($item->level < $level) : ?>
			<?php while ($item->level < $level--) : ?>
				</ul>
			<?php endwhile; ?>
		<?php endif; ?>
		<?php $level = $item->level; ?>
		<?php $link = Route::_(SermonspeakerHelperRoute::$helperMethod($item->slug, $item->catid, $item->language)); ?>
		<li>
			<?php if ($params->get('tooltip')) : ?>
				<?php $options = array('title' => $item->title, 'href' => $link, 'text' => $item->title); ?>
				<?php $tip = $item->tooltip;

				if ($item->pic) :
					$pic = $item->pic;

					if (strpos($pic, 'http://') !== 0) :
						$pic = JUri::root() . trim($pic, ' /');
					endif;
					$tip = '<div class="clearfix"><img src="' . $pic . '" alt="" class="pull-right img-rounded">' . $tip . '</div>';
				endif;
				echo HTMLHelper::tooltip($tip, $options);
			else : ?>
				<a href="<?php echo $link ?>">
					<?php echo $item->title; ?>
				</a>
			<?php endif; ?>
		</li>
	<?php endforeach;

	while ($level-- > 1) : ?>
</ul>
<?php endwhile; ?>
</ul>
