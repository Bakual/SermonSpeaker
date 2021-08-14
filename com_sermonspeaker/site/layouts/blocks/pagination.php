<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site.Layouts
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

/**
 * @var  array                    $displayData Contains the following items:
 * @var  string                   $view        The view name
 * @var  object                   $pagination  The pagination object
 * @var  Joomla\Registry\Registry $params      The params
 */
extract($displayData);
?>
<?php if ($params->get('show_pagination') and ($pagination->pagesTotal > 1)) : ?>
	<div class="com-sermonspeaker-<?php echo $view; ?>__navigation w-100">
		<?php if ($params->get('show_pagination_results', 1)) : ?>
			<p class="com-sermonspeaker-<?php echo $view; ?>__counter counter float-end pt-3 pe-2">
				<?php echo $pagination->getPagesCounter(); ?>
			</p>
		<?php endif; ?>
		<div class="com-sermonspeaker-<?php echo $view; ?>__pagination"
			<?php echo $pagination->getPagesLinks(); ?>
		</div>
	</div>
<?php endif; ?>
