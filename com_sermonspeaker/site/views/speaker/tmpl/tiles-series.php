<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Layouts.Tiles
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
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

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

$user       = Factory::getUser();
$fu_enable  = $this->params->get('fu_enable');
$canEdit    = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
?>
<div class="com-sermonspeaker-speaker<?php echo $this->pageclass_sfx; ?> com-sermonspeaker-speaker-tiles" itemscope
	 itemtype="http://schema.org/Person">
	<?php echo $this->loadTemplate('header'); ?>
	<div class="clearfix"></div>

	<form action="<?php echo OutputFilter::ampReplace(Uri::getInstance()->toString()); ?>" method="post"
		  name="adminForm" id="adminForm" class="com-sermonspeaker-speaker__series">
		<?php if ($this->params->get('show_pagination_limit')) : ?>
			<div class="com-sermonspeaker-sermons__pagination btn-group float-end">
				<label for="limit" class="visually-hidden">
					<?php echo Text::_('JGLOBAL_DISPLAY_NUM'); ?>
				</label>
				<?php echo $this->pag_series->getLimitBox(); ?>
			</div>
		<?php endif; ?>
		<div class="clearfix"></div>

		<?php if (!count($this->series)) : ?>
			<span class="icon-info-circle" aria-hidden="true"></span><span
					class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
			<?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SERIES')); ?>
		<?php else : ?>
			<div class="row row-cols-1 row-cols-md-4 g-4">
				<?php foreach ($this->series as $i => $item) : ?>
					<?php $serieUrl = Route::_(SermonspeakerHelperRoute::getSerieRoute($item->slug, $item->catid, $item->language)); ?>
					<?php $image = ($item->avatar) ?: 'media/com_sermonspeaker/images/' . $this->params->get('defaultpic', 'nopict.jpg'); ?>
					<div class="col">
						<div id="serie<?php echo $i; ?>" class="ss-entry card h-100">
							<a href="<?php echo $serieUrl; ?>">
								<img class="card-img-top" src="<?php echo trim($image, '/'); ?>">
							</a>
							<div class="card-body">
								<a href="<?php echo $serieUrl; ?>">
									<h5 class="card-title"><?php echo $item->title; ?></h5>
								</a>
								<div class="card-text">
									<?php if (in_array('speaker:category', $this->col_serie)) : ?>
										<?php echo Text::_('JCATEGORY'); ?>:
										<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSeriesRoute($item->catslug, $item->language)); ?>">
											<?php echo $item->category_title; ?></a>
										<br>
									<?php endif; ?>

									<?php if (in_array('speaker:speaker', $this->col_serie) and $item->speakers) : ?>
										<?php echo Text::_('COM_SERMONSPEAKER_FIELD_SPEAKER_LABEL') . ': ' . $item->speakers; ?>
										<br>
									<?php endif; ?>

									<?php if (in_array('speaker:hits', $this->col_serie) and $item->hits) : ?>
										<?php echo Text::_('JGLOBAL_HITS') . ': ' . $item->hits; ?>
									<?php endif; ?>
								</div>
							</div>
							<?php if (in_array('speaker:description', $this->col_serie) and $item->series_description) : ?>
								<div class="card-footer text-muted">
									<?php echo HTMLHelper::_('content.prepare', $item->series_description); ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if (!empty($this->series)) : ?>
			<?php echo LayoutHelper::render('blocks.pagination', array('view' => 'series', 'pagination' => $this->pag_series, 'params' => $this->params)); ?>
		<?php endif; ?>
		<input type="hidden" name="task" value=""/>
	</form>
</div>
