<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Layouts.Columns
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\File;
use Sermonspeaker\Component\Sermonspeaker\Site\Helper\SermonspeakerHelper;

HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');
HTMLHelper::_('stylesheet', 'com_sermonspeaker/columns.css', array('relative' => true));

$user       = Factory::getApplication()->getIdentity();
$fu_enable  = $this->params->get('fu_enable');
$canEdit    = ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn = ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$limit      = (int) $this->params->get('limit', '');
$player     = SermonspeakerHelper::getPlayer($this->items);
?>
<div class="com-sermonspeaker-serie<?php echo $this->pageclass_sfx; ?> com-sermonspeaker-serie-columns">
	<?php echo $this->loadTemplate('header'); ?>
	<div class="clearfix"></div>
	<?php if (in_array('serie:player', $this->columns) and count($this->items)) : ?>
		<?php echo LayoutHelper::render('plugin.player', array('player' => $player, 'items' => $this->items, 'view' => 'serie')); ?>
	<?php endif; ?>
	<?php echo $this->item->event->afterDisplayContent; ?>
	<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm"
		  id="adminForm" class="com-sermonspeaker-serie__sermons">
		<?php if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) : ?>
			<?php echo $this->loadTemplate('filters'); ?>
		<?php endif; ?>
		<div class="clearfix"></div>
		<?php if (!count($this->items)) : ?>
			<div class="alert alert-info">
				<span class="icon-info-circle" aria-hidden="true"></span><span
						class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
				<?php echo Text::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', Text::_('COM_SERMONSPEAKER_SERMONS')); ?>
			</div>
		<?php else : ?>
			<hr>
			<?php foreach ($this->items as $i => $item) : ?>
				<div id="sermon<?php echo $i; ?>" class="ss-entry sermon-item">
					<div class="column-picture" onclick="ss_play(<?php echo $i; ?>)">
						<div class="ss-picture">
							<?php $picture = SermonspeakerHelper::insertPicture($item);

							if (!$picture) :
								$picture = 'media/com_sermonspeaker/images/' . $this->params->get('defaultpic', 'nopict.jpg');
							endif; ?>
							<img src="<?php echo $picture; ?>">
						</div>
					</div>
					<div class="column-content" onclick="ss_play(<?php echo $i; ?>)">
						<h3 class="title"><a
									href="<?php echo Route::_(Sermonspeaker\Component\Sermonspeaker\Site\Helper\RouteHelper::getSermonRoute($item->slug, $item->catid, $item->language)); ?>"><?php echo $item->title; ?></a>
							<?php
							if ($canEdit or ($canEditOwn && ($user->id == $item->created_by))) :
								echo HTMLHelper::_('sermonspeakericon.edit', $item, $this->params, array('type' => 'sermon'));
							endif; ?>
						</h3>
						<?php $class = '';

						if (in_array('serie:scripture', $this->columns) && $item->scripture) :
							$class = 'scripture'; ?>
							<span class="scripture">
							<?php $scriptures = SermonspeakerHelper::insertScriptures($item->scripture, '; ');
							echo HTMLHelper::_('content.prepare', $scriptures); ?>
						</span>
						<?php endif;

						if (in_array('serie:speaker', $this->columns) && $item->speaker_title) : ?>
							<span class="speaker <?php echo $class; ?>">
							<?php echo LayoutHelper::render('titles.speaker', array('item' => $item, 'params' => $this->params)); ?>
						</span>
						<?php endif;

						if (in_array('serie:notes', $this->columns) && $item->notes) : ?>
							<div class="ss-notes">
								<?php echo HTMLHelper::_('content.prepare', $item->notes); ?>
							</div>
						<?php endif;

						if (in_array('serie:maintext', $this->columns) && $item->maintext) : ?>
							<div class="ss-maintext">
								<?php echo HTMLHelper::_('content.prepare', $item->maintext); ?>
							</div>
						<?php endif; ?>
					</div>
					<div class="column-detail" onclick="ss_play(<?php echo $i; ?>)">
						<?php
						if (in_array('serie:date', $this->columns) and ($item->sermon_date != '0000-00-00 00:00:00')) : ?>
							<div class="create">
								<?php echo HTMLHelper::date($item->sermon_date, Text::_('DATE_FORMAT_LC1')); ?>
							</div>
						<?php endif;

						if (in_array('serie:category', $this->columns)) : ?>
							<div class="category-name">
								<a href="<?php echo Route::_(Sermonspeaker\Component\Sermonspeaker\Site\Helper\RouteHelper::getSermonsRoute($item->catid, $item->language)); ?>"><?php echo $item->category_title; ?></a>
							</div>
						<?php endif;

						if (in_array('serie:hits', $this->columns)) : ?>
							<div class="hits">
								<?php echo Text::_('JGLOBAL_HITS'); ?>:
								<?php echo $item->hits; ?>
							</div>
						<?php endif;

						if (in_array('serie:length', $this->columns)) : ?>
							<div class="ss-sermondetail-info">
								<?php echo SermonspeakerHelper::insertTime($item->sermon_time); ?>
							</div>
						<?php endif;

						if ($this->params->get('custom1') and $item->custom1) : ?>
							<div class="ss-sermondetail-info">
								<?php echo Text::_('COM_SERMONSPEAKER_CUSTOM1'); ?>:
								<?php echo $item->custom1; ?>
							</div>
						<?php endif;

						if ($this->params->get('custom2') and $item->custom2) : ?>
							<div class="ss-sermondetail-info">
								<?php echo Text::_('COM_SERMONSPEAKER_CUSTOM2'); ?>:
								<?php echo $item->custom2; ?>
							</div>
						<?php endif; ?>
					</div>
					<div class="column-files">
						<?php if (in_array('serie:addfile', $this->columns) && $item->addfile) :
							$link = SermonspeakerHelper::makeLink($item->addfile);

							// Get extension of file
							$ext = File::getExt($item->addfile);

							if (file_exists(JPATH_SITE . '/media/com_sermonspeaker/icons/' . $ext . '.png')) :
								$file = Uri::root() . 'media/com_sermonspeaker/icons/' . $ext . '.png';
							else :
								$file = Uri::root() . 'media/com_sermonspeaker/icons/icon.png';
							endif;

							// Show filename if no addfileDesc is set
							if (!$item->addfileDesc) :
								if ($default = $this->params->get('addfiledesc')) :
									$item->addfileDesc = $default;
								else :
									$slash = strrpos($item->addfile, '/');

									if ($slash !== false) :
										$item->addfileDesc = substr($item->addfile, $slash + 1);
									else :
										$item->addfileDesc = $item->addfile;
									endif;
								endif;
							endif; ?>
							<a href="<?php echo $link; ?>" class="addfile" target="_blank"
							   title="<?php echo Text::_('COM_SERMONSPEAKER_ADDFILE_HOOVER'); ?>">
								<img src="<?php echo $file; ?>" alt=""/> <?php echo $item->addfileDesc; ?>
							</a>
						<?php endif;

						if (in_array('serie:download', $this->columns)) : ?>
							<?php if ($item->audiofile) :
								echo SermonspeakerHelper::insertdlbutton($item->slug, 'audio', 4, $item->audiofilesize);
							endif;

							if ($item->videofile) :
								echo SermonspeakerHelper::insertdlbutton($item->slug, 'video', 4, $item->videofilesize);
							endif;
						endif;

						if ($item->audiofile) : ?>
							<a href="#"
							   onclick="popup=window.open('<?php echo Route::_('index.php?view=sermon&layout=popup&tmpl=component&type=audio&id=' . $item->slug); ?>', 'PopupPage', 'height=150px, width=400px, scrollbars=yes, resizable=yes'); return false"
							   class="listen" title="<?php echo Text::_('COM_SERMONSPEAKER_POPUPPLAYER'); ?>">
								Listen
							</a>
						<?php endif;

						if ($item->videofile) : ?>
							<a href="#"
							   onclick="popup=window.open('<?php echo Route::_('index.php?view=sermon&layout=popup&tmpl=component&type=video&id=' . $item->slug); ?>', 'PopupPage', 'height=400px, width=450px, scrollbars=yes, resizable=yes'); return false"
							   class="watch" title="<?php echo Text::_('COM_SERMONSPEAKER_POPUPPLAYER'); ?>">
								Watch
							</a>
						<?php endif; ?>
					</div>
				</div>
				<hr>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php if (!empty($this->items)) : ?>
			<?php echo LayoutHelper::render('blocks.pagination', array('view' => 'serie', 'pagination' => $this->pagination, 'params' => $this->params)); ?>
		<?php endif; ?>
		<input type="hidden" name="task" value=""/>
	</form>
	<?php if (!empty($this->children[$this->category->id]) and $this->maxLevel != 0) : ?>
		<div class="cat-children">
			<h3>
				<?php echo Text::_('JGLOBAL_SUBCATEGORIES'); ?>
			</h3>
			<?php echo $this->loadTemplate('children'); ?>
		</div>
	<?php endif; ?>
</div>
