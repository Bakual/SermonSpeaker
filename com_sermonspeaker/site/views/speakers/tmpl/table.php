<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');

$user       = Factory::getUser();
$fu_enable  = $this->params->get('fu_enable');
$canEdit    = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
?>
<div class="com-sermonspeaker-speakers<?php echo $this->pageclass_sfx; ?>  com-sermonspeaker-speakers-table category-list">
	<?php echo LayoutHelper::render('blocks.header', array('category' => $this->category, 'params' => $this->params)); ?>

	<div class="cat-items">
		<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" name="adminForm"
			  id="adminForm" class="com-sermonspeaker-speakers__speakers">
			<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) : ?>
				<?php echo $this->loadTemplate('filters'); ?>
			<?php endif; ?>
			<div class="clearfix"></div>
			<?php if (!count($this->items)) : ?>
				<div class="alert alert-info">
					<span class="icon-info-circle" aria-hidden="true"></span><span
							class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
					<?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SPEAKERS')); ?>
				</div>
			<?php else : ?>
				<table class="com-sermonspeaker-speakers__table category table table-striped table-bordered table-hover">
					<thead>
					<tr>
						<th class="ss-title">
							<?php echo HTMLHelper::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_NAME_LABEL', 'title', $listDirn, $listOrder); ?>
						</th>
						<?php if (in_array('speakers:category', $this->col_speaker)) : ?>
							<th class="ss-col ss-category hidden-phone">
								<?php echo HTMLHelper::_('grid.sort', 'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
							</th>
						<?php endif;

						if (in_array('speakers:intro', $this->col_speaker)) : ?>
							<th class="ss-col ss-intro hidden-phone">
								<?php echo HTMLHelper::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_INTRO_LABEL', 'intro', $listDirn, $listOrder); ?>
							</th>
						<?php endif;

						if (in_array('speakers:hits', $this->col_speaker)) : ?>
							<th class="ss-col ss-hits hidden-phone hidden-tablet">
								<?php echo HTMLHelper::_('grid.sort', 'JGLOBAL_HITS', 'hits', $listDirn, $listOrder); ?>
							</th>
						<?php endif; ?>
						<th></th>
					</tr>
					</thead>
					<!-- Begin Data -->
					<tbody>
					<?php foreach ($this->items as $i => $item) : ?>
						<tr class="<?php echo ($item->state) ? '' : 'system-unpublished '; ?>cat-list-row<?php echo $i % 2; ?>">
							<th class="ss-title">
								<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug, $item->catid, $item->language)); ?>">
									<?php echo $item->title; ?></a>
								<?php if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
									<span class="list-edit">
										<?php echo LayoutHelper::render('icons.edit', ['item' => $item, 'params' => $this->params, 'type' => 'speaker', 'hide_text' => true]); ?>
									</span>
									<?php echo LayoutHelper::render('blocks.state_info', array('item' => $item, 'show' => true)); ?>
								<?php endif; ?>
							</th>
							<?php if (in_array('speakers:category', $this->col_speaker)) : ?>
								<td class="ss-col ss-category hidden-phone">
									<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSpeakersRoute($item->catslug, $item->language)); ?>"><?php echo $item->category_title; ?></a>
								</td>
							<?php endif;

							if (in_array('speakers:intro', $this->col_speaker)) : ?>
								<td class="ss-col ss-intro hidden-phone"><?php echo HTMLHelper::_('content.prepare', $item->intro, '', 'com_sermonspeaker.intro'); ?></td>
							<?php endif;

							if (in_array('speakers:hits', $this->col_speaker)) : ?>
								<td class="ss-col ss-hits hidden-phone hidden-tablet"><?php echo $item->hits; ?></td>
							<?php endif; ?>
							<td class="ss-col ss-links">
								<ul class="list-unstyled">
									<?php if ($item->sermons) : ?>
										<li>
											<a class="badge bg-info"
											   href="<?php echo Route::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug, $item->catid, $item->language) . '#sermons'); ?>">
												<?php echo Text::_('COM_SERMONSPEAKER_SERMONS'); ?>
											</a>
										</li>
									<?php endif;

									if ($item->series) : ?>
										<li>
											<a class="badge bg-info"
											   href="<?php echo Route::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug, $item->catid, $item->language) . '#series'); ?>">
												<?php echo Text::_('COM_SERMONSPEAKER_SERIES'); ?>
											</a>
										</li>
									<?php endif;

									if ($item->website) : ?>
										<li>
											<a class="badge bg-info" href="<?php echo $item->website; ?>">
												<?php echo Text::_('COM_SERMONSPEAKER_FIELD_WEBSITE_LABEL'); ?>
											</a>
										</li>
									<?php endif; ?>
								</ul>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<?php if ($user->authorise('core.create', 'com_sermonspeaker')) : ?>
				<?php echo HTMLHelper::_('icon.create', $this->category, $this->params, 'speaker'); ?>
			<?php endif; ?>

			<?php if (!empty($this->items)) : ?>
				<?php echo LayoutHelper::render('blocks.pagination', array('view' => 'speakers', 'pagination' => $this->pagination, 'params' => $this->params)); ?>
			<?php endif; ?>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
			<input type="hidden" name="limitstart" value=""/>
		</form>
	</div>
	<?php if (!empty($this->children[$this->category->id]) and $this->maxLevel != 0) : ?>
		<div class="cat-children">
			<h3><?php echo Text::_('JGLOBAL_SUBCATEGORIES'); ?></h3>
			<?php echo $this->loadTemplate('children'); ?>
		</div>
	<?php endif; ?>
</div>
