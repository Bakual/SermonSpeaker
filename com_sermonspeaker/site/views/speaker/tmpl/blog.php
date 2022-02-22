<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
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

HTMLHelper::_('bootstrap.tab');

// Needed for pictures in blog layout
HTMLHelper::_('stylesheet', 'com_sermonspeaker/blog.css', array('relative' => true));

$user             = Factory::getUser();
$showState        = $user->authorise('core.edit', 'com_sermonspeaker');
$fu_enable        = $this->params->get('fu_enable');
$canEdit          = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn       = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$listOrderSeries  = $this->state_series->get('list.ordering');
$listDirnSeries   = $this->state_series->get('list.direction');
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
<div class="com-sermonspeaker-speaker<?php echo $this->pageclass_sfx; ?> com-sermonspeaker-speaker-blog blog" itemscope
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
		<div class="tab-pane active" id="tab_sermons" role="tabpanel">
			<?php if (in_array('speaker:player', $this->col_sermon) and count($this->sermons)) : ?>
				<?php echo LayoutHelper::render('plugin.player', array('player' => $player, 'items' => $this->sermons, 'view' => 'speaker')); ?>
			<?php endif; ?>
			<div class="com-sermonspeaker-speaker-blog__items blog-items">
				<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString() . '#sermons'); ?>"
					  method="post" id="adminForm" name="adminForm" class="com-sermonspeaker-speaker__sermons">
					<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) : ?>
						<?php echo $this->loadTemplate('filters'); ?>
						<?php echo $this->loadTemplate('order'); ?>
					<?php endif; ?>
					<div class="clearfix"></div>
					<?php if (!count($this->sermons)) : ?>
						<div class="alert alert-info">
							<span class="icon-info-circle" aria-hidden="true"></span><span
									class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
							<?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SERMONS')); ?>
						</div>
					<?php else : ?>
						<?php foreach ($this->sermons as $i => $item) : ?>
							<div id="sermon<?php echo $i; ?>"
								 class="<?php echo ($item->state) ? '' : 'system-unpublished'; ?> image-right">
								<div class="com-sermonspeaker-speaker-blog__item blog-item">
									<?php if ($picture = SermonspeakerHelperSermonspeaker::insertPicture($item)) : ?>
										<figure class="item-image sermon-image">
											<img src="<?php echo $picture; ?>" alt="">
										</figure>
									<?php endif; ?>

									<div class="item-content">
										<h2><?php echo SermonspeakerHelperSermonspeaker::insertSermonTitle($i, $item, $player, false); ?></h2>
										<?php if (in_array('speaker:speaker', $this->col_sermon) and $item->speaker_title) : ?>
											<small class="com-sermonspeaker-speaker createdby">
												<?php echo Text::_('COM_SERMONSPEAKER_SPEAKER'); ?>:
												<?php echo LayoutHelper::render('titles.speaker', array('item' => $item, 'params' => $this->params)); ?>
											</small>
										<?php endif; ?>

										<?php if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
											<div class="icons">
												<div class="float-end">
													<?php echo HTMLHelper::_('icon.edit', $item, $this->params, array('type' => 'sermon')); ?>
												</div>
											</div>
										<?php endif; ?>
										<?php echo $item->event->afterDisplayTitle; ?>

										<dl class="article-info sermon-info text-muted">
											<dt class="article-info-term"><?php echo Text::_('JDETAILS'); ?></dt>
											<?php if (in_array('speaker:category', $this->col_sermon) and $item->category_title) : ?>
												<dd>
													<div class="category-name">
														<span class="icon-folder-open icon-fw"></span>
														<?php echo Text::_('JCATEGORY'); ?>:
														<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSermonsRoute($item->catid, $item->language)); ?>"><?php echo $item->category_title; ?></a>
													</div>
												</dd>
											<?php endif; ?>

											<?php if (in_array('speaker:series', $this->col_sermon) and $item->series_title) : ?>
												<dd>
													<div class="ss-sermondetail-info">
														<span class="icon-drawer-2"></span>
														<?php echo Text::_('COM_SERMONSPEAKER_SERIE_TITLE'); ?>:
														<?php if ($item->series_state) : ?>
															<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSerieRoute($item->series_slug, $item->series_catid, $item->series_language)); ?>">
																<?php echo $this->escape($item->series_title); ?></a>
														<?php else : ?>
															<?php echo $this->escape($item->series_title); ?>
														<?php endif; ?>
													</div>
												</dd>
											<?php endif; ?>

											<?php if (in_array('speaker:date', $this->col_sermon) and ($item->sermon_date != '0000-00-00 00:00:00')) : ?>
												<dd>
													<div class="create">
														<span class="icon-calendar"></span>
														<?php echo Text::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'); ?>:
														<?php echo HTMLHelper::date($item->sermon_date, Text::_($this->params->get('date_format')), true); ?>
													</div>
												</dd>
											<?php endif; ?>

											<?php if (in_array('speaker:hits', $this->col_sermon)) : ?>
												<dd>
													<div class="hits">
														<span class="icon-eye-open"></span>
														<?php echo Text::_('JGLOBAL_HITS'); ?>:
														<?php echo $item->hits; ?>
													</div>
												</dd>
											<?php endif; ?>

											<?php if (in_array('speaker:scripture', $this->col_sermon) and $item->scripture) : ?>
												<dd>
													<div class="ss-sermondetail-info">
														<span class="icon-quote"></span>
														<?php echo Text::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?>:
														<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '; '); ?>
														<?php echo HTMLHelper::_('content.prepare', $scriptures, '', 'com_sermonspeaker.scripture'); ?>
													</div>
												</dd>
											<?php endif; ?>

											<?php if (in_array('speaker:length', $this->col_sermon) and $item->sermon_time != '00:00:00') : ?>
												<dd>
													<div class="ss-sermondetail-info">
														<span class="icon-clock"></span>
														<?php echo Text::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL'); ?>:
														<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
													</div>
												</dd>
											<?php endif; ?>

											<?php if (in_array('speaker:addfile', $this->col_sermon) and $item->addfile) : ?>
												<dd>
													<div class="ss-sermondetail-info">
														<?php echo Text::_('COM_SERMONSPEAKER_ADDFILE'); ?>:
														<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($item->addfile, $item->addfileDesc); ?>
													</div>
												</dd>
											<?php endif; ?>

											<?php if ($playerid = !empty($player->id) ? $player->id : '') : ?>
												<dd>
													<div class="ss-sermondetail-info">
														<span class="icon-play"></span>
														<?php echo HTMLHelper::_('icon.play', $item, $this->params, array('index' => $i, 'playerid' => $playerid)); ?>
													</div>
												</dd>
											<?php endif; ?>

											<?php if (in_array('speaker:download', $this->col_sermon)) : ?>
												<?php if ($item->audiofile) : ?>
													<dd>
														<div class="ss-sermondetail-info">
															<span class="icon-download"></span>
															<?php echo HTMLHelper::_('icon.download', $item, $this->params, array('type' => 'audio', 'hideIcon' => true)); ?>
														</div>
													</dd>
												<?php endif; ?>

												<?php if ($item->videofile) : ?>
													<dd>
														<div class="ss-sermondetail-info">
															<span class="download-icon"></span>
															<?php echo HTMLHelper::_('icon.download', $item, $this->params, array('type' => 'video', 'hideIcon' => true)); ?>
														</div>
													</dd>
												<?php endif; ?>
											<?php endif; ?>
										</dl>

										<?php echo $item->event->beforeDisplayContent; ?>

										<?php if (in_array('speaker:notes', $this->col_sermon) and $item->notes) : ?>
											<div>
												<?php echo HTMLHelper::_('content.prepare', $item->notes, '', 'com_sermonspeaker.notes'); ?>
											</div>
										<?php endif; ?>

										<?php echo $item->event->afterDisplayContent; ?>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>

					<?php if (!empty($this->sermons)) : ?>
						<?php echo LayoutHelper::render('blocks.pagination', array('view' => 'sermons', 'pagination' => $this->pag_sermons, 'params' => $this->params)); ?>
					<?php endif; ?>
					<input type="hidden" name="task" value=""/>
					<input type="hidden" name="limitstart" value=""/>
				</form>
			</div>
		</div>
		<div class="tab-pane" id="tab_series" role="tabpanel">
			<div class="com-sermonspeaker-speaker-blog__items blog-item">
				<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString() . '#series'); ?>"
					  method="post" id="adminFormSeries" name="adminFormSeries" class="com-sermonspeaker-speaker__series">
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
						<?php foreach ($this->series as $i => $item) : ?>
							<div id="serie<?php echo $i; ?>"
								 class="<?php echo ($item->state) ? '' : 'system-unpublished'; ?> image-right">
								<div class="com-sermonspeaker-series-blog__item blog-item">
									<?php if ($item->avatar) : ?>
										<figure class="item-image serie-image">
											<img src="<?php echo SermonspeakerHelperSermonspeaker::makeLink($item->avatar); ?>" alt="">
										</figure>
									<?php endif; ?>

									<div class="item-content">
										<h2><?php echo $item->title; ?></h2>
										<?php if (in_array('speaker:speaker', $this->col_serie) and $item->speakers) : ?>
											<small class="com-sermonspeaker-speaker createdby">
												<?php echo Text::_('COM_SERMONSPEAKER_SPEAKERS'); ?>:
												<?php echo $item->speakers; ?>
											</small>
										<?php endif; ?>

										<?php if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
											<div class="icons">
												<div class="float-end">
													<?php echo HTMLHelper::_('icon.edit', $item, $this->params, array('type' => 'serie')); ?>
												</div>
											</div>
										<?php endif; ?>
										<?php echo $item->event->afterDisplayTitle; ?>

										<dl class="article-info serie-info text-muted">
											<dt class="article-info-term"><?php echo Text::_('JDETAILS'); ?></dt>
											<?php if (in_array('speaker:category', $this->col_serie) and $item->category_title) : ?>
												<dd>
													<div class="category-name">
														<span class="icon-folder-open icon-fw"></span>
														<?php echo Text::_('JCATEGORY'); ?>:
														<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSeriesRoute($item->catslug, $item->language)); ?>"><?php echo $item->category_title; ?></a>
													</div>
												</dd>
											<?php endif; ?>

											<?php if (in_array('speaker:hits', $this->col_serie)) : ?>
												<dd>
													<div class="hits">
														<span class="icon-eye-open"></span>
														<?php echo Text::_('JGLOBAL_HITS'); ?>:
														<?php echo $item->hits; ?>
													</div>
												</dd>
											<?php endif; ?>

											<?php if (in_array('speaker:download', $this->col_serie)) : ?>
												<dd>
													<div class="ss-sermondetail-info">
														<span class="icon-download"></span>
														<?php $url = Route::_('index.php?view=serie&layout=download&tmpl=component&id=' . $item->slug); ?>
														<?php $downloadText = Text::_('COM_SERMONSPEAKER_DOWNLOADSERIES_LABEL'); ?>
														<?php $modalOptions = array('url' => $url, 'height' => 200, 'width' => 400, 'title' => $downloadText); ?>
														<?php echo HTMLHelper::_('bootstrap.rendermodal', 'downloadModal' . $i, $modalOptions); ?>
														<a href="#downloadModal<?php echo $i; ?>" class="downloadModal" data-bs-toggle="modal">
															<?php echo $downloadText; ?>
														</a>
													</div>
												</dd>
											<?php endif; ?>
										</dl>

										<?php echo $item->event->beforeDisplayContent; ?>

										<?php if (in_array('speaker:description', $this->col_serie) and $item->series_description) : ?>
											<div>
												<?php echo HTMLHelper::_('content.prepare', $item->series_description, '', 'com_sermonspeaker.series_description'); ?>
											</div>
										<?php endif; ?>

										<?php echo $item->event->afterDisplayContent; ?>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>

					<?php if (!empty($this->series)) : ?>
						<?php echo LayoutHelper::render('blocks.pagination', array('view' => 'series', 'pagination' => $this->pag_series, 'params' => $this->params)); ?>
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
