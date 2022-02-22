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
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');

HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');
HTMLHelper::_('behavior.multiselect');

$user      = Factory::getUser();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$archived  = $this->state->get('filter.state') == 2;
$trashed   = $this->state->get('filter.state') == -2;
$saveOrder = $listOrder == 'speakers.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_sermonspeaker&task=speakers.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
	HTMLHelper::_('draggablelist.draggable');
}

$assoc = Associations::isEnabled();
?>
<form action="<?php echo Route::_('index.php?option=com_sermonspeaker&view=speakers'); ?>" method="post"
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
					<table class="table" id="speakerList">
						<caption id="captionTable" class="sr-only">
							<?php echo Text::_('COM_SERMONSPEAKER_SPEAKERS_TABLE_CAPTION'); ?>
							, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
						</caption>
						<thead>
						<tr>
							<td style="width:1%" class="text-center">
								<?php echo HTMLHelper::_('grid.checkall'); ?>
							</td>
							<th scope="col" style="width:1%" class="text-center d-none d-md-table-cell">
								<?php echo HTMLHelper::_('searchtools.sort', '', 'speakers.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
							</th>
							<th scope="col" style="width:1%" class="text-center">
								<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'speakers.state', $listDirn, $listOrder); ?>
							</th>
							<th scope="col" style="width:1%" class="text-center d-none d-md-table-cell">
								<?php echo HTMLHelper::_('searchtools.sort', 'JDEFAULT', 'speakers.home', $listDirn, $listOrder); ?>
							</th>
							<th scope="col" style="min-width:100px">
								<?php echo HTMLHelper::_('searchtools.sort', 'COM_SERMONSPEAKER_FIELD_NAME_LABEL', 'speakers.title', $listDirn, $listOrder); ?>
							</th>
							<?php if ($assoc) : ?>
								<th scope="col" style="width:5%" class="d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_SERMONSPEAKER_HEADING_ASSOCIATION', 'association', $listDirn, $listOrder); ?>
								</th>
							<?php endif; ?>
							<th scope="col" style="width:10%" class="d-none d-md-table-cell">
								<?php echo HTMLHelper::_('searchtools.sort', 'COM_SERMONSPEAKER_FIELD_PICTURE_LABEL', 'speakers.pic', $listDirn, $listOrder); ?>
							</th>
							<?php if (Multilanguage::isEnabled()) : ?>
								<th scope="col" style="width:10%" class="d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
								</th>
							<?php endif; ?>
							<th scope="col" style="width:7%" class="d-none d-lg-table-cell text-center">
								<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_HITS', 'speakers.hits', $listDirn, $listOrder); ?>
							</th>
							<th scope="col" style="width:3%" class="d-none d-lg-table-cell">
								<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'speakers.id', $listDirn, $listOrder); ?>
							</th>
						</tr>
						</thead>
						<tbody <?php if ($saveOrder) : ?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php endif; ?>>
						<?php foreach ($this->items as $i => $item) :
							$canEdit = $user->authorise('core.edit', 'com_sermonspeaker.category.' . $item->catid);
							$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->id || $item->checked_out == 0;
							$canEditOwn = $user->authorise('core.edit.own', 'com_sermonspeaker.category.' . $item->catid) && $item->created_by == $user->id;
							$canChange = $user->authorise('core.edit.state', 'com_sermonspeaker.category.' . $item->catid) && $canCheckin;
							$canEditCat = $user->authorise('core.edit', 'com_sermonspeaker.category.' . $item->catid);
							$canEditOwnCat = $user->authorise('core.edit.own', 'com_sermonspeaker.category.' . $item->catid) && $item->category_uid == $user->id;
							$canEditParCat = $user->authorise('core.edit', 'com_sermonspeaker.category.' . $item->parent_category_id);
							$canEditOwnParCat = $user->authorise('core.edit.own', 'com_sermonspeaker.category.' . $item->parent_category_id) && $item->parent_category_uid == $user->id;
							?>
							<tr class="row<?php echo $i % 2; ?>" data-draggable-group="<?php echo $item->catid; ?>">
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
									<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'speakers.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
								</td>
								<td class="text-center d-none d-md-table-cell">
									<?php echo HTMLHelper::_('jgrid.isdefault', $item->home, $i, 'speakers.', $canChange); ?>
								</td>
								<td class="nowrap has-context">
									<div class="pull-left">
										<?php if ($item->checked_out) : ?>
											<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'speakers.', $canCheckin); ?>
										<?php endif; ?>
										<?php if ($canEdit || $canEditOwn) : ?>
											<a href="<?php echo Route::_('index.php?option=com_sermonspeaker&task=speaker.edit&id='
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
											$ParentCatUrl  = Route::_('index.php?option=com_categories&task=category.edit&id=' . $item->parent_category_id . '&extension=com_sermonspeaker.speakers');
											$CurrentCatUrl = Route::_('index.php?option=com_categories&task=category.edit&id=' . $item->catid . '&extension=com_sermonspeaker.speakers');
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
									<td class="hidden-phone">
										<?php if ($item->association) : ?>
											<?php echo HTMLHelper::_('sermonspeakeradministrator.association', $item->id, 'speaker'); ?>
										<?php endif; ?>
									</td>
								<?php endif; ?>
								<td class="small d-none d-md-table-cell">
									<?php if (!$item->pic && $pic = $this->state->get('params')->get('defaultpic')) : ?>
										<?php $item->pic = Uri::root() . 'media/com_sermonspeaker/images/' . $pic; ?>
									<?php elseif ($item->pic && !parse_url($item->pic, PHP_URL_SCHEME)) : ?>
										<?php $item->pic = Uri::root() . trim($item->pic, '/.'); ?>
									<?php endif; ?>
									<?php if ($item->pic) : ?>
										<img src="<?php echo $item->pic; ?>" style="max-height: 40px">
									<?php else : ?>
										<span class="fas fa-picture-o fa-3x text-secondary"></span>
									<?php endif; ?>
								</td>
								<?php if (Multilanguage::isEnabled()) : ?>
									<td class="small d-none d-md-table-cell">
										<?php echo LayoutHelper::render('joomla.content.language', $item); ?>
									</td>
								<?php endif; ?>
								<td class="d-none d-lg-table-cell text-center">
										<span class="badge bg-info">
											<?php echo (int) $item->hits; ?>
										</span>
									<?php if ($canEdit || $canEditOwn) : ?>
										<a class="btn btn-sm btn-warning"
										   href="index.php?option=com_sermonspeaker&task=speaker.reset&id=<?php echo $item->id; ?>">
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

				<input type="hidden" name="task" value=""/>
				<input type="hidden" name="boxchecked" value="0"/>
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>
