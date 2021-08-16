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
<div class="com-sermonspeaker-series<?php echo $this->pageclass_sfx; ?> com-sermonspeaker-series-table">
	<?php echo LayoutHelper::render('blocks.header', array('category' => $this->category, 'params' => $this->params)); ?>

	<div class="cat-items">
		<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" id="adminForm"
			  name="adminForm" class="com-sermonspeaker-series__series">
			<?php echo $this->loadTemplate('filters'); ?>
			<div class="clearfix"></div>
			<?php if (!count($this->items)) : ?>
				<div class="alert alert-info">
					<span class="icon-info-circle" aria-hidden="true"></span><span
							class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
					<?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SERIES')); ?>
				</div>
			<?php else : ?>
				<table class="com-sermonspeaker-series__table category table table-striped table-bordered table-hover">
					<thead>
					<tr>
						<?php if ($this->av) : ?>
							<th class="ss-av hidden-phone hidden-tablet"></th>
						<?php endif; ?>
						<th class="ss-title">
							<?php echo HTMLHelper::_('grid.sort', 'JGLOBAL_TITLE', 'title', $listDirn, $listOrder); ?>
						</th>
						<?php if (in_array('series:category', $this->col_serie)) : ?>
							<th class="ss-col ss-category hidden-phone">
								<?php echo HTMLHelper::_('grid.sort', 'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
							</th>
						<?php endif;

						if (in_array('series:description', $this->col_serie)) : ?>
							<th class="ss-col ss-series_desc hidden-phone">
								<?php echo HTMLHelper::_('grid.sort', 'JGLOBAL_DESCRIPTION', 'series_description', $listDirn, $listOrder); ?>
							</th>
						<?php endif;

						if (in_array('series:speaker', $this->col_serie)) : ?>
							<th class="ss-col ss-speakers hidden-phone hidden-tablet"><?php echo Text::_('COM_SERMONSPEAKER_SPEAKERS'); ?></th>
						<?php endif;

						if (in_array('series:hits', $this->col_serie)) : ?>
							<th class="ss-col ss-hits hidden-phone hidden-tablet">
								<?php echo HTMLHelper::_('grid.sort', 'JGLOBAL_HITS', 'hits', $listDirn, $listOrder); ?>
							</th>
						<?php endif;

						if (in_array('series:download', $this->col_serie)) : ?>
							<th class="ss-col ss-dl hidden-phone"></th>
						<?php endif; ?>
					</tr>
					</thead>
					<!-- Begin Data -->
					<tbody>
					<?php foreach ($this->items as $i => $item) : ?>
						<tr class="<?php echo ($item->state) ? '' : 'system-unpublished '; ?>cat-list-row<?php echo $i % 2; ?>">
							<?php
							if ($this->av) :
								if ($item->avatar) : ?>
									<td class="ss-col ss-av hidden-phone hidden-tablet"><a
												href="<?php echo Route::_(SermonspeakerHelperRoute::getSerieRoute($item->slug, $item->catid, $item->language)); ?>"><img
													class="img-thumbnail "
													src="<?php echo SermonspeakerHelperSermonspeaker::makeLink($item->avatar); ?>"></a>
									</td>
								<?php else : ?>
									<td class="ss-col ss-av hidden-phone hidden-tablet"></td>
								<?php endif;
							endif; ?>
							<th class="ss-title">
								<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSerieRoute($item->slug, $item->catid, $item->language)); ?>">
									<?php echo $item->title; ?></a>
								<?php if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
									<span class="list-edit">
										<?php echo LayoutHelper::render('icons.edit', ['item' => $item, 'params' => $this->params, 'type' => 'serie', 'hide_text' => true]); ?>
									</span>
									<?php echo LayoutHelper::render('blocks.state_info', array('item' => $item, 'show' => true)); ?>
								<?php endif; ?>
							</th>
							<?php if (in_array('series:category', $this->col_serie)) : ?>
								<td class="ss-col ss-category hidden-phone">
									<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSeriesRoute($item->catslug, $item->language)); ?>"><?php echo $item->category_title; ?></a>
								</td>
							<?php endif;

							if (in_array('series:description', $this->col_serie)): ?>
								<td class="ss-col ss-series_desc hidden-phone"><?php echo HTMLHelper::_('content.prepare', $item->series_description); ?></td>
							<?php endif;

							if (in_array('series:speaker', $this->col_serie)) : ?>
								<td class="ss-col ss-speakers hidden-phone hidden-tablet"><?php echo $item->speakers; ?></td>
							<?php endif;

							if (in_array('series:hits', $this->col_serie)) : ?>
								<td class="ss-col ss-hits hidden-phone hidden-tablet"><?php echo $item->hits; ?></td>
							<?php endif;

							if (in_array('series:download', $this->col_serie)) : ?>
								<td class="ss-col ss-dl hidden-phone">
									<?php $url = Route::_('index.php?view=serie&layout=download&tmpl=component&id=' . $item->slug); ?>
									<?php $downloadText = Text::_('COM_SERMONSPEAKER_DOWNLOADSERIES_LABEL'); ?>
									<?php $modalOptions = array('url' => $url, 'height' => 200, 'width' => 400, 'title' => $downloadText); ?>
									<?php echo HTMLHelper::_('bootstrap.rendermodal', 'downloadModal' . $i, $modalOptions); ?>
									<a href="#downloadModal<?php echo $i; ?>" class="downloadModal" data-bs-toggle="modal" title="<?php echo $downloadText; ?>">
										<span class="icon-download"></span>
									</a>
								</td>
							<?php endif; ?>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<?php if ($user->authorise('core.create', 'com_sermonspeaker')) : ?>
				<?php echo HTMLHelper::_('icon.create', $this->category, $this->params, 'serie'); ?>
			<?php endif; ?>

			<?php if (!empty($this->items)) : ?>
				<?php echo LayoutHelper::render('blocks.pagination', array('view' => 'series', 'pagination' => $this->pagination, 'params' => $this->params)); ?>
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
