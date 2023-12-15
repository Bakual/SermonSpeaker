<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Layouts.Tiles
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_BASE . '/components/com_sermonspeaker/helpers');

$player = SermonspeakerHelperSermonspeaker::getPlayer($this->items);
?>
<div class="com-sermonspeaker-sermons<?php echo $this->pageclass_sfx; ?>  com-sermonspeaker-sermons-tiles">
	<?php echo LayoutHelper::render('blocks.header', array('category' => $this->category, 'params' => $this->params)); ?>

	<?php if (in_array('sermons:player', $this->columns) and count($this->items)) : ?>
		<?php echo LayoutHelper::render('plugin.player', array('player' => $player, 'items' => $this->items, 'view' => 'sermons')); ?>
	<?php endif; ?>
	<form action="<?php echo OutputFilter::ampReplace(Uri::getInstance()->toString()); ?>" method="post"
		  name="adminForm" id="adminForm" class="com-sermonspeaker-sermons__sermons">
		<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) : ?>
			<?php echo $this->loadTemplate('filters'); ?>
			<?php echo $this->loadTemplate('order'); ?>
		<?php endif; ?>
		<div class="clearfix"></div>

		<?php if (!count($this->items)) : ?>
			<span class="icon-info-circle" aria-hidden="true"></span><span
					class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
			<?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SERMONS')); ?>
		<?php else : ?>
			<div class="row row-cols-1 row-cols-md-4 g-4">
				<?php foreach ($this->items as $i => $item) : ?>
					<?php $sermonUrl = Route::_(SermonspeakerHelperRoute::getSermonRoute($item->slug, $item->catid, $item->language)); ?>
					<?php $picture = SermonspeakerHelperSermonspeaker::insertPicture($item); ?>
					<?php if (!$picture) : ?>
						<?php $picture = 'media/com_sermonspeaker/images/' . $this->params->get('defaultpic', 'nopict.jpg'); ?>
					<?php endif; ?>
					<div class="col">
						<div id="sermon<?php echo $i; ?>" class="ss-entry sermon-item card h-100">
							<a href="<?php echo $sermonUrl; ?>">
								<img class="card-img-top" src="<?php echo trim($picture, '/'); ?>">
							</a>
							<div class="card-body">
								<a href="<?php echo $sermonUrl; ?>">
									<h5 class="card-title"><?php echo $item->title; ?></h5>
								</a>
								<div class="card-text">
									<?php if (in_array('speaker:num', $this->columns) and $item->sermon_number) : ?>
										<?php echo Text::_('COM_SERMONSPEAKER_FIELD_NUM_LABEL') . ': ' . $item->sermon_number; ?>
										<br>
									<?php endif; ?>

									<?php if (in_array('speaker:date', $this->columns) and ($item->sermon_date != '0000-00-00 00:00:00')) : ?>
										<?php echo Text::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'); ?>:
										<?php echo HTMLHelper::date($item->sermon_date, Text::_($this->params->get('date_format')), true); ?>
										<br>
									<?php endif; ?>

									<?php if (in_array('speaker:category', $this->columns)) : ?>
										<?php echo Text::_('JCATEGORY'); ?>:
										<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSermonsRoute($item->catslug, $item->language)); ?>">
											<?php echo $item->category_title; ?></a>
										<br>
									<?php endif; ?>

									<?php if (in_array('sermons:speaker', $this->columns) and $item->speaker_title) : ?>
										<?php echo Text::_('COM_SERMONSPEAKER_FIELD_SPEAKER_LABEL'); ?>:
										<?php echo LayoutHelper::render('titles.speaker', array('item' => $item, 'params' => $this->params)); ?>
										<br>
									<?php endif; ?>

									<?php if (in_array('speaker:series', $this->columns) and $item->series_title) : ?>
										<?php echo Text::_('COM_SERMONSPEAKER_FIELD_SERIES_LABEL'); ?>:
										<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSerieRoute($item->series_slug, $item->series_catid, $item->series_language)); ?>">
											<?php echo $item->series_title; ?></a>
										<br>
									<?php endif; ?>

									<?php if (in_array('speaker:scripture', $this->columns) and $item->scripture) : ?>
										<?php echo Text::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL') . ': ' . SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '; ', false); ?>
										<br>
									<?php endif; ?>

									<?php if (in_array('speaker:length', $this->columns) and $item->sermon_time) : ?>
										<?php echo Text::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL') . ': ' . SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
										<br>
									<?php endif; ?>

									<?php if (in_array('speaker:hits', $this->columns) and $item->hits) : ?>
										<?php echo Text::_('JGLOBAL_HITS') . ': ' . $item->hits; ?>
										<br>
									<?php endif; ?>
								</div>
							</div>
							<?php if (in_array('speaker:notes', $this->columns) and $item->notes) : ?>
								<div class="card-footer text-muted">
									<?php echo $item->notes; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if (!empty($this->items)) : ?>
			<?php echo LayoutHelper::render('blocks.pagination', array('view' => 'sermons', 'pagination' => $this->pagination, 'params' => $this->params)); ?>
		<?php endif; ?>
		<input type="hidden" name="task" value=""/>
	</form>
	<?php if (!empty($this->children[$this->category->id]) and $this->maxLevel != 0) : ?>
		<div class="cat-children">
			<h3><?php echo Text::_('JGLOBAL_SUBCATEGORIES'); ?></h3>
			<?php echo $this->loadTemplate('children'); ?>
		</div>
	<?php endif; ?>
</div>
