<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HtmlHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

HtmlHelper::_('bootstrap.tooltip');

$user       = Factory::getUser();
$showState  = $user->authorise('core.edit', 'com_sermonspeaker');
$fu_enable  = $this->params->get('fu_enable');
$canEdit    = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$limit      = (int) $this->params->get('limit', '');
$player     = SermonspeakerHelperSermonspeaker::getPlayer($this->items);
?>
<div class="category-list<?php echo $this->pageclass_sfx; ?> ss-sermons-container<?php echo $this->pageclass_sfx; ?>">
	<?php
	if ($this->params->get('show_page_heading', 1)) : ?>
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif;

	if ($this->params->get('show_category_title', 1) or $this->params->get('page_subheading')) : ?>
		<h2>
			<?php echo $this->escape($this->params->get('page_subheading'));

			if ($this->params->get('show_category_title')) : ?>
				<span class="subheading-category"><?php echo $this->category->title; ?></span>
			<?php endif; ?>
		</h2>
	<?php endif;

	if ($this->params->get('show_description', 1) or $this->params->get('show_description_image', 1)) : ?>
		<div class="category-desc">
			<?php if ($this->params->get('show_description_image') and $this->category->getParams()->get('image')) : ?>
				<img src="<?php echo $this->category->getParams()->get('image'); ?>"/>
			<?php endif;

			if ($this->params->get('show_description') and $this->category->description) :
				echo HtmlHelper::_('content.prepare', $this->category->description, '', 'com_sermonspeaker.category');
			endif; ?>
			<div class="clearfix"></div>
		</div>
	<?php endif;

	if (in_array('sermons:player', $this->columns) and count($this->items)) : ?>
		<?php echo LayoutHelper::render('plugin.player', array('player' => $player, 'items' => $this->items, 'view' => 'sermons')); ?>
	<?php endif; ?>
	<div class="cat-items">
		<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" id="adminForm"
			name="adminForm" class="form-inline">
			<?php
			if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) :
				echo $this->loadTemplate('filters');
			endif; ?>
			<div class="clearfix"></div>
			<?php if (!count($this->items)) : ?>
				<div
					class="no_entries alert alert-error"><?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
			<?php else : ?>
				<ul class="category list-striped list-condensed">
					<?php foreach ($this->items as $i => $item) : ?>
						<?php $sep = 0; ?>
						<li id="sermon<?php echo $i; ?>"
							class="<?php echo ($item->state) ? '' : 'system-unpublished '; ?>cat-list-row<?php echo $i % 2; ?>">
							<?php if (in_array('sermons:hits', $this->columns)) : ?>
								<span class="ss-hits badge badge-info pull-right">
									<?php echo Text::sprintf('JGLOBAL_HITS_COUNT', $item->hits); ?>
								</span>
							<?php endif; ?>
							<?php if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
								<span class="list-edit pull-left width-50">
									<?php echo HtmlHelper::_('icon.edit', $item, $this->params, array('type' => 'sermon')); ?>
								</span>
							<?php endif; ?>
							<strong class="ss-title">
								<?php echo SermonspeakerHelperSermonspeaker::insertSermonTitle($i, $item, $player); ?>
							</strong>
							<?php echo LayoutHelper::render('blocks.state_info', array('item' => $item, 'show' => $showState)); ?>
							<br/>
							<?php if (in_array('sermons:speaker', $this->columns) and $item->speaker_title) : ?>
								<?php $sep = 1; ?>
								<small class="ss-speaker">
									<?php echo Text::_('COM_SERMONSPEAKER_SPEAKER'); ?>:
									<?php echo LayoutHelper::render('titles.speaker', array('item' => $item, 'params' => $this->params)); ?>
								</small>
							<?php endif; ?>

							<?php if (in_array('sermons:series', $this->columns) and $item->series_title) : ?>
								<?php if ($sep) : ?>
									|
								<?php endif; ?>
								<?php $sep = 1; ?>
								<small class="ss-series">
									<?php echo Text::_('COM_SERMONSPEAKER_SERIE'); ?>:
									<?php if ($item->series_state): ?>
										<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSerieRoute($item->series_slug, $item->series_catid, $item->series_language)); ?>">
											<?php echo $item->series_title; ?></a>
									<?php else : ?>
										<?php echo $item->series_title; ?>
									<?php endif; ?>
								</small>
							<?php endif;

							if (in_array('sermons:length', $this->columns) and $item->sermon_time != '00:00:00') : ?>
								<?php if ($sep) : ?>
									|
								<?php endif; ?>
								<?php $sep = 1; ?>
								<small class="ss-length">
									<?php echo Text::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL'); ?>:
									<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
								</small>
							<?php endif;

							if (in_array('sermons:scripture', $this->columns) and $item->scripture) : ?>
								<?php if ($sep) : ?>
									|
								<?php endif; ?>
								<?php $sep = 1; ?>
								<small class="ss-scripture">
									<?php echo Text::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?>:
									<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '; ');
									echo HtmlHelper::_('content.prepare', $scriptures, '', 'com_sermonspeaker.scripture'); ?>
								</small>
							<?php endif; ?>

							<?php if (in_array('sermons:date', $this->columns) and ($item->sermon_date != '0000-00-00 00:00:00')) : ?>
								<span class="ss-date small pull-right">
									<?php echo HtmlHelper::date($item->sermon_date, Text::_($this->params->get('date_format')), true); ?>
								</span>&nbsp;
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ($user->authorise('core.create', 'com_sermonspeaker')) : ?>
				<?php echo HtmlHelper::_('icon.create', $this->category, $this->params); ?>
			<?php endif; ?>

			<?php if ($this->params->get('show_pagination') and ($this->pagination->pagesTotal > 1)) : ?>
				<div class="pagination">
					<?php if ($this->params->get('show_pagination_results', 1)) : ?>
						<p class="counter pull-right">
							<?php echo $this->pagination->getPagesCounter(); ?>
						</p>
					<?php endif; ?>
					<?php echo $this->pagination->getPagesLinks(); ?>
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
