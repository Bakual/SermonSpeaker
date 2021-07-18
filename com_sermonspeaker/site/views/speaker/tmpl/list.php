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
HTMLHelper::_('bootstrap.tab');

$user             = Factory::getUser();
$showState        = $user->authorise('core.edit', 'com_sermonspeaker');
$fu_enable        = $this->params->get('fu_enable');
$canEdit          = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn       = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$listOrderSermons = $this->state_sermons->get('list.ordering');
$listDirnSermons  = $this->state_sermons->get('list.direction');
$listOrderSeries  = $this->state_series->get('list.ordering');
$listDirnSeries   = $this->state_series->get('list.direction');
$limit            = (int) $this->params->get('limit', '');
$player           = SermonspeakerHelperSermonspeaker::getPlayer($this->sermons);

// Determine active tab
$this->document->addScriptDeclaration("window.onload = function() {
		let tab = 'tabber_sermons';
		if (location.hash == '#series') {
			tab = 'tabber_series';
		}
		let bootstrapTab = new bootstrap.Tab(document.getElementById(tab));
		bootstrapTab.show();
	}");
?>
<div class="com-sermonspeaker-speaker<?php echo $this->pageclass_sfx; ?> com-sermonspeaker-speaker-list" itemscope
	 itemtype="http://schema.org/Person">
	<?php echo $this->loadTemplate('header'); ?>
	<div class="clearfix"></div>

	<ul class="nav nav-tabs" id="speakerTab" role="tablist">
		<li class="nav-item">
			<a href="#tab_sermons" id="tabber_sermons" class="nav-link active" data-bs-toggle="tab" role="tab">
				<?php echo Text::_('COM_SERMONSPEAKER_SERMONS'); ?></a>
		</li>
		<li class="nav-item">
			<a href="#tab_series" id="tabber_series" class="nav-link" data-bs-toggle="tab" role="tab">
				<?php echo Text::_('COM_SERMONSPEAKER_SERIES'); ?></a>
		</li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="tab_sermons">
			<?php if (in_array('speaker:player', $this->col_sermon) and count($this->sermons)) : ?>
				<?php echo LayoutHelper::render('plugin.player', array('player' => $player, 'items' => $this->sermons, 'view' => 'speaker')); ?>
			<?php endif; ?>
			<div class="cat-items">
				<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString() . '#sermons'); ?>"
					  method="post" name="adminForm" id="adminForm" class="com-sermonspeaker-speaker__sermons">
					<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) : ?>
						<?php echo $this->loadTemplate('filters'); ?>
					<?php endif; ?>
					<div class="clearfix"></div>
					<?php if (!count($this->sermons)) : ?>
						<span class="icon-info-circle" aria-hidden="true"></span><span
								class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
						<?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SERMONS')); ?>
					<?php else : ?>
						<ul class="list-group list-group-flush">
							<?php foreach ($this->sermons as $i => $item) :
								$sep = 0; ?>
								<li id="sermon<?php echo $i; ?>"
									class="<?php echo ($item->state) ? '' : 'system-unpublished '; ?>cat-list-row<?php echo $i % 2; ?> list-group-item">
									<?php if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
										<span class="list-edit">
											<?php echo LayoutHelper::render('icons.edit', ['item' => $item, 'params' => $this->params, 'type' => 'sermon']); ?>
										</span>
									<?php endif; ?>
									<strong class="ss-title">
										<?php echo SermonspeakerHelperSermonspeaker::insertSermonTitle($i, $item, $player); ?>
									</strong>
									<?php echo LayoutHelper::render('blocks.state_info', array('item' => $item, 'show' => $showState)); ?>
									<?php if (in_array('speaker:hits', $this->col_sermon)) : ?>
										<span class="ss-hits badge bg-info float-end">
											<?php echo Text::sprintf('JGLOBAL_HITS_COUNT', $item->hits); ?>
										</span>
									<?php endif; ?>
									<br/>
									<?php if (in_array('speaker:series', $this->col_sermon) and $item->series_title) :
										if ($sep) : ?>
											|
										<?php endif;
										$sep = 1; ?>
										<small class="ss-series">
											<?php echo Text::_('COM_SERMONSPEAKER_SERIE'); ?>:
											<?php
											if ($item->series_state): ?>
												<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSerieRoute($item->series_slug, $item->series_catid, $item->series_language)); ?>"><?php echo $item->series_title; ?></a>
											<?php else :
												echo $item->series_title;
											endif; ?>
										</small>
									<?php endif;

									if (in_array('speaker:length', $this->col_sermon) and $item->sermon_time != '00:00:00') :
										if ($sep) : ?>
											|
										<?php endif;
										$sep = 1; ?>
										<small class="ss-length">
											<?php echo Text::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL'); ?>:
											<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
										</small>
									<?php endif;

									if (in_array('speaker:scripture', $this->col_sermon) and $item->scripture) :
										if ($sep) : ?>
											|
										<?php endif;
										$sep = 1; ?>
										<small class="ss-scripture">
											<?php echo Text::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?>:
											<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '; ');
											echo HTMLHelper::_('content.prepare', $scriptures, '', 'com_sermonspeaker.scripture'); ?>
										</small>
									<?php endif;

									if (in_array('speaker:date', $this->col_sermon) and ($item->sermon_date != '0000-00-00 00:00:00')) : ?>
										<span class="ss-date float-end">
											<small class="text-muted">
												<?php echo HTMLHelper::date($item->sermon_date, Text::_($this->params->get('date_format')), true); ?>
											</small>
										</span>&nbsp;
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif;

					if ($user->authorise('core.create', 'com_sermonspeaker')) :
						echo HTMLHelper::_('icon.create', $this->category, $this->params);
					endif;

					if ($this->params->get('show_pagination') and ($this->pag_sermons->pagesTotal > 1)) : ?>
						<div class="pagination">
							<?php if ($this->params->get('show_pagination_results', 1)) : ?>
								<p class="counter pull-right">
									<?php echo $this->pag_sermons->getPagesCounter(); ?>
								</p>
							<?php endif;
							echo $this->pag_sermons->getPagesLinks(); ?>
						</div>
					<?php endif; ?>
					<input type="hidden" name="task" value=""/>
					<input type="hidden" name="filter_order" value="<?php echo $listOrderSermons; ?>"/>
					<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirnSermons; ?>"/>
					<input type="hidden" name="limitstart" value=""/>
				</form>
			</div>
		</div>
		<div class="tab-pane" id="tab_series">
			<div class="cat-items">
				<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString() . '#series'); ?>" method="post"
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
						<div class="alert alert-info">
							<span class="icon-info-circle" aria-hidden="true"></span><span
									class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
							<?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SERIES')); ?>
						</div>
					<?php else : ?>
						<ul class="list-group list-group-flush">
							<?php foreach ($this->series as $i => $item) :
								$sep = 0; ?>
								<li class="<?php echo ($item->state) ? '' : 'system-unpublished '; ?>cat-list-row<?php echo $i % 2; ?> list-group-item">
									<?php if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
										<span class="list-edit"><?php echo LayoutHelper::render('icons.edit', ['item' => $item, 'params' => $this->params, 'type' => 'serie']); ?></span>
									<?php endif; ?>
									<strong class="ss-title">
										<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSerieRoute($item->slug, $item->catid, $item->language)); ?>">
											<?php echo $item->title; ?>
										</a>
									</strong>
									<?php echo LayoutHelper::render('blocks.state_info', array('item' => $item, 'show' => $showState)); ?>
									<?php if (in_array('speaker:hits', $this->col_serie)) : ?>
										<span class="ss-hits badge bg-info float-end">
											<?php echo Text::sprintf('JGLOBAL_HITS_COUNT', $item->hits); ?>
										</span>
									<?php endif; ?>
									<br/>
									<?php if (in_array('speaker:speaker', $this->col_serie) and $item->speakers) : ?>
										<small class="ss-speakers">
											<?php echo Text::_('COM_SERMONSPEAKER_SPEAKERS'); ?>:
											<?php echo $item->speakers; ?>
										</small>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif;

					if ($this->params->get('show_pagination') and ($this->pag_series->pagesTotal > 1)) : ?>
						<div class="pagination">
							<?php if ($this->params->get('show_pagination_results', 1)) : ?>
								<p class="counter float-end">
									<?php echo $this->pag_series->getPagesCounter(); ?>
								</p>
							<?php endif;
							echo $this->pag_series->getPagesLinks(); ?>
						</div>
					<?php endif; ?>
					<input type="hidden" name="task" value=""/>
					<input type="hidden" name="filter_order" value="<?php echo $listOrderSeries; ?>"/>
					<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirnSeries; ?>"/>
					<input type="hidden" name="limitstart" value=""/>
				</form>
			</div>
		</div>
	</div>
</div>
