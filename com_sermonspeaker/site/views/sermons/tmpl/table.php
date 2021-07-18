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
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');

$user       = Factory::getUser();
$fu_enable  = $this->params->get('fu_enable');
$canEdit    = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$limit      = (int) $this->params->get('limit', '');
$player     = SermonspeakerHelperSermonspeaker::getPlayer($this->items);
?>
<div class="com-sermonspeaker-sermons<?php echo $this->pageclass_sfx; ?>  com-sermonspeaker-sermons-table category-list">
	<?php echo LayoutHelper::render('blocks.header', array('category' => $this->category, 'params' => $this->params)); ?>

	<?php if (in_array('sermons:player', $this->columns) and count($this->items)) : ?>
		<?php echo LayoutHelper::render('plugin.player', array('player' => $player, 'items' => $this->items, 'view' => 'sermons')); ?>
	<?php endif; ?>
	<div class="cat-items">
		<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm"
			  id="adminForm" class="com-sermonspeaker-sermons__sermons">
			<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) : ?>
				<?php echo $this->loadTemplate('filters'); ?>
			<?php endif; ?>
			<div class="clearfix"></div>
			<?php if (!count($this->items)) : ?>
				<div class="alert alert-info">
					<span class="icon-info-circle" aria-hidden="true"></span><span
							class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
					<?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SERMONS')); ?>
				</div>
			<?php else : ?>
				<table class="com-sermonspeaker-sermons__table category table table-striped table-bordered table-hover">
					<thead>
					<tr>
						<?php if (in_array('sermons:num', $this->columns)) : ?>
							<th class="num hidden-phone hidden-tablet">
								<?php if (!$limit) :
									echo HTMLHelper::_('grid.sort', 'COM_SERMONSPEAKER_SERMONNUMBER', 'sermon_number', $listDirn, $listOrder);
								else :
									echo Text::_('COM_SERMONSPEAKER_SERMONNUMBER');
								endif; ?>
							</th>
						<?php endif; ?>
						<th class="ss-title">
							<?php if (!$limit) :
								echo HTMLHelper::_('grid.sort', 'JGLOBAL_TITLE', 'title', $listDirn, $listOrder);
							else :
								echo Text::_('JGLOBAL_TITLE');
							endif; ?>
						</th>
						<?php if (in_array('sermons:category', $this->columns)) : ?>
							<th class="ss-col ss-category hidden-phone">
								<?php if (!$limit) :
									echo HTMLHelper::_('grid.sort', 'JCATEGORY', 'category_title', $listDirn, $listOrder);
								else :
									echo Text::_('JCATEGORY');
								endif; ?>
							</th>
						<?php endif;

						if (in_array('sermons:scripture', $this->columns)) : ?>
							<th class="ss-col ss-scripture hidden-phone">
								<?php if (!$limit) :
									echo HTMLHelper::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL', 'book', $listDirn, $listOrder);
								else :
									echo Text::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL');
								endif; ?>
							</th>
						<?php endif;

						if (in_array('sermons:speaker', $this->columns)) : ?>
							<th class="ss-col ss-speaker hidden-phone">
								<?php if (!$limit) :
									echo HTMLHelper::_('grid.sort', 'COM_SERMONSPEAKER_SPEAKER', 'speaker_title', $listDirn, $listOrder);
								else :
									echo Text::_('COM_SERMONSPEAKER_SPEAKER');
								endif; ?>
							</th>
						<?php endif;

						if (in_array('sermons:date', $this->columns)) : ?>
							<th class="ss-col ss-date">
								<?php if (!$limit) :
									echo HTMLHelper::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_DATE_LABEL', 'sermon_date', $listDirn, $listOrder);
								else :
									echo Text::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL');
								endif; ?>
							</th>
						<?php endif;

						if (in_array('sermons:length', $this->columns)) : ?>
							<th class="ss-col ss-length hidden-phone hidden-tablet">
								<?php if (!$limit) :
									echo HTMLHelper::_('grid.sort', 'COM_SERMONSPEAKER_FIELD_LENGTH_LABEL', 'sermon_time', $listDirn, $listOrder);
								else :
									echo Text::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL');
								endif; ?>
							</th>
						<?php endif;

						if (in_array('sermons:series', $this->columns)) : ?>
							<th class="ss-col ss-series hidden-phone">
								<?php if (!$limit) :
									echo HTMLHelper::_('grid.sort', 'COM_SERMONSPEAKER_SERIES', 'series_title', $listDirn, $listOrder);
								else :
									echo Text::_('COM_SERMONSPEAKER_SERIES');
								endif; ?>
							</th>
						<?php endif;

						if (in_array('sermons:addfile', $this->columns)) : ?>
							<th class="ss-col ss-addfile hidden-phone">
								<?php if (!$limit) :
									echo HTMLHelper::_('grid.sort', 'COM_SERMONSPEAKER_ADDFILE', 'addfileDesc', $listDirn, $listOrder);
								else :
									echo Text::_('COM_SERMONSPEAKER_ADDFILE');
								endif; ?>
							</th>
						<?php endif;

						if (in_array('sermons:hits', $this->columns)) : ?>
							<th class="ss-col ss-hits hidden-phone hidden-tablet">
								<?php if (!$limit) :
									echo HTMLHelper::_('grid.sort', 'JGLOBAL_HITS', 'hits', $listDirn, $listOrder);
								else :
									echo Text::_('JGLOBAL_HITS');
								endif; ?>
							</th>
						<?php endif;

						if (in_array('sermons:download', $this->columns)) :
							$prio = $this->params->get('fileprio'); ?>
							<th class="ss-col ss-dl hidden-phone"></th>
						<?php endif; ?>
					</tr>
					</thead>
					<!-- Begin Data -->
					<tbody>
					<?php foreach ($this->items as $i => $item) : ?>
						<tr id="sermon<?php echo $i; ?>"
							class="<?php echo ($item->state) ? '' : 'system-unpublished '; ?>cat-list-row<?php echo $i % 2; ?>">
							<?php
							if (in_array('sermons:num', $this->columns)) : ?>
								<td class="num hidden-phone hidden-tablet">
									<?php echo $item->sermon_number; ?>
								</td>
							<?php endif; ?>
							<th class="ss-title">
								<?php echo SermonspeakerHelperSermonspeaker::insertSermonTitle($i, $item, $player);

								if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
									<span class="list-edit">
										<?php echo LayoutHelper::render('icons.edit', ['item' => $item, 'params' => $this->params, 'type' => 'sermon', 'hide_text' => true]); ?>
									</span>
									<?php echo LayoutHelper::render('blocks.state_info', array('item' => $item, 'show' => true)); ?>
								<?php endif; ?>
							</th>
							<?php if (in_array('sermons:category', $this->columns)) : ?>
								<td class="ss-col ss-category hidden-phone">
									<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSermonsRoute($item->catid, $item->language)); ?>"><?php echo $item->category_title; ?></a>
								</td>
							<?php endif;

							if (in_array('sermons:scripture', $this->columns)) : ?>
								<td class="ss-col ss-scripture hidden-phone">
									<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '<br />');
									echo HTMLHelper::_('content.prepare', $scriptures); ?>
								</td>
							<?php endif;

							if (in_array('sermons:speaker', $this->columns)) : ?>
								<td class="ss-col ss-speaker hidden-phone">
									<?php echo LayoutHelper::render('titles.speaker', array('item' => $item, 'params' => $this->params)); ?>
								</td>
							<?php endif;

							if (in_array('sermons:date', $this->columns)) : ?>
								<td class="ss-col ss-date">
									<?php if ($item->sermon_date != '0000-00-00 00:00:00'):
										echo HTMLHelper::date($item->sermon_date, Text::_($this->params->get('date_format')), true);
									endif; ?>
								</td>
							<?php endif;

							if (in_array('sermons:length', $this->columns)) : ?>
								<td class="ss-col ss-length hidden-phone hidden-tablet">
									<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
								</td>
							<?php endif;

							if (in_array('sermons:series', $this->columns)) : ?>
								<td class="ss-col ss-series hidden-phone">
									<?php if ($item->series_state): ?>
										<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSerieRoute($item->series_slug, $item->series_catid, $item->series_language)); ?>">
											<?php echo $item->series_title; ?>
										</a>
									<?php else:
										echo $item->series_title;
									endif; ?>
								</td>
							<?php endif;

							if (in_array('sermons:addfile', $this->columns)) : ?>
								<td class="ss-col ss-addfile hidden-phone">
									<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($item->addfile, $item->addfileDesc); ?>
								</td>
							<?php endif;

							if (in_array('sermons:hits', $this->columns)) : ?>
								<td class="ss-col ss-hits hidden-phone hidden-tablet">
									<?php echo $item->hits; ?>
								</td>
							<?php endif;

							if (in_array('sermons:download', $this->columns)) :
								$type = ($item->videofile and ($prio || !$item->audiofile)) ? 'video' : 'audio';
								$filesize = $type . 'filesize'; ?>
								<td class="ss-col ss-dl hidden-phone">
									<?php echo SermonspeakerHelperSermonspeaker::insertdlbutton($item->slug, $type, 3, $item->$filesize); ?>
								</td>
							<?php endif; ?>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif;

			if ($user->authorise('core.create', 'com_sermonspeaker')) :
				echo HTMLHelper::_('icon.create', $this->category, $this->params, 'sermon');
			endif;

			if ($this->params->get('show_pagination') and ($this->pagination->pagesTotal > 1)) : ?>
				<div class="pagination">
					<?php if ($this->params->get('show_pagination_results', 1)) : ?>
						<p class="counter pull-right">
							<?php echo $this->pagination->getPagesCounter(); ?>
						</p>
					<?php endif;
					echo $this->pagination->getPagesLinks(); ?>
				</div>
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
