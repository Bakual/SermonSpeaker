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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');

$user      = JFactory::getUser();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$archived  = $this->state->get('filter.state') == 2 ? true : false;
$trashed   = $this->state->get('filter.state') == -2 ? true : false;
$saveOrder = $listOrder == 'sermons.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_sermonspeaker&task=sermons.saveOrderAjax&tmpl=component' . Session::getFormToken() . '=1';
	JHtml::_('draggablelist.draggable');
}

$assoc = Associations::isEnabled();
?>
<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=sermons'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div id="j-sidebar-container" class="col-md-2">
			<?php echo $this->sidebar; ?>
		</div>
		<div class="col-md-10">
			<div id="j-main-container" class="j-main-container">
				<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
				<?php if (empty($this->items)) : ?>
					<div class="alert alert-warning alert-no-items">
						<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>
					<table class="table table-striped" id="sermonList">
					<thead>
						<tr>
							<th width="1%" class="nowrap text-center hidden-phone">
								<?php echo JHtml::_('searchtools.sort', '', 'sermons.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
							</th>
							<th width="1%" class="hidden-phone">
								<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
							</th>
							<th width="1%" style="min-width:40px" class="nowrap text-center">
								<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'sermons.state', $listDirn, $listOrder); ?>
							</th>
							<th>
								<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'sermons.title', $listDirn, $listOrder); ?>
							</th>
							<?php if ($assoc) : ?>
								<th width="5%" class="nowrap hidden-phone">
									<?php echo JHtml::_('searchtools.sort', 'COM_SERMONSPEAKER_HEADING_ASSOCIATION', 'association', $listDirn, $listOrder); ?>
								</th>
							<?php endif;?>
							<th width="10%" class="nowrap hidden-phone hidden-tablet">
								<?php echo JHtml::_('searchtools.sort',  'COM_SERMONSPEAKER_SPEAKER', 'speaker_title', $listDirn, $listOrder); ?>
							</th>
							<th width="10%" class="nowrap hidden-phone hidden-tablet">
								<?php echo JHtml::_('searchtools.sort',  'COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL', 'scripture', $listDirn, $listOrder); ?>
							</th>
							<th width="10%" class="nowrap hidden-phone hidden-tablet">
								<?php echo JHtml::_('searchtools.sort',  'COM_SERMONSPEAKER_SERIE', 'series_title', $listDirn, $listOrder); ?>
							</th>
							<th width="7%" class="nowrap hidden-phone">
								<?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
							</th>
							<th width="7%" class="nowrap hidden-phone">
								<?php echo JHtml::_('searchtools.sort',  'COM_SERMONSPEAKER_FIELD_DATE_LABEL', 'sermons.sermon_date', $listDirn, $listOrder); ?>
							</th>
							<th width="5%" class="nowrap hidden-phone">
								<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_HITS', 'sermons.hits', $listDirn, $listOrder); ?>
							</th>
							<th width="1%" class="nowrap hidden-phone">
								<?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ID', 'sermons.id', $listDirn, $listOrder); ?>
							</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($this->items as $i => $item) :
						$ordering   = ($listOrder == 'sermons.ordering');
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
									<?php echo JHtml::_('jgrid.published', $item->state, $i, 'sermons.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
									<?php echo JHtml::_('sermonspeakeradministrator.podcasted', $item->podcast, $i, 'sermons.podcast_', $canChange); ?>
								</div>
							</td>
							<td class="nowrap has-context">
								<div class="pull-left">
									<?php if ($item->checked_out) : ?>
										<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'sermons.', $canCheckin); ?>
									<?php endif; ?>
									<?php if ($canEdit || $canEditOwn) : ?>
										<a href="<?php echo JRoute::_('index.php?option=com_sermonspeaker&task=sermon.edit&id=' . (int) $item->id);?>">
											<?php echo $this->escape($item->title); ?>
										</a>
									<?php else : ?>
										<?php echo $this->escape($item->title); ?>
									<?php endif; ?>
									<span class="small">
										<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
									</span>
									<div class="small">
										<?php echo JText::_('JCATEGORY') . ': ' . $this->escape($item->category_title); ?>
									</div>
								</div>
							</td>
							<?php if ($assoc) : ?>
								<td class="hidden-phone">
									<?php if ($item->association) : ?>
										<?php echo JHtml::_('sermonspeakeradministrator.association', $item->id, 'sermon'); ?>
									<?php endif; ?>
								</td>
							<?php endif;?>
							<td class="nowrap small hidden-phone hidden-tablet">
								<?php echo $this->escape($item->speaker_title); ?>
							</td>
							<td class="text-center small hidden-phone hidden-tablet">
								<?php if ($item->scripture):
									$passages	= explode('!', $item->scripture);
									$separator	= JText::_('COM_SERMONSPEAKER_SCRIPTURE_SEPARATOR');
									$j = 1;
									foreach ($passages as $passage){
										$explode	= explode('|',$passage);
										if ($explode[5]){
											if ($explode[0]){
												echo $explode[5];
											} else {
												echo '<i><u>'.$explode[5].'</u></i>';
											}
										} else {
											echo JText::_('COM_SERMONSPEAKER_BOOK_'.$explode[0]);
											if ($explode[1]){
												echo '&nbsp;'.$explode[1];
												if ($explode[2]){
													echo $separator.$explode[2];
												}
												if ($explode[3] || $explode[4]){
													echo '-';
													if ($explode[3]){
														echo $explode[3];
														if ($explode[4]){
															echo $separator.$explode[4];
														}
													} else {
														echo $explode[4];
													}
												}
											}
										}
										if($j < count($passages)){
											echo '<br/ >';
										}
										$j++;
									}
								endif; ?>
							</td>
							<td class="small hidden-phone hidden-tablet">
								<?php echo $this->escape($item->series_title); ?>
							</td>
							<td class="small hidden-phone">
								<?php echo LayoutHelper::render('joomla.content.language', $item); ?>
							</td>
							<td class="nowrap small hidden-phone">
								<?php if ($item->sermon_date != '0000-00-00 00:00:00'):
									echo JHtml::date($item->sermon_date, JText::_('DATE_FORMAT_LC4'), true);
								endif; ?>
							</td>
							<td class="hidden-phone text-center">
								<span class="badge badge-info">
									<?php echo (int) $item->hits; ?>
								</span>
								<?php if ($canEdit || $canEditOwn) : ?>
									<a class="btn btn-xs btn-warning" href="index.php?option=com_sermonspeaker&task=sermon.reset&id=<?php echo $item->id; ?>">
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
