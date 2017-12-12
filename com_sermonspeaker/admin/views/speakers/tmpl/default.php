<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Language\Associations;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');

$user      = JFactory::getUser();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$archived  = $this->state->get('filter.state') == 2 ? true : false;
$trashed   = $this->state->get('filter.state') == -2 ? true : false;
$saveOrder = $listOrder == 'speakers.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_sermonspeaker&task=speakers.saveOrderAjax&tmpl=component' . Session::getFormToken() . '=1';
}

$assoc = Associations::isEnabled();
?>
<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=speakers'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div id="j-sidebar-container" class="col-md-2">
			<?php echo $this->sidebar; ?>
		</div>
		<div class="col-md-10">
			<div id="j-main-container" class="j-main-container">
		<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
				<?php if (empty($this->items)) : ?>
					<div class="alert alert-no-items">
						<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>
					<table class="table table-striped" id="speakerList">
						<thead>
						<tr>
							<th width="1%" class="nowrap text-center hidden-phone">
								<?php echo JHtml::_('searchtools.sort', '', 'speakers.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
							</th>
							<th width="1%" class="hidden-phone">
								<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
							</th>
							<th width="1%" style="min-width:40px" class="nowrap text-center">
								<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'speakers.state', $listDirn, $listOrder); ?>
							</th>
							<th>
								<?php echo JHtml::_('searchtools.sort', 'COM_SERMONSPEAKER_FIELD_NAME_LABEL', 'speakers.title', $listDirn, $listOrder); ?>
							</th>
							<?php if ($assoc) : ?>
								<th width="5%" class="nowrap hidden-phone">
									<?php echo JHtml::_('searchtools.sort', 'COM_SERMONSPEAKER_HEADING_ASSOCIATION', 'association', $listDirn, $listOrder); ?>
								</th>
							<?php endif;?>
							<th width="5%" class="hidden-phone">
								<?php echo JHtml::_('searchtools.sort',  'COM_SERMONSPEAKER_FIELD_PICTURE_LABEL', 'speakers.pic', $listDirn, $listOrder); ?>
							</th>
							<th width="7%" class="nowrap hidden-phone">
								<?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
							</th>
							<th width="5%" class="nowrap hidden-phone">
								<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_HITS', 'speakers.hits', $listDirn, $listOrder); ?>
							</th>
							<th width="1%" class="nowrap hidden-phone">
								<?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ID', 'speakers.id', $listDirn, $listOrder); ?>
							</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach ($this->items as $i => $item) :
							$ordering   = ($listOrder == 'speakers.ordering');
							$canEdit    = $user->authorise('core.edit', 'com_sermonspeaker.category.'.$item->catid);
							$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->id || $item->checked_out == 0;
							$canEditOwn = $user->authorise('core.edit.own', 'com_sermonspeaker.category.'.$item->catid) && $item->created_by == $user->id;
							$canChange  = $user->authorise('core.edit.state', 'com_sermonspeaker.category.'.$item->catid) && $canCheckin;
							?>
							<tr class="row<?php echo $i % 2; ?>" data-dragable-group="<?php echo $item->catid; ?>">
								<td class="order nowrap text-center hidden-sm-down">
									<?php
									$iconClass = '';
									if (!$canChange)
									{
										$iconClass = ' inactive';
									}
									elseif (!$saveOrder)
									{
										$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::_('tooltipText', 'JORDERINGDISABLED');
									}
									?>
									<span class="sortable-handler<?php echo $iconClass ?>">
										<span class="icon-menu" aria-hidden="true"></span>
									</span>
									<?php if ($canChange && $saveOrder) : ?>
										<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order ">
									<?php endif; ?>
								</td>
								<td class="text-center">
									<?php echo JHtml::_('grid.id', $i, $item->id); ?>
								</td>
								<td class="text-center">
									<div class="btn-group">
										<?php echo JHtml::_('jgrid.published', $item->state, $i, 'speakers.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
										<?php echo JHtml::_('jgrid.isdefault', $item->home, $i, 'speakers.', $canChange);?>
									</div>
								</td>
								<td class="nowrap has-context">
									<div class="pull-left">
										<?php if ($item->checked_out) : ?>
											<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'speakers.', $canCheckin); ?>
										<?php endif; ?>
										<?php if ($canEdit || $canEditOwn) : ?>
											<a href="<?php echo JRoute::_('index.php?option=com_sermonspeaker&task=speaker.edit&id=' . $item->id);?>">
												<?php echo $this->escape($item->title); ?>
											</a>
										<?php else : ?>
											<?php echo $this->escape($item->title); ?>
										<?php endif; ?>
										<span class="small">
									<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
								</span>
										<div class="small">
											<?php echo JText::_('JCATEGORY') . ": " . $this->escape($item->category_title); ?>
										</div>
									</div>
									<div class="pull-left">
								</td>
								<?php if ($assoc) : ?>
									<td class="hidden-phone">
										<?php if ($item->association) : ?>
											<?php echo JHtml::_('sermonspeakeradministrator.association', $item->id, 'speaker'); ?>
										<?php endif; ?>
									</td>
								<?php endif;?>
								<td class="text-center small hidden-phone">
									<?php if (!$item->pic){
										$item->pic = Uri::root().'media/com_sermonspeaker/images/'.$this->state->get('params')->get('defaultpic', 'nopict.jpg');
									}
									if (!parse_url($item->pic, PHP_URL_SCHEME)) {
										$item->pic = Uri::root().trim($item->pic, '/.');
									} ?>
									<img src="<?php echo $item->pic; ?>" border="1" width="50" height="50">
								</td>
								<td class="small hidden-phone">
									<?php echo LayoutHelper::render('joomla.content.language', $item); ?>
								</td>
								<td class="hidden-phone text-center">
							<span class="badge badge-info">
								<?php echo (int) $item->hits; ?>
							</span>
									<?php if ($canEdit || $canEditOwn) : ?>
										<a class="btn btn-xs btn-warning" href="index.php?option=com_sermonspeaker&task=speaker.reset&id=<?php echo $item->id; ?>">
											<i class="icon-loop" rel="tooltip" title="<?php echo JText::_('JSEARCH_RESET'); ?>"> </i>
										</a>
									<?php endif; ?>
								</td>
								<td class="text-center hidden-phone">
									<?php echo (int) $item->id; ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
				<?php echo $this->pagination->getListFooter(); ?>
				<?php //Load the batch processing form. ?>
				<?php if ($user->authorise('core.create', 'com_sermonspeaker')
					&& $user->authorise('core.edit', 'com_sermonspeaker')
					&& $user->authorise('core.edit.state', 'com_sermonspeaker')) : ?>
					<?php echo JHtml::_(
						'bootstrap.renderModal',
						'collapseModal',
						array(
							'title' => JText::_('COM_SERMONSPEAKER_BATCH_OPTIONS'),
							'footer' => $this->loadTemplate('batch_footer')
						),
						$this->loadTemplate('batch_body')
					); ?>
				<?php endif; ?>

				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>
