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

$user       = Factory::getUser();
$showState  = $user->authorise('core.edit', 'com_sermonspeaker');
$fu_enable  = $this->params->get('fu_enable');
$canEdit    = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$htag       = $this->params->get('show_page_heading') ? 'h2' : 'h1';
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

<div class="icons">
	<div class="float-end">
		<?php if ($canEdit or ($canEditOwn and ($user->id == $this->item->created_by))) : ?>
			<div>
				<span class="edit-icon"><?php echo HTMLHelper::_('icon.edit', $this->item, $this->params, array('type' => 'speaker')); ?></span>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php echo LayoutHelper::render('blocks.state_info', array('item' => $this->item, 'show' => Factory::getUser()->authorise('core.edit', 'com_sermonspeaker'))); ?>

<?php if ($this->item->pic) : ?>
	<div class="img-thumbnail float-end item-image m-1">
		<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSpeakerRoute($this->item->slug, $this->item->catid, $this->item->language)); ?>"
		   itemprop="url">
			<img src="<?php echo SermonspeakerHelperSermonspeaker::makeLink($this->item->pic); ?>" itemprop="image"
				 alt="">
		</a>
	</div>
<?php endif; ?>

<dl class="article-info speaker-info text-muted">
	<dt class="article-info-term"><?php echo Text::_('JDETAILS'); ?></dt>
	<?php if (in_array('speaker:category', $this->columns) and $this->item->category_title) : ?>
		<dd>
			<div class="category-name">
				<span class="fas fa-folder-open"></span>
				<?php echo Text::_('JCATEGORY'); ?>:
				<a href="<?php echo Route::_(SermonspeakerHelperRoute::getSpeakersRoute($this->item->catslug, $this->item->language)); ?>"
				   itemprop="genre">
					<?php echo $this->item->category_title; ?>
				</a>
			</div>
		</dd>
	<?php endif; ?>

	<?php if (in_array('speaker:hits', $this->columns)) : ?>
		<dd>
			<div class="hits">
				<span class="fas fa-eye"></span>
				<meta itemprop="interactionCount" content="UserPageVisits:<?php echo $this->item->hits; ?>"/>
				<?php echo Text::_('JGLOBAL_HITS'); ?>:
				<?php echo $this->item->hits; ?>
			</div>
		</dd>
	<?php endif; ?>

	<?php if ($this->item->website) : ?>
		<dd>
			<div class="website">
				<span class="fas fa-external-link-alt"></span>
				<a href="<?php echo $this->item->website; ?>" itemprop="sameAs">
					<?php echo Text::_('COM_SERMONSPEAKER_FIELD_WEBSITE_LABEL'); ?>
				</a>
			</div>
		</dd>
	<?php endif; ?>
</dl>

<?php if ($this->params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
	<?php echo LayoutHelper::render('joomla.content.tags', $this->item->tags->itemTags); ?>
<?php endif; ?>

<?php echo $this->item->event->afterDisplayTitle; ?>
<?php echo $this->item->event->beforeDisplayContent; ?>

<?php if (in_array('speaker:intro', $this->columns) and $this->item->intro) : ?>
	<div itemprop="description">
		<?php echo HTMLHelper::_('content.prepare', $this->item->intro, '', 'com_sermonspeaker.intro'); ?>
	</div>
<?php endif; ?>

<?php if (in_array('speaker:bio', $this->columns) and $this->item->bio) : ?>
	<div itemprop="description">
		<?php echo HTMLHelper::_('content.prepare', $this->item->bio, '', 'com_sermonspeaker.bio'); ?>
	</div>
<?php endif; ?>

<?php echo $this->item->event->afterDisplayContent; ?>
