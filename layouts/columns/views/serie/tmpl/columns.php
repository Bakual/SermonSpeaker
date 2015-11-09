<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Layouts.Columns
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2015 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.modal');
JHtml::stylesheet('com_sermonspeaker/columns.css', '', true);
$user		= JFactory::getUser();
$canEdit	= $user->authorise('core.edit', 'com_sermonspeaker');
$canEditOwn	= $user->authorise('core.edit.own', 'com_sermonspeaker');
$limit 		= (int) $this->params->get('limit', '');
$player		= SermonspeakerHelperSermonspeaker::getPlayer($this->items);
?>
<div class="category-list<?php echo $this->pageclass_sfx;?> ss-serie-container<?php echo $this->pageclass_sfx; ?>">
<?php
if ($this->params->get('show_page_heading', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>
<h2><a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSerieRoute($this->item->slug)); ?>"><?php echo $this->item->title; ?></a></h2>
<?php
if ($canEdit or ($canEditOwn and ($user->id == $this->item->created_by))) : ?>
	<ul class="actions">
		<li class="edit-icon">
			<?php echo JHtml::_('icon.edit', $this->item, $this->params, array('type' => 'serie')); ?>
		</li>
	</ul>
<?php endif;

if ($this->params->get('show_category_title', 0) or in_array('serie:hits', $this->col_serie) or in_array('serie:speaker', $this->col_serie)) : ?>
	<dl class="article-info serie-info">
	<dt class="article-info-term"><?php  echo JText::_('JDETAILS'); ?></dt>
	<?php
	if ($this->params->get('show_category_title', 0)) : ?>
		<dd class="category-name">
			<?php echo JText::_('JCATEGORY') . ': ' . $this->category->title; ?>
		</dd>
	<?php endif;

	if (in_array('serie:speaker', $this->col_serie) and $this->item->speakers) : ?>
		<dd class="createdby">
			<?php echo JText::_('COM_SERMONSPEAKER_SPEAKERS') . ': ' . $this->item->speakers; ?>
		</dd>
	<?php endif;

	if (in_array('serie:hits', $this->col_serie)) : ?>
		<dd class="hits">
			<?php echo JText::_('JGLOBAL_HITS') . ': ' . $this->item->hits; ?>
		</dd>
	<?php endif;

	if (in_array('serie:download', $this->col_serie)) : ?>
		<dd class="hits">
			<?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADSERIES_LABEL') . ': '; ?>
			<a href="<?php echo JRoute::_('index.php?task=serie.download&id=' . $this->item->slug); ?>" target="_new" title="<?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADSERIES_DESC'); ?>">
			<img src="media/com_sermonspeaker/images/download.png" alt="<?php echo JText::_('COM_SERMONSPEAKER_DOWNLOADSERIES_LABEL'); ?>" />
		</a></dd>
	<?php endif; ?>
	</dl>
<?php endif;

if (in_array('serie:description', $this->col_serie)) : ?>
	<div class="category-desc">
		<div class="ss-avatar">
			<?php if ($this->item->avatar) : ?>
				<img src="<?php echo trim($this->item->avatar, '/'); ?>">
			<?php endif; ?>
		</div>
		<?php echo JHtml::_('content.prepare', $this->item->series_description); ?>
		<div class="clear-left"></div>
	</div>
<?php endif;

if (in_array('serie:player', $this->columns) and count($this->items)) :
	JHtml::stylesheet('com_sermonspeaker/player.css', '', true); ?>
	<div class="ss-serie-player">
		<hr class="ss-serie-player" />
		<?php if (empty($player->hideInfo)): ?>
			<div id="playing">
				<img id="playing-pic" class="picture" src="" />
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
		<hr class="ss-serie-player" />
	<?php if ($player->toggle): ?>
		<div>
			<img class="pointer" src="media/com_sermonspeaker/images/Video.png" onclick="Video()" alt="Video" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_VIDEO'); ?>" />
			<img class="pointer" src="media/com_sermonspeaker/images/Sound.png" onclick="Audio()" alt="Audio" title="<?php echo JText::_('COM_SERMONSPEAKER_SWITCH_AUDIO'); ?>" />
		</div>
	<?php endif; ?>
	</div>
<?php endif; ?>
<form action="<?php echo JFilterOutput::ampReplace(JUri::getInstance()->toString()); ?>" method="post" id="adminForm" name="adminForm" class="form-inline">
	<?php
	if ($this->params->get('filter_field') or $this->params->get('show_pagination_limit')) :
		echo $this->loadTemplate('filters');
	endif;

	if (!count($this->items)) : ?>
		<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERMONS')); ?></div>
	<?php else : ?>
		<hr class="ss-serie-player" style="clear:both" />
		<?php foreach ($this->items as $i => $item) : ?>
			<div id="sermon<?php echo $i; ?>" class="ss-entry">
				<div class="column-picture" onclick="ss_play('<?php echo $i; ?>')">
					<div class="ss-picture">
						<?php $picture = SermonspeakerHelperSermonspeaker::insertPicture($item);

						if (!$picture) :
							$picture = 'media/com_sermonspeaker/images/' . $this->params->get('defaultpic', 'nopict.jpg');
						endif; ?>
						<img src="<?php echo $picture; ?>">
					</div>
				</div>
				<div class="column-content" onclick="ss_play('<?php echo $i; ?>')">
					<h3 class="title"><a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug)); ?>"><?php echo $item->title; ?></a>
						<?php
						if ($canEdit or ($canEditOwn && ($user->id == $item->created_by))) :
							echo JHtml::_('icon.edit', $item, $this->params, array('type' => 'sermon'));
						endif; ?>
					</h3>
					<?php $class = '';

					if (in_array('serie:scripture', $this->columns) && $item->scripture) :
						$class = 'scripture'; ?>
						<span class="scripture">
							<?php $scriptures = SermonspeakerHelperSermonspeaker::insertScriptures($item->scripture, '; ');
							echo JHtml::_('content.prepare', $scriptures); ?>
						</span>
					<?php endif;

					if (in_array('serie:speaker', $this->columns) && $item->speaker_title) : ?>
						<span class="speaker <?php echo $class; ?>">
							<?php echo JLayoutHelper::render('titles.speaker', array('item' => $item, 'params' => $this->params)); ?>
						</span>
					<?php endif;

					if (in_array('serie:notes', $this->columns) && $item->notes) : ?>
						<div>
							<?php echo JHtml::_('content.prepare', $item->notes); ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="column-files">
					<?php if (in_array('serie:addfile', $this->columns) && $item->addfile) :
						$link = SermonspeakerHelperSermonspeaker::makelink($item->addfile);

						// Get extension of file
						$ext = JFile::getExt($item->addfile);

						if (file_exists(JPATH_SITE . '/media/com_sermonspeaker/icons/' . $ext . '.png')) :
							$file = JURI::root() . 'media/com_sermonspeaker/icons/' . $ext . '.png';
						else :
							$file = JURI::root() . 'media/com_sermonspeaker/icons/icon.png';
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
						<a href="<?php echo $link; ?>" class="addfile" target="_blank" title="<?php echo JText::_('COM_SERMONSPEAKER_ADDFILE_HOOVER'); ?>">
							<img src="<?php echo $file; ?>" alt="" /> <?php echo $item->addfileDesc; ?>
						</a>
					<?php endif;

					if (in_array('serie:download', $this->columns)) : ?>
						<?php if ($item->audiofile) :
							echo SermonspeakerHelperSermonspeaker::insertdlbutton($item->slug, 'audio', 4, $item->audiofilesize);
						endif;

						if ($item->videofile) :
							echo SermonspeakerHelperSermonspeaker::insertdlbutton($item->slug, 'video', 4, $item->videofilesize);
						endif;
					endif;

					if ($item->audiofile) : ?>
						<a href="#" onclick="popup=window.open('<?php echo JRoute::_('index.php?view=sermon&layout=popup&tmpl=component&type=audio&id=' . $item->slug); ?>', 'PopupPage', 'height=150px, width=400px, scrollbars=yes, resizable=yes'); return false" class="listen" title="<?php echo JText::_('COM_SERMONSPEAKER_POPUPPLAYER'); ?>">
							Listen
						</a>
					<?php endif;

					if ($item->videofile) : ?>
						<a href="#" onclick="popup=window.open('<?php echo JRoute::_('index.php?view=sermon&layout=popup&tmpl=component&type=video&id=' . $item->slug); ?>', 'PopupPage', 'height=400px, width=450px, scrollbars=yes, resizable=yes'); return false" class="watch" title="<?php echo JText::_('COM_SERMONSPEAKER_POPUPPLAYER'); ?>">
							Watch
						</a>
					<?php endif; ?>
				</div>
				<div class="column-detail" onclick="ss_play('<?php echo $i; ?>')">
					<?php
					if (in_array('serie:date', $this->columns) and ($item->sermon_date != '0000-00-00 00:00:00')) : ?>
						<div class="create">
							<?php echo JHtml::Date($item->sermon_date, JText::_('DATE_FORMAT_LC1'), true); ?>
						</div>
					<?php endif;

					if (in_array('serie:category', $this->columns)) : ?>
						<div class="category-name">
							<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonsRoute($item->catslug)); ?>"><?php echo $item->category_title; ?></a>
						</div>
					<?php endif;

					if (in_array('serie:hits', $this->columns)) : ?>
						<div class="hits">
							<?php echo JText::_('JGLOBAL_HITS'); ?>:
							<?php echo $item->hits; ?>
						</div>
					<?php endif;

					if (in_array('serie:length', $this->columns)) : ?>
						<div class="ss-sermondetail-info">
							<?php echo SermonspeakerHelperSermonspeaker::insertTime($item->sermon_time); ?>
						</div>
					<?php endif;

					if ($this->params->get('custom1') and $item->custom1) : ?>
						<div class="ss-sermondetail-info">
							<?php echo JText::_('COM_SERMONSPEAKER_CUSTOM1'); ?>:
							<?php echo $item->custom1; ?>
						</div>
					<?php endif;

					if ($this->params->get('custom2') and $item->custom2) : ?>
						<div class="ss-sermondetail-info">
							<?php echo JText::_('COM_SERMONSPEAKER_CUSTOM2'); ?>:
							<?php echo $item->custom2; ?>
						</div>
					<?php endif; ?>
				</div>
				<br style="clear:both" />
			</div>
			<hr class="ss-sermons-player" />
		<?php endforeach;
	endif;

	if ($this->params->get('show_pagination') and ($this->pagination->get('pages.total') > 1)) : ?>
		<div class="pagination">
			<?php if ($this->params->get('show_pagination_results', 1)) : ?>
				<p class="counter">
					<?php echo $this->pagination->getPagesCounter(); ?>
				</p>
			<?php endif;
			echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php endif; ?>
	<input type="hidden" name="task" value="" />
</form>
<?php if (!empty($this->children[$this->category->id]) and $this->maxLevel != 0) : ?>
	<div class="cat-children">
		<h3>
			<?php echo JTEXT::_('JGLOBAL_SUBCATEGORIES'); ?>
		</h3>
		<?php echo $this->loadTemplate('children'); ?>
	</div>
<?php endif; ?>
</div>
