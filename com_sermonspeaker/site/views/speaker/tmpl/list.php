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
<div class="com-sermonspeaker-speaker<?php echo $this->pageclass_sfx; ?> com-sermonspeaker-speaker-list" itemscope itemtype="http://schema.org/Person">
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
			<?php if (in_array('speaker:player', $this->col_sermon) and count($this->sermons)) :
				HTMLHelper::_('stylesheet', 'com_sermonspeaker/player.css', array('relative' => true)); ?>
				<div id="ss-speaker-player" class="ss-player row-fluid">
					<div class="span10 offset1">
						<hr/>
						<?php if ($player->player != 'PixelOut') : ?>
							<div id="playing">
								<img id="playing-pic" class="picture" src="" alt=""/>
								<span id="playing-duration" class="duration"></span>
								<div class="text">
									<span id="playing-title" class="title"></span>
									<span id="playing-desc" class="desc"></span>
								</div>
								<span id="playing-error" class="error"></span>
							</div>
						<?php endif;
						echo $player->mspace;
						echo $player->script;
						?>
						<hr/>
						<?php if ($player->toggle) : ?>
                            <div class="row">
                                <div class="mx-auto btn-group">
                                    <button type="button" onclick="Video()" class="btn btn-secondary" title="<?php echo Text::_('COM_SERMONSPEAKER_SWITCH_VIDEO'); ?>">
                                        <span class="fas fa-film fa-4x"></span>
                                    </button>
                                    <button type="button" onclick="Audio()" class="btn btn-secondary" title="<?php echo Text::_('COM_SERMONSPEAKER_SWITCH_AUDIO'); ?>">
                                        <span class="fas fa-music fa-4x"></span>
                                    </button>
                                </div>
                            </div>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
			<div class="cat-items">
				<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString() . '#sermons'); ?>"
					method="post" id="adminForm" name="adminForm" class="form-inline">
					<?php
					if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) :
						echo $this->loadTemplate('filters');
					endif; ?>
					<div class="clearfix"></div>
					<?php if (!count($this->sermons)) : ?>
						<div
							class="no_entries alert alert-error"><?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
					<?php else : ?>
						<ul class="category list-striped list-condensed">
							<?php foreach ($this->sermons as $i => $item) :
								$sep = 0; ?>
								<li id="sermon<?php echo $i; ?>"
									class="<?php echo ($item->state) ? '' : 'system-unpublished '; ?>cat-list-row<?php echo $i % 2; ?>">
									<?php
									if (in_array('speaker:hits', $this->col_sermon)) : ?>
										<span class="ss-hits badge bg-info pull-right">
											<?php echo Text::sprintf('JGLOBAL_HITS_COUNT', $item->hits); ?>
										</span>
									<?php endif;

									if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
										<span class="list-edit"><?php echo LayoutHelper::render('icons.edit', ['item' => $item, 'params' => $this->params, 'type' => 'sermon', 'hide_text' => true]); ?></span>
									<?php endif; ?>
									<strong class="ss-title">
										<?php echo SermonspeakerHelperSermonspeaker::insertSermonTitle($i, $item, $player); ?>
									</strong>
									<?php echo JLayoutHelper::render('blocks.state_info', array('item' => $item, 'show' => $showState)); ?>
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
												<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->series_slug, $item->series_catid, $item->series_language)); ?>"><?php echo $item->series_title; ?></a>
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
										<span class="ss-date small pull-right">
											<?php echo HTMLHelper::date($item->sermon_date, Text::_($this->params->get('date_format')), true); ?>
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
				<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString() . '#series'); ?>"
					method="post" id="adminFormSeries" name="adminFormSeries">
					<?php
					if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) : ?>
						<div class="filters btn-toolbar">
							<?php if ($this->params->get('show_pagination_limit')) : ?>
								<div class="btn-group pull-right">
									<label class="element-invisible">
										<?php echo Text::_('JGLOBAL_DISPLAY_NUM'); ?>
									</label>
									<?php echo $this->pag_series->getLimitBox(); ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<div class="clearfix"></div>
					<?php if (!count($this->series)) : ?>
						<div
							class="no_entries alert alert-error"><?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SERIES')); ?></div>
					<?php else : ?>
						<ul class="category list-striped list-condensed">
							<?php foreach ($this->series as $i => $item) :
								$sep = 0; ?>
								<li class="<?php echo ($item->state) ? '' : 'system-unpublished '; ?>cat-list-row<?php echo $i % 2; ?>">
									<?php
									if (in_array('speaker:hits', $this->col_serie)) : ?>
										<span class="ss-hits badge bg-info pull-right">
											<?php echo Text::sprintf('JGLOBAL_HITS_COUNT', $item->hits); ?>
										</span>
									<?php endif;

									if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
										<span class="list-edit"><?php echo LayoutHelper::render('icons.edit', ['item' => $item, 'params' => $this->params, 'type' => 'serie', 'hide_text' => true]); ?></span>
									<?php endif; ?>
									<strong class="ss-title">
										<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->slug, $item->catid, $item->language)); ?>">
											<?php echo $item->title; ?>
										</a>
									</strong>
									<?php echo JLayoutHelper::render('blocks.state_info', array('item' => $item, 'show' => $showState)); ?>
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
								<p class="counter pull-right">
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
