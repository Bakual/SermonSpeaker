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
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

HTMLHelper::_('behavior.multiselect');

$user      = Factory::getUser();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$archived  = $this->state->get('filter.state') == 2;
$trashed   = $this->state->get('filter.state') == -2;
$saveOrder = $listOrder == 'sermons.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_sermonspeaker&task=sermons.saveOrderAjax&tmpl=component' . Session::getFormToken() . '=1';
	HTMLHelper::_('draggablelist.draggable');
}

$assoc = Associations::isEnabled();
?>
<form action="<?php echo Route::_('index.php?option=com_sermonspeaker&view=sermons'); ?>" method="post"
	  name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
				<?php if (empty($this->items)) : ?>
					<div class="alert alert-info">
						<span class="fas fa-info-circle" aria-hidden="true"></span><span
								class="sr-only"><?php echo Text::_('INFO'); ?></span>
						<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>
					<table class="table" id="sermonList">
						<caption id="captionTable" class="sr-only">
							<?php echo Text::_('COM_SERMONSPEAKER_SERMONS_TABLE_CAPTION'); ?>
							, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
						</caption>
						<thead>
						<tr>
							<td style="width:1%" class="text-center">
								<?php echo HTMLHelper::_('grid.checkall'); ?>
							</td>
							<th scope="col" style="width:1%" class="text-center d-none d-md-table-cell">
								<?php echo HTMLHelper::_('searchtools.sort', '', 'sermons.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
							</th>
							<th scope="col" style="width:1%" class="text-center">
								<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'sermons.state', $listDirn, $listOrder); ?>
							</th>
							<th scope="col" style="width:1%" class="text-center d-none d-md-table-cell">
								<?php echo HTMLHelper::_('searchtools.sort', 'COM_SERMONSPEAKER_SERMONCAST', 'sermons.podcast', $listDirn, $listOrder); ?>
							</th>
							<th scope="col" style="min-width:100px">
								<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'sermons.title', $listDirn, $listOrder); ?>
							</th>
							<?php if ($assoc) : ?>
								<th scope="col" style="width:5%" class="d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_SERMONSPEAKER_HEADING_ASSOCIATION', 'association', $listDirn, $listOrder); ?>
								</th>
							<?php endif; ?>
							<th scope="col" style="width:10%" class="d-none d-md-table-cell">
								<?php echo HTMLHelper::_('searchtools.sort', 'COM_SERMONSPEAKER_SPEAKER', 'speaker_title', $listDirn, $listOrder); ?>
							</th>
							<th scope="col" style="width:10%" class="d-none d-md-table-cell">
								<?php echo HTMLHelper::_('searchtools.sort', 'COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL', 'scripture', $listDirn, $listOrder); ?>
							</th>
							<th scope="col" style="width:10%" class="d-none d-md-table-cell">
								<?php echo HTMLHelper::_('searchtools.sort', 'COM_SERMONSPEAKER_SERIE', 'series_title', $listDirn, $listOrder); ?>
							</th>
							<?php if (Multilanguage::isEnabled()) : ?>
								<th scope="col" style="width:10%" class="d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
								</th>
							<?php endif; ?>
							<th scope="col" style="width:10%" class="d-none d-md-table-cell">
								<?php echo HTMLHelper::_('searchtools.sort', 'COM_SERMONSPEAKER_FIELD_DATE_LABEL', 'sermons.sermon_date', $listDirn, $listOrder); ?>
							</th>
							<th scope="col" style="width:7%" class="d-none d-lg-table-cell text-center">
								<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_HITS', 'sermons.hits', $listDirn, $listOrder); ?>
							</th>
							<th scope="col" style="width:3%" class="d-none d-lg-table-cell">
								<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'sermons.id', $listDirn, $listOrder); ?>
							</th>
						</tr>
						</thead>
						<tbody <?php if ($saveOrder) : ?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php endif; ?>>
						<?php foreach ($this->items as $i => $item) :
							$ordering = ($listOrder == 'sermons.ordering');
							$canEdit = $user->authorise('core.edit', 'com_sermonspeaker.category.' . $item->catid);
							$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->id || $item->checked_out == 0;
							$canEditOwn = $user->authorise('core.edit.own', 'com_sermonspeaker.category.' . $item->catid) && $item->created_by == $user->id;
							$canChange = $user->authorise('core.edit.state', 'com_sermonspeaker.category.' . $item->catid) && $canCheckin;
							$canEditCat = $user->authorise('core.edit', 'com_sermonspeaker.category.' . $item->catid);
							$canEditOwnCat = $user->authorise('core.edit.own', 'com_sermonspeaker.category.' . $item->catid) && $item->category_uid == $user->id;
							$canEditParCat = $user->authorise('core.edit', 'com_sermonspeaker.category.' . $item->parent_category_id);
							$canEditOwnParCat = $user->authorise('core.edit.own', 'com_sermonspeaker.category.' . $item->parent_category_id) && $item->parent_category_uid == $user->id;
							?>
							<tr class="row<?php echo $i % 2; ?>" data-dragable-group="<?php echo $item->catid; ?>">
								<td class="text-center">
									<?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->title); ?>
								</td>
								<td class="text-center d-none d-md-table-cell">
									<?php
									$iconClass = '';
									if (!$canChange)
									{
										$iconClass = ' inactive';
									}
									elseif (!$saveOrder)
									{
										$iconClass = ' inactive" title="' . Text::_('JORDERINGDISABLED');
									}
									?>
									<span class="sortable-handler<?php echo $iconClass ?>">
											<span class="fas fa-ellipsis-v" aria-hidden="true"></span>
										</span>
									<?php if ($canChange && $saveOrder) : ?>
										<input type="text" name="order[]" size="5"
											   value="<?php echo $item->ordering; ?>"
											   class="width-20 text-area-order hidden">
									<?php endif; ?>
								</td>
								<td class="text-center">
									<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'sermons.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
								</td>
								<td class="text-center d-none d-md-table-cell">
									<?php echo HTMLHelper::_('sermonspeakeradministrator.podcasted', $item->podcast, $i, 'sermons.podcast_', $canChange); ?>
								</td>
								<td class="nowrap has-context">
									<div class="pull-left">
										<?php if ($item->checked_out) : ?>
											<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'sermons.', $canCheckin); ?>
										<?php endif; ?>
										<?php if ($canEdit || $canEditOwn) : ?>
											<a href="<?php echo Route::_('index.php?option=com_sermonspeaker&task=sermon.edit&id='
												. (int) $item->id); ?>"
											   title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape(addslashes($item->title)); ?>">
												<?php echo $this->escape($item->title); ?>
											</a>
										<?php else : ?>
											<?php echo $this->escape($item->title); ?>
										<?php endif; ?>
										<span class="small break-word">
												<?php echo Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?>
											</span>
										<div class="small">
											<?php
											$ParentCatUrl  = Route::_('index.php?option=com_categories&task=category.edit&id=' . $item->parent_category_id . '&extension=com_sermonspeaker.sermons');
											$CurrentCatUrl = Route::_('index.php?option=com_categories&task=category.edit&id=' . $item->catid . '&extension=com_sermonspeaker.sermons');
											$EditCatTxt    = Text::_('COM_SERMONSPEAKER_EDIT_CATEGORY');
											echo Text::_('JCATEGORY') . ': ';
											if ($item->category_level != '1') :
												if ($item->parent_category_level != '1') :
													echo ' &#187; ';
												endif;
											endif;
											if (Factory::getLanguage()->isRtl())
											{
												if ($canEditCat || $canEditOwnCat) :
													echo '<a href="' . $CurrentCatUrl . '" title="' . $EditCatTxt . '">';
												endif;
												echo $this->escape($item->category_title);
												if ($canEditCat || $canEditOwnCat) :
													echo '</a>';
												endif;
												if ($item->category_level != '1') :
													echo ' &#171; ';
													if ($canEditParCat || $canEditOwnParCat) :
														echo '<a href="' . $ParentCatUrl . '" title="' . $EditCatTxt . '">';
													endif;
													echo $this->escape($item->parent_category_title);
													if ($canEditParCat || $canEditOwnParCat) :
														echo '</a>';
													endif;
												endif;
											}
											else
											{
												if ($item->category_level != '1') :
													if ($canEditParCat || $canEditOwnParCat) :
														echo '<a href="' . $ParentCatUrl . '" title="' . $EditCatTxt . '">';
													endif;
													echo $this->escape($item->parent_category_title);
													if ($canEditParCat || $canEditOwnParCat) :
														echo '</a>';
													endif;
													echo ' &#187; ';
												endif;
												if ($canEditCat || $canEditOwnCat) :
													echo '<a href="' . $CurrentCatUrl . '" title="' . $EditCatTxt . '">';
												endif;
												echo $this->escape($item->category_title);
												if ($canEditCat || $canEditOwnCat) :
													echo '</a>';
												endif;
											}
											?>
										</div>
									</div>
								</td>
								<?php if ($assoc) : ?>
									<td class="d-none d-md-table-cell">
										<?php if ($item->association) : ?>
											<?php echo HTMLHelper::_('sermonspeakeradministrator.association', $item->id, 'sermon'); ?>
										<?php endif; ?>
									</td>
								<?php endif; ?>
								<td class="small d-none d-md-table-cell">
									<?php if ($item->speaker_title) : ?>
										<?php echo $this->escape($item->speaker_title); ?>
									<?php else : ?>
										<?php echo Text::_('JNONE'); ?>
									<?php endif; ?>
								</td>
								<td class="small d-none d-md-table-cell">
									<?php if ($item->scripture) :
										$passages  = explode('!', $item->scripture);
										$separator = Text::_('COM_SERMONSPEAKER_SCRIPTURE_SEPARATOR');
										$j         = 1;
										foreach ($passages as $passage) :
											$explode = explode('|', $passage);
											if ($explode[5]) :
												if ($explode[0]) :
													echo $explode[5];
												else :
													echo '<i><u>' . $explode[5] . '</u></i>';
												endif;
											else :
												echo Text::_('COM_SERMONSPEAKER_BOOK_' . $explode[0]);
												if ($explode[1]) :
													echo '&nbsp;' . $explode[1];
													if ($explode[2]) :
														echo $separator . $explode[2];
													endif;
													if ($explode[3] || $explode[4]) :
														echo '-';
														if ($explode[3]) :
															echo $explode[3];
															if ($explode[4]) :
																echo $separator . $explode[4];
															endif;
														else :
															echo $explode[4];
														endif;
													endif;
												endif;
											endif;
											if ($j < count($passages)) :
												echo '<br/ >';
											endif;
											$j++;
										endforeach;
									endif; ?>
								</td>
								<td class="small d-none d-md-table-cell">
									<?php if ($item->series_title) : ?>
										<?php echo $this->escape($item->series_title); ?>
									<?php else : ?>
										<?php echo Text::_('JNONE'); ?>
									<?php endif; ?>
								</td>
								<?php if (Multilanguage::isEnabled()) : ?>
									<td class="small d-none d-md-table-cell">
										<?php echo LayoutHelper::render('joomla.content.language', $item); ?>
									</td>
								<?php endif; ?>
								<td class="small d-none d-md-table-cell text-center">
									<?php echo $item->sermon_date > 0 ? HTMLHelper::_('date', $item->sermon_date, Text::_('DATE_FORMAT_LC4')) : '-'; ?>
								</td>
								<td class="d-none d-lg-table-cell text-center">
										<span class="badge bg-info">
											<?php echo (int) $item->hits; ?>
										</span>
									<?php if ($canEdit || $canEditOwn) : ?>
										<a class="btn btn-sm btn-warning"
										   href="index.php?option=com_sermonspeaker&task=sermon.reset&id=<?php echo $item->id; ?>">
											<span class="icon-loop"
												  title="<?php echo Text::_('JSEARCH_RESET'); ?>"></span>
										</a>
									<?php endif; ?>
								</td>
								<td class="d-none d-lg-table-cell">
									<?php echo (int) $item->id; ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>

					<?php // load the pagination. ?>
					<?php echo $this->pagination->getListFooter(); ?>

					<?php //Load the batch processing form. ?>
					<?php if ($user->authorise('core.create', 'com_sermonspeaker')
						&& $user->authorise('core.edit', 'com_sermonspeaker')
						&& $user->authorise('core.edit.state', 'com_sermonspeaker')) : ?>
						<?php echo HTMLHelper::_(
							'bootstrap.renderModal',
							'collapseModal',
							array(
								'title'  => Text::_('COM_SERMONSPEAKER_BATCH_OPTIONS'),
								'footer' => $this->loadTemplate('batch_footer'),
							),
							$this->loadTemplate('batch_body')
						); ?>
					<?php endif; ?>
				<?php endif; ?>

				<input type="hidden" name="task" value="">
				<input type="hidden" name="boxchecked" value="0">
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>
