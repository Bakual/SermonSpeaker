<?php
defined('_JEXEC') or die('Restricted access');
JHTML::addIncludePath(JPATH_COMPONENT.'/helpers');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
$user		= JFactory::getUser();
$canEdit	= $user->authorise('core.edit', 'com_sermonspeaker');
$canEditOwn	= $user->authorise('core.edit.own', 'com_sermonspeaker');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$config['autostart']	= 0;
?>
<div class="ss-sermons-container<?php echo htmlspecialchars($this->params->get('pageclass_sfx')); ?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif;
if ($this->cat): ?>
	<h2><span class="subheading-category"><?php echo $this->cat; ?></span></h2>
<?php endif;
if (empty($this->items)) : ?>
	<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERIES')); ?></div>
<?php else : ?>
<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" id="adminForm" name="adminForm">
	<?php if ($this->params->get('show_pagination_limit')) : ?>
	<div class="display-limit">
		<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>
	<?php endif; ?>
	<!-- Begin Data -->
	<?php
	$config['count'] = 0;
	$model	= &$this->getModel();
	foreach($this->items as $item) :
		$sermons = &$model->getSermons($item->id); ?>
		<div>
			<?php if($item->avatar) : ?>
				<img src="<?php echo SermonspeakerHelperSermonspeaker::makelink($item->avatar); ?>" style="float:right; margin-top:25px;">
			<?php endif; ?>
			<h3 class="contentheading"><?php echo $this->escape($item->series_title); ?></h3>
			<?php if (in_array('seriessermon:description', $this->col_serie)): ?>
				<p><?php echo JHTML::_('content.prepare', $item->series_description); ?></p>
			<?php endif; ?>
		</div>
		<div style="margin-left:10%;">
			<?php foreach($sermons as $sermon) : 
				$config['count'] ++;?>
				<h4 style="margin-left:-5%;">
					<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonRoute($sermon->slug)); ?>">
						<?php echo $this->escape($sermon->sermon_title);
						if (in_array('seriessermon:date', $this->columns)):
							echo ' ('.JHTML::Date($sermon->sermon_date, JText::_($this->params->get('date_format')), true).')';
						endif; ?>
					</a>
				</h4>
				<?php if ($canEdit || ($canEditOwn && ($user->id == $sermon->created_by))) : ?>
					<ul class="actions">
						<li class="edit-icon">
							<?php echo JHtml::_('icon.edit', $sermon, $this->params); ?>
						</li>
					</ul>
				<?php endif;
				if (in_array('seriessermon:notes', $this->columns)): ?>
				<div>
					<?php echo $sermon->notes; ?>
				</div>
				<?php endif;
				if ($sermon->addfile && $sermon->addfileDesc && in_array('seriessermon:addfile', $this->columns)): ?>
					<b><?php echo JText::_('COM_SERMONSPEAKER_ADDFILE'); ?> : </b>
					<?php echo SermonspeakerHelperSermonspeaker::insertAddfile($sermon->addfile, $sermon->addfileDesc); ?>
					<br />
				<?php endif;
				if (in_array('seriessermon:player', $this->columns)):
					$player = new SermonspeakerHelperPlayer($sermon, $config);
					echo $player->mspace;
					echo $player->script;
				endif;
				if (in_array('seriessermon:download', $this->columns)) : ?>
					<div class="ss-dl">
						<?php if ($sermon->audiofile):
							echo SermonspeakerHelperSermonspeaker::insertdlbutton($sermon->slug, 'audio');
						endif;
						if ($sermon->videofile):
							echo SermonspeakerHelperSermonspeaker::insertdlbutton($sermon->slug, 'video');
						endif; ?>
					</div>
				<?php endif;
			endforeach; ?>
		</div>
		<br style="clear:both;" />
		<hr size="2" width="100%" />
		<?php endforeach; ?>
</form>
<?php endif; ?>
</div>