<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Layouts.Tiles
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_BASE . '/components/com_sermonspeaker/helpers');

$player = SermonspeakerHelper::getPlayer($this->items);
?>
<div class="com-sermonspeaker-serie<?php echo $this->pageclass_sfx; ?> com-sermonspeaker-serie-tiles">
	<?php echo $this->loadTemplate('header'); ?>
	<div class="clearfix"></div>
	<?php if (in_array('serie:player', $this->columns) and count($this->items)) : ?>
		<?php echo LayoutHelper::render('plugin.player', array('player' => $player, 'items' => $this->items, 'view' => 'serie')); ?>
	<?php endif; ?>
	<?php echo $this->item->event->afterDisplayContent; ?>
	<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm"
		  id="adminForm" class="com-sermonspeaker-serie__sermons">
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
			<div class="row row-cols-1 row-cols-md-4 g-4">
				<?php foreach ($this->items as $i => $item) : ?>
					<?php $sermonUrl = Route::_(Sermonspeaker\Component\Sermonspeaker\Site\Helper\RouteHelper::getSermonRoute($item->slug, $item->catid, $item->language)); ?>
					<?php $picture = SermonspeakerHelper::insertPicture($item); ?>
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
									<?php if (in_array('serie:num', $this->columns) and $item->sermon_number) : ?>
										<?php echo Text::_('COM_SERMONSPEAKER_FIELD_NUM_LABEL') . ': ' . $item->sermon_number; ?>
										<br>
									<?php endif; ?>

									<?php if (in_array('serie:date', $this->columns) and ($item->sermon_date != '0000-00-00 00:00:00')) : ?>
										<?php echo Text::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'); ?>:
										<?php echo HTMLHelper::date($item->sermon_date, Text::_($this->params->get('date_format')), true); ?>
										<br>
									<?php endif; ?>

									<?php if (in_array('serie:category', $this->columns)) : ?>
										<?php echo Text::_('JCATEGORY'); ?>:
										<a href="<?php echo Route::_(Sermonspeaker\Component\Sermonspeaker\Site\Helper\RouteHelper::getSermonsRoute($item->catslug, $item->language)); ?>">
											<?php echo $item->category_title; ?></a>
										<br>
									<?php endif; ?>

									<?php if (in_array('serie:speaker', $this->columns) and $item->speaker_title) : ?>
										<?php echo Text::_('COM_SERMONSPEAKER_FIELD_SPEAKER_LABEL'); ?>:
										<?php echo LayoutHelper::render('titles.speaker', array('item' => $item, 'params' => $this->params)); ?>
										<br>
									<?php endif; ?>

									<?php if (in_array('serie:scripture', $this->columns) and $item->scripture) : ?>
										<?php echo Text::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL') . ': ' . SermonspeakerHelper::insertScriptures($item->scripture, '; ', false); ?>
										<br>
									<?php endif; ?>

									<?php if (in_array('serie:length', $this->columns) and $item->sermon_time) : ?>
										<?php echo Text::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL') . ': ' . SermonspeakerHelper::insertTime($item->sermon_time); ?>
										<br>
									<?php endif; ?>

									<?php if (in_array('serie:hits', $this->columns) and $item->hits) : ?>
										<?php echo Text::_('JGLOBAL_HITS') . ': ' . $item->hits; ?>
										<br>
									<?php endif; ?>
								</div>
							</div>
							<?php if (in_array('serie:notes', $this->columns) and $item->notes) : ?>
								<div class="card-footer text-muted ss-notes">
									<?php echo $item->notes; ?>
								</div>
							<?php endif; ?>
							<?php if (in_array('serie:maintext', $this->columns) and $item->maintext) : ?>
								<div class="card-footer text-muted ss-maintext">
									<?php echo $item->maintext; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if (!empty($this->items)) : ?>
			<?php echo LayoutHelper::render('blocks.pagination', array('view' => 'serie', 'pagination' => $this->pagination, 'params' => $this->params)); ?>
		<?php endif; ?>
		<input type="hidden" name="task" value=""/>
	</form>
</div>
