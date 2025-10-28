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

$user       = Factory::getUser();
$showState  = $user->authorise('core.edit', 'com_sermonspeaker');
$fu_enable  = $this->params->get('fu_enable');
$canEdit    = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$htag       = $this->params->get('show_page_heading') ? 'h2' : 'h1';
$limit      = $this->params->get('limitseriesdl');
?>
<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
	</div>
<?php endif; ?>

<<?php echo $htag; ?>>
<?php echo $this->item->title; ?>
</<?php echo $htag; ?>>

<?php echo LayoutHelper::render('blocks.state_info', array('item' => $this->item, 'show' => $showState)); ?>

<?php if (in_array('serie:speaker', $this->col_serie) and $this->item->speakers) : ?>
	<small class="ss-speakers createdby">
		<?php echo Text::_('COM_SERMONSPEAKER_SPEAKERS'); ?>:
		<?php echo $this->item->speakers; ?>
	</small>
<?php endif; ?>

<div class="icons">
	<div class="float-end">
		<?php if ($this->params->get('seriesdl') && ($this->item->zip_dl !== -1) && (!$limit || (count($rows) <= $limit)) && in_array('serie:download', $this->col_serie)) : ?>
			<?php $url = Route::_('index.php?view=serie&layout=download&tmpl=component&id=' . $this->item->slug); ?>
			<?php $downloadText = Text::_('COM_SERMONSPEAKER_DOWNLOADSERIES_LABEL'); ?>
			<?php $modalOptions = array('url' => $url, 'height' => 200, 'width' => 400, 'title' => $downloadText); ?>
			<?php echo HTMLHelper::_('bootstrap.rendermodal', 'downloadModal', $modalOptions); ?>
			<span class="download-icon">
				<a href="#downloadModal" class="downloadModal" data-bs-toggle="modal">
					<span class="fas fa-download"> </span>
					<?php echo $downloadText; ?>
				</a>
			</span>
		<?php endif; ?>

		<?php if ($canEdit or ($canEditOwn and ($user->id == $this->item->created_by))) : ?>
			<div>
				<span class="edit-icon"><?php echo HTMLHelper::_('icon.edit', $this->item, $this->params, array('type' => 'serie')); ?></span>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php if ($this->item->avatar) : ?>
	<div class="img-thumbnail float-end item-image">
		<img src="<?php echo SermonspeakerHelper::makeLink($this->item->avatar); ?>">
	</div>
<?php endif; ?>

<dl class="article-info serie-info text-muted">
	<dt class="article-info-term"><?php echo Text::_('JDETAILS'); ?></dt>
	<?php if (in_array('serie:category', $this->col_serie) and $this->item->category_title) : ?>
		<dd>
			<div class="category-name">
				<span class="icon-folder-open icon-fw"></span>
				<?php echo Text::_('JCATEGORY'); ?>:
				<a href="<?php echo Route::_(Sermonspeaker\Component\Sermonspeaker\Site\Helper\RouteHelper::getSeriesRoute($this->item->catslug, $this->item->language)); ?>"><?php echo $this->item->category_title; ?></a>
			</div>
		</dd>
	<?php endif;

	if (in_array('serie:hits', $this->col_serie)) : ?>
		<dd>
			<div class="hits">
				<span class="icon-eye-open"></span>
				<?php echo Text::_('JGLOBAL_HITS'); ?>:
				<?php echo $this->item->hits; ?>
			</div>
		</dd>
	<?php endif; ?>
</dl>

<?php if ($this->params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
	<?php echo LayoutHelper::render('joomla.content.tags', $this->item->tags->itemTags); ?>
<?php endif; ?>

<?php echo $this->item->event->afterDisplayTitle; ?>
<?php echo $this->item->event->beforeDisplayContent; ?>

<?php if (in_array('serie:description', $this->col_serie) and $this->item->series_description) : ?>
	<div>
		<?php echo HTMLHelper::_('content.prepare', $this->item->series_description, '', 'com_sermonspeaker.description'); ?>
	</div>
<?php endif; ?>
