<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

HtmlHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

HtmlHelper::_('jquery.framework');
HtmlHelper::_('bootstrap.tooltip');

// Needed for pictures in blog layout
HtmlHelper::_('stylesheet', 'com_sermonspeaker/blog.css', array('relative' => true));

$user             = JFactory::getUser();
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
$this->document->addScriptDeclaration('jQuery(function() {
		if (location.hash == \'#series\') {
			tab = \'#tab_series\';
		} else {
			tab = \'#tab_sermons\';
		}
		jQuery(\'#speakerTab a[href="\' + tab + \'"]\').tab(\'show\');
	})');
?>
<div class="category-list<?php echo $this->pageclass_sfx; ?> ss-speaker-container<?php echo $this->pageclass_sfx; ?>"
	itemscope itemtype="http://schema.org/Person">
	<?php
	if ($this->params->get('show_page_heading', 1)) : ?>
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>
	<div class="<?php echo ($this->item->state) ? '' : 'system-unpublished'; ?>">
		<div class="btn-group pull-right">
			<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				<i class="icon-cog"></i>
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
				<?php
				if ($canEdit or ($canEditOwn and ($user->id == $this->item->created_by))) : ?>
					<li class="edit-icon"><?php echo HtmlHelper::_('icon.edit', $this->item, $this->params, array('type' => 'speaker')); ?></li>
				<?php endif; ?>
			</ul>
		</div>
		<?php echo JLayoutHelper::render('blocks.speaker', array('item' => $this->item, 'params' => $this->params, 'columns' => $this->columns)); ?>
	</div>
	<div class="clearfix"></div>
	<ul class="nav nav-tabs" id="speakerTab" role="tablist">
		<li class="nav-link-item">
            <a href="#tab_sermons" class="nav-link" data-toggle="tab" role="tab"><?php echo Text::_('COM_SERMONSPEAKER_SERMONS'); ?></a>
        </li>
		<li class="nav-link-item">
            <a href="#tab_series" class="nav-link" data-toggle="tab" role="tab"><?php echo Text::_('COM_SERMONSPEAKER_SERIES'); ?></a>
        </li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="tab_sermons" role="tabpanel">
			<?php if (in_array('speaker:player', $this->col_sermon) and count($this->sermons)) : ?>
				<?php echo LayoutHelper::render('plugin.player', array('player' => $player, 'items' => $this->sermons, 'view' => 'speaker')); ?>
			<?php endif; ?>
			<div class="cat-items">
				<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString() . '#sermons'); ?>" method="post" id="adminForm" name="adminForm">
					<?php $this->params->set('filter_field', 1); ?>
					<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) : ?>
						<?php echo $this->loadTemplate('filters'); ?>
					<?php endif; ?>
					<div class="clearfix"></div>
					<?php if (!count($this->sermons)) : ?>
						<div
							class="no_entries alert alert-error"><?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
					<?php else : ?>
						<div class="items-leading">
							<?php foreach ($this->sermons as $i => $item) : ?>
								<div id="sermon<?php echo $i; ?>"
									class="clearfix<?php echo ($item->state) ? '' : ' system-unpublished'; ?>">
									<div class="btn-group pull-right">
										<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
											<i class="icon-cog"></i>
											<span class="caret"></span>
										</a>
										<ul class="dropdown-menu">
											<?php if ($playerid = !empty($player->id) ? $player->id : '') : ?>
												<li class="play-icon"><?php echo HtmlHelper::_('icon.play', $item, $this->params, array('index' => $i, 'playerid' => $playerid)); ?></li>
											<?php endif; ?>
											<?php
											if (in_array('speaker:download', $this->col_sermon)) :
												if ($item->audiofile) : ?>
													<li class="download-icon"><?php echo HtmlHelper::_('icon.download', $item, $this->params, array('type' => 'audio')); ?></li>
												<?php endif;

												if ($item->videofile) : ?>
													<li class="download-icon"><?php echo HtmlHelper::_('icon.download', $item, $this->params, array('type' => 'video')); ?></li>
												<?php endif; ?>
											<?php endif; ?>
											<?php
											if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
												<li class="edit-icon"><?php echo HtmlHelper::_('icon.edit', $item, $this->params, array('type' => 'sermon')); ?></li>
											<?php endif; ?>
										</ul>
									</div>
									<div class="page-header">
										<h2><?php echo SermonspeakerHelperSermonspeaker::insertSermonTitle($i, $item, $player, false); ?></h2>
										<?php echo JLayoutHelper::render('blocks.state_info', array('item' => $item, 'show' => $showState)); ?>
									</div>

									<?php echo $item->event->afterDisplayTitle; ?>

									<?php if ($picture = SermonspeakerHelperSermonspeaker::insertPicture($item)) : ?>
										<div class="img-polaroid pull-right item-image sermon-image"><img
												src="<?php echo $picture; ?>"></div>
									<?php endif; ?>
									<div class="article-info sermon-info muted">
										<dl class="article-info">
											<dt class="article-info-term"><?php echo Text::_('JDETAILS'); ?></dt>
											<?php
											if (in_array('speaker:category', $this->col_sermon) and $item->category_title) : ?>
												<dd>
													<div class="category-name">
														<?php echo Text::_('JCATEGORY'); ?>:
														<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonsRoute($item->catslug, $item->language)); ?>"><?php echo $item->category_title; ?></a>
													</div>
												</dd>
											<?php endif;

											if (in_array('speaker:series', $this->col_sermon) and $item->series_title) : ?>
												<dd>
													<div class="ss-sermondetail-info">
														<span class="icon-drawer-2"></span>
														<?php echo Text::_('COM_SERMONSPEAKER_SERIE_TITLE'); ?>:
														<?php
														if ($item->series_state) : ?>
															<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->series_slug, $item->series_catid, $item->series_language)); ?>"><?php echo $this->escape($item->series_title); ?></a>
														<?php else :
															echo $this->escape($item->series_title);
														endif; ?>
													</div>
												</dd>
											<?php endif;

											if (in_array('speaker:date', $this->col_sermon) and ($item->sermon_date != '0000-00-00 00:00:00')) : ?>
												<dd>
													<div class="create">
														<i class="icon-calendar"></i>
														<?php echo Text::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'); ?>:
														<?php echo HtmlHelper::date($item->sermon_date, Text::_($this->params->get('date_format')), true); ?>
													</div>
												</dd>
											<?php endif;

											if (in_array('speaker:hits', $this->col_sermon)) : ?>
												<dd>
													<div class="hits">
														<i class="icon-eye-open"></i>
														<?php echo Text::_('JGLOBAL_HITS'); ?>:
														<?php echo $item->hits; ?>
													</div>
												</dd>
											<?php endif;

											if (in_array('speaker:scripture', $this->col_sermon) and $item->scripture) : ?>
												<dd>
													<div class="ss-sermondetail-info">
														<i class="icon-quote"></i>
														<?php echo Text::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?>
														:
														<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '; ');
														echo HtmlHelper::_('content.prepare', $scriptures, '', 'com_sermonspeaker.scripture'); ?>
													</div>
												</dd>
											<?php endif;

											if (in_array('speaker:length', $this->col_sermon) and $item->sermon_time != '00:00:00') : ?>
												<dd>
													<div class="ss-sermondetail-info">
														<i class="icon-clock"></i>
														<?php echo Text::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL'); ?>:
														<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
													</div>
												</dd>
											<?php endif;

											if (in_array('speaker:addfile', $this->col_sermon) and $item->addfile) : ?>
												<dd>
													<div class="ss-sermondetail-info">
														<?php echo Text::_('COM_SERMONSPEAKER_ADDFILE'); ?>:
														<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($item->addfile, $item->addfileDesc); ?>
													</div>
												</dd>
											<?php endif; ?>
										</dl>
									</div>

									<?php echo $item->event->beforeDisplayContent; ?>

									<?php if (in_array('speaker:notes', $this->col_sermon) and $item->notes) : ?>
										<div>
											<?php echo HtmlHelper::_('content.prepare', $item->notes, '', 'com_sermonspeaker.notes'); ?>
										</div>
									<?php endif; ?>

									<?php echo $item->event->afterDisplayContent; ?>

								</div>
							<?php endforeach; ?>
						</div>
					<?php endif;

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
		<div class="tab-pane" id="tab_series" role="tabpanel">
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
						<div class="items-leading">
							<?php foreach ($this->series as $i => $item) : ?>
								<div class="clearfix<?php echo ($item->state) ? '' : ' system-unpublished'; ?>">
									<div class="btn-group pull-right">
										<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
											<i class="icon-cog"></i>
											<span class="caret"></span>
										</a>
										<ul class="dropdown-menu">
											<?php if (in_array('speaker:download', $this->col_serie)) : ?>
												<li class="download-icon">
													<a href="<?php echo JRoute::_('index.php?view=serie&layout=download&tmpl=component&id=' . $item->slug); ?>"
														class="modal" rel="{handler:'iframe',size:{x:400,y:200}}">
														<i class="icon-download"> </i>
														<?php echo Text::_('COM_SERMONSPEAKER_DOWNLOADSERIES_LABEL'); ?>
													</a>
												</li>
											<?php endif; ?>
											<?php
											if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
												<li class="edit-icon"><?php echo HtmlHelper::_('icon.edit', $item, $this->params, array('type' => 'serie')); ?></li>
											<?php endif; ?>
										</ul>
									</div>
									<div class="page-header">
										<a title="<?php echo Text::_('COM_SERMONSPEAKER_SERIESLINK_HOOVER'); ?>"
											href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->slug, $item->catid, $item->language)); ?>">
											<h2><?php echo $item->title; ?></h2>
										</a>
										<?php echo JLayoutHelper::render('blocks.state_info', array('item' => $item, 'show' => $showState)); ?>

										<?php if (in_array('speaker:speaker', $this->col_serie) and $item->speakers) : ?>
											<small class="ss-speakers createdby">
												<?php echo Text::_('COM_SERMONSPEAKER_SPEAKERS'); ?>:
												<?php echo $item->speakers; ?>
											</small>
										<?php endif; ?>
									</div>

									<?php echo $item->event->afterDisplayTitle; ?>

									<?php if ($item->avatar) : ?>
										<div class="img-polaroid pull-right item-image">
											<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($item->slug, $item->catid, $item->language)); ?>">
												<img
													src="<?php echo SermonspeakerHelperSermonspeaker::makeLink($item->avatar); ?>">
											</a>
										</div>
									<?php endif; ?>
									<div class="article-info serie-info muted">
										<dl class="article-info">
											<dt class="article-info-term"><?php echo Text::_('JDETAILS'); ?></dt>
											<?php
											if (in_array('speaker:category', $this->col_serie) and $item->category_title) : ?>
												<dd>
													<div class="category-name">
														<?php echo Text::_('JCATEGORY'); ?>:
														<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSeriesRoute($item->catslug, $item->language)); ?>"><?php echo $item->category_title; ?></a>
													</div>
												</dd>
											<?php endif;

											if (in_array('speaker:hits', $this->col_serie)) : ?>
												<dd>
													<div class="hits">
														<i class="icon-eye-open"></i>
														<?php echo Text::_('JGLOBAL_HITS'); ?>:
														<?php echo $item->hits; ?>
													</div>
												</dd>
											<?php endif; ?>
										</dl>
									</div>

									<?php echo $item->event->beforeDisplayContent; ?>

									<?php if (in_array('speaker:description', $this->col_serie) and $item->series_description) : ?>
										<div>
											<?php echo HtmlHelper::_('content.prepare', $item->series_description, '', 'com_sermonspeaker.series_description'); ?>
										</div>
									<?php endif; ?>

									<?php echo $item->event->afterDisplayContent; ?>

								</div>
							<?php endforeach; ?>
						</div>
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
