<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2014 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

$app = JFactory::getApplication();

if ($app->isSite())
{
	JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
}

require_once JPATH_ROOT . '/components/com_sermonspeaker/helpers/route.php';

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.core');

// Load plugin language file
$jlang = JFactory::getLanguage();
$jlang->load('plg_editors-xtd_sermonspeaker', JPATH_PLUGINS . '/editors-xtd/sermonspeaker');

$function  = JFactory::getApplication()->input->get('function', 'jSelectSermon');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=sermons&layout=modal&tmpl=component&function=' . $function . '&' . JSession::getFormToken() . '=1');?>" method="post" name="adminForm" id="adminForm" class="form-inline">
	<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>
		<table class="table table-striped table-condensed" id="sermonList">
			<thead>
				<tr>
					<th class="title">
						<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'sermons.title', $listDirn, $listOrder); ?>
					</th>
					<th width="15%" class="center nowrap">
						<?php echo JHtml::_('searchtools.sort', 'JCATEGORY', 'sermons.catid', $listDirn, $listOrder); ?>
					</th>
					<th width="5%" class="center nowrap">
						<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
					</th>
					<th width="5%" class="center nowrap">
						<?php echo JHtml::_('searchtools.sort',  'JDATE', 'sermons.sermon_date', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="center nowrap">
						<?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'sermons.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td>
							<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>', '<?php echo $this->escape($item->catid); ?>', '<?php echo $this->escape(SermonspeakerHelperRoute::getSermonRoute($item->id)); ?>', document.getElementById('mode').value);">
								<?php echo $this->escape($item->title); ?></a>
						</td>
						<td class="center">
							<?php echo $this->escape($item->category_title); ?>
						</td>
						<td class="center">
							<?php if ($item->language == '*'):?>
								<?php echo JText::alt('JALL', 'language'); ?>
							<?php else:?>
								<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
							<?php endif;?>
						</td>
						<td class="center nowrap">
							<?php if ($item->sermon_date != '0000-00-00 00:00:00'):
								echo JHtml::Date($item->sermon_date, JText::_('DATE_FORMAT_LC4'), true);
							endif; ?>
						</td>
						<td class="center">
							<?php echo (int) $item->id; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
	<?php echo $this->pagination->getListFooter(); ?>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
