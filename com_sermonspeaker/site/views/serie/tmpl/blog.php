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

HtmlHelper::addIncludePath(JPATH_COMPONENT . '/helpers');

HtmlHelper::_('jquery.framework');
HtmlHelper::_('bootstrap.tooltip');

// Needed for pictures in blog layout
HtmlHelper::_('stylesheet', 'com_sermonspeaker/blog.css', array('relative' => true));

$user       = JFactory::getUser();
$showState  = $user->authorise('core.edit', 'com_sermonspeaker');
$fu_enable  = $this->params->get('fu_enable');
$canEdit    = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$limit      = (int) $this->params->get('limit', '');
$player     = SermonspeakerHelperSermonspeaker::getPlayer($this->items);
?>
<div class="category-list<?php echo $this->pageclass_sfx; ?> ss-serie-container<?php echo $this->pageclass_sfx; ?>">
	<?php
	if ($this->params->get('show_page_heading', 1)) : ?>
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>
	<div class="<?php echo ($this->item->state) ? '' : 'system-unpublished'; ?>">
		<div class="btn-group pull-right">
			<a class="btn dropdown-toggle" data-bs-toggle="dropdown" href="#">
				<i class="icon-cog"></i>
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
				<?php if (in_array('serie:download', $this->col_serie)) : ?>
					<li class="download-icon">
						<a href="<?php echo JRoute::_('index.php?view=serie&layout=download&tmpl=component&id=' . $this->item->slug); ?>"
							class="modal" rel="{handler:'iframe',size:{x:400,y:200}}">
							<i class="icon-download"> </i>
							<?php echo Text::_('COM_SERMONSPEAKER_DOWNLOADSERIES_LABEL'); ?>
						</a>
					</li>
				<?php endif; ?>
				<?php
				if ($canEdit or ($canEditOwn and ($user->id == $this->item->created_by))) : ?>
					<li class="edit-icon"><?php echo HtmlHelper::_('icon.edit', $this->item, $this->params, array('type' => 'serie')); ?></li>
				<?php endif; ?>
			</ul>
		</div>
		<div class="page-header">
			<h2><?php echo $this->item->title; ?></h2>
			<?php echo JLayoutHelper::render('blocks.state_info', array('item' => $this->item, 'show' => $showState)); ?>

			<?php if (in_array('serie:speaker', $this->col_serie) and $this->item->speakers) : ?>
				<small class="ss-speakers createdby">
					<?php echo Text::_('COM_SERMONSPEAKER_SPEAKERS'); ?>:
					<?php echo $this->item->speakers; ?>
				</small>
			<?php endif; ?>
		</div>
		<?php if ($this->item->avatar) : ?>
			<div class="img-polaroid pull-right item-image">
				<img src="<?php echo SermonspeakerHelperSermonspeaker::makeLink($this->item->avatar); ?>">
			</div>
		<?php endif; ?>
		<div class="article-info serie-info muted">
			<dl class="article-info">
				<dt class="article-info-term"><?php echo Text::_('JDETAILS'); ?></dt>
				<?php
				if (in_array('serie:category', $this->col_serie) and $this->item->category_title) : ?>
					<dd>
						<div class="category-name">
							<?php echo Text::_('JCATEGORY'); ?>:
							<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSeriesRoute($this->item->catid, $this->item->language)); ?>"><?php echo $this->item->category_title; ?></a>
						</div>
					</dd>
				<?php endif;

				if (in_array('serie:hits', $this->col_serie)) : ?>
					<dd>
						<div class="hits">
							<i class="icon-eye-open"></i>
							<?php echo Text::_('JGLOBAL_HITS'); ?>:
							<?php echo $this->item->hits; ?>
						</div>
					</dd>
				<?php endif; ?>
			</dl>
		</div>
		<?php if ($this->params->get('show_tags', 1) and !empty($this->item->tags->itemTags)) :
			$tagLayout = new JLayoutFile('joomla.content.tags');
			echo $tagLayout->render($this->item->tags->itemTags); ?>
		<?php endif; ?>
		<?php echo $this->item->event->afterDisplayTitle; ?>
		<?php echo $this->item->event->beforeDisplayContent; ?>
		<?php if (in_array('serie:description', $this->col_serie) and $this->item->series_description) : ?>
			<div>
				<?php echo HtmlHelper::_('content.prepare', $this->item->series_description, '', 'com_sermonspeaker.description'); ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="clearfix"></div>
	<?php if (in_array('serie:player', $this->columns) and count($this->items)) :
		HtmlHelper::_('stylesheet', 'com_sermonspeaker/player.css', array('relative' => true)); ?>
		<div id="ss-serie-player" class="ss-player row-fluid">
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
	<?php echo $this->item->event->afterDisplayContent; ?>
	<div class="cat-items">
		<form action="<?php echo htmlspecialchars(JUri::getInstance()->toString()); ?>" method="post" id="adminForm"
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
				<div class="items-leading">
					<?php foreach ($this->items as $i => $item) : ?>
						<div id="sermon<?php echo $i; ?>"
							class="<?php echo ($item->state) ? '' : 'system-unpublished'; ?>">
							<div class="btn-group pull-right">
								<a class="btn dropdown-toggle" data-bs-toggle="dropdown" href="#">
									<i class="icon-cog"></i>
									<span class="caret"></span>
								</a>
								<ul class="dropdown-menu">
									<?php if ($playerid = !empty($player->id) ? $player->id : '') : ?>
										<li class="play-icon"><?php echo HtmlHelper::_('icon.play', $item, $this->params, array('index' => $i, 'playerid' => $playerid)); ?></li>
									<?php endif; ?>
									<?php
									if (in_array('serie:download', $this->columns)) :
										if ($item->audiofile) : ?>
											<li class="download-icon"><?php echo HtmlHelper::_('icon.download', $item, $this->params, array('type' => 'audio')); ?></li>
										<?php endif;

										if ($item->videofile) : ?>
											<li class="download-icon"><?php echo HtmlHelper::_('icon.download', $item, $this->params, array('type' => 'video')); ?></li>
										<?php endif;
									endif; ?>
									<?php
									if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
										<li class="edit-icon"><?php echo HtmlHelper::_('icon.edit', $item, $this->params, array('type' => 'sermon')); ?></li>
									<?php endif; ?>
								</ul>
							</div>
							<div class="page-header">
								<h2><?php echo SermonspeakerHelperSermonspeaker::insertSermonTitle($i, $item, $player, false); ?></h2>
								<?php echo JLayoutHelper::render('blocks.state_info', array('item' => $item, 'show' => $showState)); ?>

								<?php if (in_array('sermons:speaker', $this->columns) and $item->speaker_title) : ?>
									<small class="ss-speaker createdby">
										<?php echo Text::_('COM_SERMONSPEAKER_SPEAKER'); ?>:
										<?php echo JLayoutHelper::render('titles.speaker', array('item' => $item, 'params' => $this->params)); ?>
									</small>
								<?php endif; ?>
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
									if (in_array('serie:category', $this->columns) and $item->category_title) : ?>
										<dd>
											<div class="category-name">
												<?php echo Text::_('JCATEGORY'); ?>:
												<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonsRoute($item->catslug, $item->language)); ?>"><?php echo $item->category_title; ?></a>
											</div>
										</dd>
									<?php endif;

									if (in_array('serie:date', $this->columns) and ($item->sermon_date != '0000-00-00 00:00:00')) : ?>
										<dd>
											<div class="create">
												<i class="icon-calendar"></i>
												<?php echo Text::_('COM_SERMONSPEAKER_FIELD_DATE_LABEL'); ?>:
												<?php echo HtmlHelper::date($item->sermon_date, Text::_($this->params->get('date_format')), true); ?>
											</div>
										</dd>
									<?php endif;

									if (in_array('serie:hits', $this->columns)) : ?>
										<dd>
											<div class="hits">
												<i class="icon-eye-open"></i>
												<?php echo Text::_('JGLOBAL_HITS'); ?>:
												<?php echo $item->hits; ?>
											</div>
										</dd>
									<?php endif;

									if (in_array('serie:scripture', $this->columns) and $item->scripture) : ?>
										<dd>
											<div class="ss-sermondetail-info">
												<i class="icon-quote"></i>
												<?php echo Text::_('COM_SERMONSPEAKER_FIELD_SCRIPTURE_LABEL'); ?>:
												<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '; ');
												echo HtmlHelper::_('content.prepare', $scriptures, '', 'com_sermonspeaker.scripture'); ?>
											</div>
										</dd>
									<?php endif;

									if (in_array('serie:length', $this->columns) and $item->sermon_time != '00:00:00') : ?>
										<dd>
											<div class="ss-sermondetail-info">
												<i class="icon-clock"></i>
												<?php echo Text::_('COM_SERMONSPEAKER_FIELD_LENGTH_LABEL'); ?>:
												<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
											</div>
										</dd>
									<?php endif;

									if (in_array('serie:addfile', $this->columns) and $item->addfile) : ?>
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

							<?php if (in_array('serie:notes', $this->columns) and $item->notes) : ?>
								<div>
									<?php echo HtmlHelper::_('content.prepare', $item->notes, '', 'com_sermonspeaker.notes'); ?>
								</div>
							<?php endif; ?>

							<?php echo $item->event->afterDisplayContent; ?>

						</div>
						<div class="clearfix"></div>
					<?php endforeach; ?>
				</div>
			<?php endif;

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
</div>
