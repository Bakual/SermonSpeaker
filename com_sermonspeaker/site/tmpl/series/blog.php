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
<div class="com-sermonspeaker-series<?php echo $this->pageclass_sfx; ?> com-sermonspeaker-series-blog blog">
	<?php echo LayoutHelper::render('blocks.header', array('category' => $this->category, 'params' => $this->params)); ?>

	<div class="com-sermonspeaker-series-blog__items blog-items">
		<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" id="adminForm"
			  name="adminForm" class="com-sermonspeaker-series__series">
			<?php echo $this->loadTemplate('filters'); ?>
			<?php echo $this->loadTemplate('order'); ?>
			<div class="clearfix"></div>
			<?php if (!count($this->items)) : ?>
				<div class="alert alert-info">
					<span class="icon-info-circle" aria-hidden="true"></span><span
							class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
					<?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SERIES')); ?>
				</div>
			<?php else : ?>
				<?php foreach ($this->items as $i => $item) : ?>
					<div id="serie<?php echo $i; ?>"
						 class="<?php echo ($item->state) ? '' : 'system-unpublished '; ?>serie-item image-right">
						<div class="com-sermonspeaker-series-blog__item blog-item">
							<?php if ($item->avatar) : ?>
								<figure class="item-image serie-image">
									<img src="<?php echo SermonspeakerHelper::makeLink($item->avatar); ?>" alt="">
								</figure>
							<?php endif; ?>

							<div class="item-content">
								<h2>
									<a href="<?php echo Route::_(Sermonspeaker\Component\Sermonspeaker\Site\Helper\RouteHelper::getSerieRoute($item->slug, $item->catid, $item->language)); ?>">
										<?php echo $item->title; ?>
									</a>
								</h2>
								<?php if (in_array('series:speaker', $this->col_serie) and $item->speakers) : ?>
									<small class="com-sermonspeaker-series createdby">
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
									<?php if (in_array('series:category', $this->col_serie) and $item->category_title) : ?>
										<dd>
											<div class="category-name">
												<span class="icon-folder-open icon-fw"></span>
												<?php echo Text::_('JCATEGORY'); ?>:
												<a href="<?php echo Route::_(Sermonspeaker\Component\Sermonspeaker\Site\Helper\RouteHelper::getSeriesRoute($item->catslug, $item->language)); ?>"><?php echo $item->category_title; ?></a>
											</div>
										</dd>
									<?php endif; ?>

									<?php if (in_array('series:hits', $this->col_serie)) : ?>
										<dd>
											<div class="hits">
												<span class="icon-eye-open"></span>
												<?php echo Text::_('JGLOBAL_HITS'); ?>:
												<?php echo $item->hits; ?>
											</div>
										</dd>
									<?php endif; ?>

									<?php if ($this->params->get('seriesdl') && ($item->zip_dl !== -1) && in_array('series:download', $this->col_serie)) : ?>
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

								<?php if (in_array('series:description', $this->col_serie) and $item->series_description) : ?>
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

			<?php if (!empty($this->items)) : ?>
				<?php echo LayoutHelper::render('blocks.pagination', array('view' => 'series', 'pagination' => $this->pagination, 'params' => $this->params)); ?>
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
