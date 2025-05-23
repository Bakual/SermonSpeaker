<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Layouts.Tiles
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_BASE . '/components/com_sermonspeaker/helpers');

$user       = Factory::getUser();
$fu_enable  = $this->params->get('fu_enable');
$canEdit    = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$player     = SermonspeakerHelperSermonspeaker::getPlayer($this->sermons);
?>
<div class="com-sermonspeaker-speaker<?php echo $this->pageclass_sfx; ?> com-sermonspeaker-speaker-tiles" itemscope
	 itemtype="http://schema.org/Person">
	<?php echo $this->loadTemplate('header'); ?>
	<div class="clearfix"></div>

	<?php if (in_array('speaker:player', $this->col_sermon) and count($this->sermons)) : ?>
		<?php echo LayoutHelper::render('plugin.player', array('player' => $player, 'items' => $this->sermons, 'view' => 'speaker')); ?>
	<?php endif; ?>
	<form action="<?php echo OutputFilter::ampReplace(Uri::getInstance()->toString()); ?>" method="post"
		  name="adminForm" id="adminForm" class="com-sermonspeaker-speaker__sermons">
		<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) : ?>
			<?php echo $this->loadTemplate('filters'); ?>
		<?php endif; ?>
		<div class="clearfix"></div>

		<?php if (!count($this->sermons)) : ?>
			<span class="icon-info-circle" aria-hidden="true"></span><span
					class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
			<?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SERMONS')); ?>
		<?php else : ?>
			<div class="row row-cols-1 row-cols-md-4 g-4">
				<?php foreach ($this->sermons as $i => $item) : ?>
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
									<?php if (in_array('speaker:num', $this->col_sermon) and $item->sermon_number) : ?>
										<?php echo Text::_('COM_SERMONSPEAKER_FIELD_NUM_LABEL') . ': ' . $item->sermon_number; ?>
										<br>
									<?php endif; ?>

									<?php if (in_array('speaker:date', $this->col_sermon) and ($item->sermon_date != '0000-00-00 00:00:00')) : ?>
										<?php echo Text::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'); ?>:
										<?php echo HTMLHelper::date($item->sermon_date, Text::_($this->params->get('date_format')), true); ?>
										<br>
									<?php endif; ?>

									<?php if (in_array('speaker:category', $this->col_sermon)) : ?>
										<?php echo Text::_('JCATEGORY'); ?>:
										<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSermonsRoute($item->catslug, $item->language)); ?>">
											<?php echo $item->category_title; ?></a>
										<br>
									<?php endif; ?>

									<?php if (in_array('speaker:series', $this->col_sermon) and $item->series_title) : ?>
										<?php echo Text::_('COM_SERMONSPEAKER_FIELD_SERIES_LABEL'); ?>:
										<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSerieRoute($item->series_slug, $item->series_catid, $item->series_language)); ?>">
											<?php echo $item->series_title; ?></a>
										<br>
									<?php endif; ?>

									<?php if (in_array('speaker:scripture', $this->col_sermon) and $item->scripture) : ?>
										<?php echo Text::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL') . ': ' . SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '; ', false); ?>
										<br>
									<?php endif; ?>

									<?php if (in_array('speaker:length', $this->col_sermon) and $item->sermon_time) : ?>
										<?php echo Text::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL') . ': ' . SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
										<br>
									<?php endif; ?>

									<?php if (in_array('speaker:hits', $this->col_sermon) and $item->hits) : ?>
										<?php echo Text::_('JGLOBAL_HITS') . ': ' . $item->hits; ?>
										<br>
									<?php endif; ?>
								</div>
							</div>
							<?php if (in_array('speaker:notes', $this->col_sermon) and $item->notes) : ?>
								<div class="card-footer text-muted ss-notes">
									<?php echo $item->notes; ?>
								</div>
							<?php endif; ?>
							<?php if (in_array('speaker:maintext', $this->col_sermon) and $item->maintext) : ?>
								<div class="card-footer text-muted ss-maintext">
									<?php echo $item->maintext; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if (!empty($this->sermons)) : ?>
			<?php echo LayoutHelper::render('blocks.pagination', array('view' => 'sermons', 'pagination' => $this->pag_sermons, 'params' => $this->params)); ?>
		<?php endif; ?>
		<input type="hidden" name="task" value=""/>
	</form>
</div>
