<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$app = Factory::getApplication();

if ($app->isClient('site'))
{
	JSession::checkToken('get') or die(Text::_('JINVALID_TOKEN'));
}

JLoader::register('SermonspeakerHelperRoute', JPATH_ROOT . '/components/com_sermonspeaker/helpers/route.php');

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

HTMLHelper::_('behavior.core');
HTMLHelper::_('behavior.polyfill', array('event'), 'lt IE 9');
HTMLHelper::_('bootstrap.tooltip', '.hasTooltip', array('placement' => 'bottom'));

// Load plugin language file
$jlang = Factory::getLanguage();
$jlang->load('plg_editors-xtd_sermonspeaker', JPATH_PLUGINS . '/editors-xtd/sermonspeaker');

$function  = Factory::getApplication()->input->get('function', 'jSelectSermon');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<div class="container-popup">

	<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=sermons&layout=modal&tmpl=component&function=' . $function . '&' . JSession::getFormToken() . '=1'); ?>"
		  method="post" name="adminForm" id="adminForm" class="form-inline">
		<?php if ($this->state->get('filter.forcedLanguage')) : ?>
			<input type="hidden" id="mode" name="mode" value=""/>
		<?php else : ?>
			<div id="mode_wrapper" class="btn-group pull-right hasTooltip"
				 title="<?php echo Text::_('PLG_EDITORS-XTD_SERMONSPEAKER_FIELD_MODE_DESC'); ?>">
				<select name="mode" id="mode" class="input-medium">
					<option value=""><?php echo Text::_('JOPTION_USE_DEFAULT'); ?></option>
					<option value="1"><?php echo Text::_('PLG_EDITORS-XTD_SERMONSPEAKER_FIELD_MODE_OPTION_LINK'); ?></option>
					<option value="2"><?php echo Text::_('PLG_EDITORS-XTD_SERMONSPEAKER_FIELD_MODE_OPTION_PLAYER'); ?></option>
					<option value="3"><?php echo Text::_('PLG_EDITORS-XTD_SERMONSPEAKER_FIELD_MODE_OPTION_MODULE'); ?></option>
				</select>
			</div>
		<?php endif; ?>

		<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped table-condensed" id="sermonList">
				<thead>
				<tr>
					<th width="1%" class="center nowrap">
						<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'sermons.state', $listDirn, $listOrder); ?>
					</th>
					<th class="title">
						<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'sermons.title', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="nowrap hidden-phone">
						<?php echo HTMLHelper::_('searchtools.sort', 'JCATEGORY', 'sermons.catid', $listDirn, $listOrder); ?>
					</th>
					<th width="15%" class="nowrap">
						<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
					</th>
					<th width="5%" class="nowrap hidden-phone">
						<?php echo HTMLHelper::_('searchtools.sort', 'JDATE', 'sermons.sermon_date', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap hidden-phone">
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
						<td class="center">
							<span class="<?php echo $iconStates[$this->escape($item->state)]; ?>"></span>
						</td>
						<td>
							<a onclick="if (window.parent) window.parent.<?php echo $this->escape($function); ?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>', '<?php echo $this->escape($item->catid); ?>', '<?php echo $this->escape(SermonspeakerHelperRoute::getSermonRoute($item->id)); ?>', document.getElementById('mode').value);">
								<?php echo $this->escape($item->title); ?></a>
						</td>
						<td class="small hidden-phone">
							<?php echo $this->escape($item->category_title); ?>
						</td>
						<td class="small">
							<?php echo JLayoutHelper::render('joomla.content.language', $item); ?>
						</td>
						<td class="nowrap small hidden-phone">
							<?php echo HTMLHelper::_('date', $item->sermon_date, Text::_('DATE_FORMAT_LC4'), true); ?>
						</td>
						<td class="nowrap small hidden-phone">
							<?php echo (int) $item->id; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
		<?php echo $this->pagination->getListFooter(); ?>
		<div>
			<input type="hidden" name="forcedLanguage"
				   value="<?php echo $this->escape($this->state->get('filter.forcedLanguage')); ?>"/>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</div>
	</form>
</div>