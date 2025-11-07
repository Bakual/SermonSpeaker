<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Sermonspeaker\Component\Sermonspeaker\Site\Helper\SermonspeakerHelper;

$user       = Factory::getApplication()->getIdentity();
$showState  = $user->authorise('core.edit', 'com_sermonspeaker');
$fu_enable  = $this->params->get('fu_enable');
$canEdit    = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$listOrder  = $this->escape($this->state->get('list.ordering'));

$listDirn   = $this->escape($this->state->get('list.direction'));

?>
<div class="com-sermonspeaker-speakers<?php echo $this->pageclass_sfx; ?> com-sermonspeaker-speakers-blog blog">
	<?php echo LayoutHelper::render('blocks.header', array('category' => $this->category, 'params' => $this->params)); ?>

	<div class="com-sermonspeaker-speakers-blog__items blog-items">
		<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" id="adminForm"
			  name="adminForm" class="com-sermonspeaker-speakers__speakers">
			<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) : ?>
				<?php echo $this->loadTemplate('filters'); ?>
				<?php echo $this->loadTemplate('order'); ?>
			<?php endif; ?>
			<div class="clearfix"></div>
			<?php if (!count($this->items)) : ?>
				<div class="alert alert-info">
					<span class="icon-info-circle" aria-hidden="true"></span><span
							class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
					<?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SPEAKERS')); ?>
				</div>
			<?php else : ?>
				<?php foreach ($this->items as $i => $item) : ?>
					<div id="speaker<?php echo $i; ?>"
						 class="<?php echo ($item->state) ? '' : 'system-unpublished '; ?>speaker-item image-right">
						<div class="com-sermonspeaker-speakers-blog__item blog-item">
							<?php if ($item->pic) : ?>
								<figure class="item-image speaker-image">
									<img src="<?php echo SermonspeakerHelper::makeLink($item->pic); ?>" alt="">
								</figure>
							<?php endif; ?>

							<div class="item-content">
								<h2>
									<a href="<?php echo Route::_(Sermonspeaker\Component\Sermonspeaker\Site\Helper\RouteHelper::getSpeakerRoute($item->slug, $item->catid, $item->language)); ?>">
										<?php echo $item->title; ?>
									</a>
								</h2>

								<?php if ($canEdit or ($canEditOwn and ($user->id == $item->created_by))) : ?>
									<div class="icons">
										<div class="float-end">
											<?php echo HTMLHelper::_('sermonspeakericon.edit', $item, $this->params, array('type' => 'speaker')); ?>
										</div>
									</div>
								<?php endif; ?>
								<?php echo $item->event->afterDisplayTitle; ?>

								<dl class="article-info speaker-info text-muted">
									<dt class="article-info-term"><?php echo Text::_('JDETAILS'); ?></dt>
									<?php if (in_array('speakers:category', $this->col_speaker) and $item->category_title) : ?>
										<dd>
											<div class="category-name">
												<span class="icon-folder-open icon-fw"></span>
												<?php echo Text::_('JCATEGORY'); ?>:
												<a href="<?php echo Route::_(Sermonspeaker\Component\Sermonspeaker\Site\Helper\RouteHelper::getSpeakersRoute($item->catslug, $item->language)); ?>"><?php echo $item->category_title; ?></a>
											</div>
										</dd>
									<?php endif; ?>

									<?php if (in_array('speakers:hits', $this->col_speaker)) : ?>
										<dd>
											<div class="hits">
												<span class="icon-eye-open"></span>
												<?php echo Text::_('JGLOBAL_HITS'); ?>:
												<?php echo $item->hits; ?>
											</div>
										</dd>
									<?php endif; ?>

									<?php if ($item->website) : ?>
										<dd>
											<div class="website">
												<span class=" icon-out-2"></span>
												<a href="<?php echo $item->website; ?>">
													<?php echo Text::_('COM_SERMONSPEAKER_FIELD_WEBSITE_LABEL'); ?>
												</a>
											</div>
										</dd>
									<?php endif; ?>
								</dl>

								<?php echo $item->event->beforeDisplayContent; ?>

								<?php if (in_array('speakers:intro', $this->col_speaker) and $item->intro) : ?>
									<div>
										<?php echo HTMLHelper::_('content.prepare', $item->intro, '', 'com_sermonspeaker.intro'); ?>
									</div>
								<?php endif; ?>

								<?php if (in_array('speakers:bio', $this->col_speaker) and $item->bio) : ?>
									<div>
										<?php echo HTMLHelper::_('content.prepare', $item->bio, '', 'com_sermonspeaker.bio'); ?>
									</div>
								<?php endif; ?>

								<?php if ($item->sermons) : ?>
									<a class="badge bg-info"
									   title="<?php echo Text::_('COM_SERMONSPEAKER_SPEAKERS_SERMONSLINK_HOOVER'); ?>"
									   href="<?php echo Route::_(Sermonspeaker\Component\Sermonspeaker\Site\Helper\RouteHelper::getSpeakerRoute($item->slug, $item->catid, $item->language) . '#sermons'); ?>">
										<?php echo Text::_('COM_SERMONSPEAKER_SERMONS') . ': ' . $item->sermons; ?></a>&nbsp;
								<?php endif; ?>

								<?php if ($item->series) : ?>
									<a class="badge bg-info"
									   title="<?php echo Text::_('COM_SERMONSPEAKER_SPEAKERS_SERIESLINK_HOOVER'); ?>"
									   href="<?php echo Route::_(Sermonspeaker\Component\Sermonspeaker\Site\Helper\RouteHelper::getSpeakerRoute($item->slug, $item->catid, $item->language) . '#series'); ?>">
										<?php echo Text::_('COM_SERMONSPEAKER_SERIES') . ': ' . $item->series; ?></a>&nbsp;
								<?php endif; ?>

								<?php echo $item->event->afterDisplayContent; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>

			<?php if (!empty($this->items)) : ?>
				<?php echo LayoutHelper::render('blocks.pagination', array('view' => 'speakers', 'pagination' => $this->pagination, 'params' => $this->params)); ?>
			<?php endif; ?>
			<input type="hidden" name="task" value=""/>
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
