<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

$app = Factory::getApplication();

if ($app->isClient('site'))
{
	Session::checkToken('get') or die(Text::_('JINVALID_TOKEN'));
	HTMLHelper::_('stylesheet', 'com_sermonspeaker/sermonspeaker.css', array('relative' => true));
}

JLoader::register('SermonspeakerHelperRoute', JPATH_ROOT . '/components/com_sermonspeaker/helpers/route.php');

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

HTMLHelper::_('behavior.core');
HTMLHelper::_('behavior.polyfill', array('event'), 'lt IE 9');
HTMLHelper::_('bootstrap.tooltip', '.hasTooltip', array('placement' => 'bottom'));

$function  = Factory::getApplication()->input->get('function', 'jSelectSpeaker');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<div class="container-popup">

	<form action="<?php echo Route::_('index.php?option=com_sermonspeaker&view=speakers&layout=modal&tmpl=component&function=' . $function . '&' . Session::getFormToken() . '=1'); ?>"
		  method="post" name="adminForm" id="adminForm" class="form-inline">

		<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

		<table class="table table-striped table-condensed">
			<thead>
			<tr>
				<th width="1%" class="center nowrap">
					<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'sermons.state', $listDirn, $listOrder); ?>
				</th>
				<th class="title">
					<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'speakers.title', $listDirn, $listOrder); ?>
				</th>
				<th width="10%" class="nowrap">
					<?php echo HTMLHelper::_('searchtools.sort', 'JCATEGORY', 'speakers.catid', $listDirn, $listOrder); ?>
				</th>
				<th width="15%" class="nowrap">
					<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'speakers.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php
			$iconStates = array(
				-2 => 'icon-trash',
				0  => 'icon-unpublish',
				1  => 'icon-publish',
				2  => 'icon-archive',
			);
			?>
			<?php foreach ($this->items as $i => $item) : ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="center">
						<span class="<?php echo $iconStates[$this->escape($item->state)]; ?>"></span>
					</td>
					<td>
						<a onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>', '<?php echo $this->escape($item->catid); ?>', '<?php echo $this->escape(SermonspeakerHelperRoute::getSermonRoute($item->id)); ?>');">
							<?php echo $this->escape($item->title); ?></a>
					</td>
					<td class="small hidden-phone">
						<?php echo $this->escape($item->category_title); ?>
					</td>
					<td class="small">
						<?php echo LayoutHelper::render('joomla.content.language', $item); ?>
					</td>
					<td class="nowrap small hidden-phone">
						<?php echo (int) $item->id; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<div>
			<input type="hidden" name="forcedLanguage"
				   value="<?php echo $this->escape($this->state->get('filter.forcedLanguage')); ?>"/>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</div>
	</form>
</div>