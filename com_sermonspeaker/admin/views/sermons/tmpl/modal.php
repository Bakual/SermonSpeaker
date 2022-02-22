<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

$app = Factory::getApplication();

if ($app->isClient('site'))
{
	Session::checkToken('get') or die(Text::_('JINVALID_TOKEN'));
}

JLoader::register('SermonspeakerHelperRoute', JPATH_ROOT . '/components/com_sermonspeaker/helpers/route.php');

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

HTMLHelper::_('behavior.core');
HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');

// Load plugin language file
$jlang = Factory::getLanguage();
$jlang->load('plg_editors-xtd_sermonspeaker', JPATH_PLUGINS . '/editors-xtd/sermonspeaker');

$function  = Factory::getApplication()->input->get('function', 'jSelectSermon');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$onclick   = $this->escape($function);
$multilang = Multilanguage::isEnabled();
?>
<div class="container-popup">

	<form action="<?php echo Route::_('index.php?option=com_sermonspeaker&view=sermons&layout=modal&tmpl=component&function=' . $function . '&' . Session::getFormToken() . '=1'); ?>"
		  method="post" name="adminForm" id="adminForm">

		<div id="mode_wrapper" class="float-start hasTooltip"
			 title="<?php echo Text::_('PLG_EDITORS-XTD_SERMONSPEAKER_FIELD_MODE_DESC'); ?>">
			<select id="mode" class="form-select">
				<option value=""><?php echo Text::_('JOPTION_USE_DEFAULT'); ?></option>
				<option value="1"><?php echo Text::_('PLG_EDITORS-XTD_SERMONSPEAKER_FIELD_MODE_OPTION_LINK'); ?></option>
				<option value="2"><?php echo Text::_('PLG_EDITORS-XTD_SERMONSPEAKER_FIELD_MODE_OPTION_PLAYER'); ?></option>
				<option value="3"><?php echo Text::_('PLG_EDITORS-XTD_SERMONSPEAKER_FIELD_MODE_OPTION_MODULE'); ?></option>
			</select>
		</div>

		<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

		<?php if (empty($this->items)) : ?>
			<div class="alert alert-info">
				<span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
				<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-sm" id="sermonList">
				<thead>
					<tr>
						<th scope="col" class="w-1 text-center">
							<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'sermons.state', $listDirn, $listOrder); ?>
						</th>
						<th scope="col" class="title">
							<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'sermons.title', $listDirn, $listOrder); ?>
						</th>
						<th scope="col" class="w-10 d-none d-md-table-cell">
							<?php echo HTMLHelper::_('searchtools.sort', 'JCATEGORY', 'sermons.catid', $listDirn, $listOrder); ?>
						</th>
						<?php if ($multilang) : ?>
							<th scope="col" class="w-15">
								<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
							</th>
						<?php endif; ?>
						<th scope="col" class="w-10 d-none d-md-table-cell">
							<?php echo HTMLHelper::_('searchtools.sort', 'JDATE', 'sermons.sermon_date', $listDirn, $listOrder); ?>
						</th>
						<th scope="col" class="w-1 d-none d-md-table-cell">
							<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'sermons.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
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
							<td class="text-center">
								<span class="tbody-icon">
									<span class="<?php echo $iconStates[$this->escape($item->state)]; ?>" aria-hidden="true"></span>
								</span>
							</td>
							<th scope="row">
								<a href="#" onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>', '<?php echo $this->escape($item->catid); ?>', '<?php echo $this->escape(SermonspeakerHelperRoute::getSermonRoute($item->id)); ?>', document.getElementById('mode').value);">
									<?php echo $this->escape($item->title); ?></a>
								<span class="small break-word">
									<?php if (empty($item->note)) : ?>
										<?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
									<?php else : ?>
										<?php echo Text::sprintf('JGLOBAL_LIST_ALIAS_NOTE', $this->escape($item->alias), $this->escape($item->note)); ?>
									<?php endif; ?>
								</span>
							</th>
							<td class="small d-none d-md-table-cell">
								<?php echo $this->escape($item->category_title); ?>
							</td>
							<?php if ($multilang) : ?>
								<td class="small">
									<?php echo LayoutHelper::render('joomla.content.language', $item); ?>
								</td>
							<?php endif; ?>
							<td class="small d-none d-md-table-cell">
								<?php echo HTMLHelper::_('date', $item->sermon_date, Text::_('DATE_FORMAT_LC4')); ?>
							</td>
							<td class="small d-none d-md-table-cell">
								<?php echo (int) $item->id; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
		<?php echo $this->pagination->getListFooter(); ?>

		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="forcedLanguage" value="<?php echo $app->input->get('forcedLanguage', '', 'CMD'); ?>">
		<?php echo HTMLHelper::_('form.token'); ?>

	</form>
</div>
