<?php
// no direct access
defined('_JEXEC') or die;

$app = JFactory::getApplication();
if ($app->isClient('site'))
{
	JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
}

JLoader::register('SermonspeakerHelperRoute', JPATH_ROOT . '/components/com_sermonspeaker/helpers/route.php');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::_('behavior.core');
JHtml::_('behavior.polyfill', array('event'), 'lt IE 9');
JHtml::_('bootstrap.tooltip', '.hasTooltip', array('placement' => 'bottom'));
JHtml::_('formbehavior.chosen', 'select');

$function	= JFactory::getApplication()->input->get('function', 'jSelectSerie');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<div class="container-popup">

	<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=series&layout=modal&tmpl=component&function='.$function.'&'.JSession::getFormToken().'=1');?>" method="post" name="adminForm" id="adminForm" class="form-inline">

		<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

		<table class="table table-striped table-condensed">
			<thead>
				<tr>
					<th width="1%" class="center nowrap">
						<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'sermons.state', $listDirn, $listOrder); ?>
					</th>
					<th class="title">
						<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'series.title', $listDirn, $listOrder); ?>
					</th>
					<th width="10%" class="nowrap">
						<?php echo JHtml::_('searchtools.sort', 'JCATEGORY', 'series.catid', $listDirn, $listOrder); ?>
					</th>
					<th width="15%" class="nowrap">
						<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap">
						<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'series.id', $listDirn, $listOrder); ?>
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
				0 => 'icon-unpublish',
				1 => 'icon-publish',
				2 => 'icon-archive',
			);
			?>
			<?php foreach ($this->items as $i => $item) : ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="center">
						<span class="<?php echo $iconStates[$this->escape($item->state)]; ?>"></span>
					</td>
					<td>
						<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>', '<?php echo $this->escape($item->catid); ?>', '<?php echo $this->escape(SermonspeakerHelperRoute::getSermonRoute($item->id)); ?>');">
							<?php echo $this->escape($item->title); ?></a>
					</td>
					<td class="small hidden-phone">
						<?php echo $this->escape($item->category_title); ?>
					</td>
					<td class="small">
						<?php echo JLayoutHelper::render('joomla.content.language', $item); ?>
					</td>
					<td class="nowrap small hidden-phone">
						<?php echo (int) $item->id; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<div>
			<input type="hidden" name="forcedLanguage" value="<?php echo $this->escape($this->state->get('filter.forcedLanguage')); ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>