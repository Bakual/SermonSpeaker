<?php
// no direct access
defined('_JEXEC') or die;

$app = JFactory::getApplication();
if ($app->isSite())
{
	JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
}

require_once JPATH_ROOT.'/components/com_sermonspeaker/helpers/route.php';

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('bootstrap.tooltip');

// Load plugin language file
$jlang = JFactory::getLanguage();
$jlang->load('plg_editors-xtd_sermonspeaker', JPATH_PLUGINS.'/editors-xtd/sermonspeaker');

$function	= JFactory::getApplication()->input->get('function', 'jSelectSermon');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=sermons&layout=modal&tmpl=component&function='.$function.'&'.JSession::getFormToken().'=1');?>" method="post" name="adminForm" id="adminForm" class="form-inline">
	<fieldset class="filter clearfix">
		<div class="btn-toolbar">
			<div class="btn-group pull-left">
				<label for="filter_search">
					<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>
				</label>
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" size="30" title="<?php echo JText::_('COM_SERMONSPEAKER_FILTER_SEARCH_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" data-placement="bottom" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
					<i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip" data-placement="bottom" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();">
					<i class="icon-remove"></i></button>
			</div>
			<div class="btn-group pull-right">
				<label for="mode" class="hasTip" title="<?php echo JText::_('PLG_EDITORS-XTD_SERMONSPEAKER_FIELD_MODE_LABEL').'::'.JText::_('PLG_EDITORS-XTD_SERMONSPEAKER_FIELD_MODE_DESC'); ?>">
					<?php echo JText::_('PLG_EDITORS-XTD_SERMONSPEAKER_FIELD_MODE_LABEL'); ?>:
				</label>
				<select name="mode" id="mode" class="input-medium">
					<option value=""><?php echo JText::_('JOPTION_USE_DEFAULT'); ?></option>
					<option value="1"><?php echo JText::_('PLG_EDITORS-XTD_SERMONSPEAKER_FIELD_MODE_OPTION_LINK'); ?></option>
					<option value="2"><?php echo JText::_('PLG_EDITORS-XTD_SERMONSPEAKER_FIELD_MODE_OPTION_PLAYER'); ?></option>
					<option value="3"><?php echo JText::_('PLG_EDITORS-XTD_SERMONSPEAKER_FIELD_MODE_OPTION_MODULE'); ?></option>
				</select>
			</div>
			<div class="clearfix"></div>
		</div>
		<hr class="hr-condensed" />
		<div class="filters">
			<select name="filter_published" class="input-medium" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true);?>
			</select>

			<?php if ($this->state->get('filter.forcedLanguage')) : ?>
				<select name="filter_category_id" class="input-medium" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
					<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_sermonspeaker', array('filter.language' => array('*', $this->state->get('filter.forcedLanguage')))), 'value', 'text', $this->state->get('filter.category_id'));?>
				</select>
				<input type="hidden" name="forcedLanguage" value="<?php echo $this->escape($this->state->get('filter.forcedLanguage')); ?>" />
				<input type="hidden" name="filter_language" value="<?php echo $this->escape($this->state->get('filter.language')); ?>" />
			<?php else : ?>
				<select name="filter_category_id" class="input-medium" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
					<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_sermonspeaker'), 'value', 'text', $this->state->get('filter.category_id'));?>
				</select>
				<select name="filter_language" class="input-medium" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
					<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
				</select>
			<?php endif; ?>
		</div>
	</fieldset>

	<table class="table table-striped table-condensed">
		<thead>
			<tr>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'sermons.sermon_title', $listDirn, $listOrder); ?>
				</th>
				<th width="15%" class="center nowrap">
					<?php echo JHtml::_('grid.sort', 'JCATEGORY', 'sermons.catid', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" class="center nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" class="center nowrap">
					<?php echo JHtml::_('grid.sort',  'JDATE', 'sermons.sermon_date', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="center nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'sermons.id', $listDirn, $listOrder); ?>
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
		<?php foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->sermon_title)); ?>', '<?php echo $this->escape($item->catid); ?>', '<?php echo $this->escape(SermonspeakerHelperRoute::getSermonRoute($item->id)); ?>', document.getElementById('mode').value);">
						<?php echo $this->escape($item->sermon_title); ?></a>
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
						echo JHTML::Date($item->sermon_date, JText::_('DATE_FORMAT_LC4'), true);
					endif; ?>
				</td>
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
