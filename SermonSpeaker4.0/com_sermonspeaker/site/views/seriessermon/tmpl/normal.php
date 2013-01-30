<?php
defined('_JEXEC') or die;
JHTML::addIncludePath(JPATH_COMPONENT.'/helpers');
JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
$user		= JFactory::getUser();
$fu_enable	= $this->params->get('fu_enable');
$canEdit	= ($fu_enable and $user->authorise('core.edit', 'com_sermonspeaker'));
$canEditOwn	= ($fu_enable and $user->authorise('core.edit.own', 'com_sermonspeaker'));
$config['autostart']	= 0;
?>
<div class="category-list<?php echo $this->pageclass_sfx;?> ss-seriessermons-container<?php echo $this->pageclass_sfx; ?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif;
if ($this->params->get('show_category_title', 1) or $this->params->get('page_subheading')) : ?>
	<h2>
		<?php echo $this->escape($this->params->get('page_subheading'));
		if ($this->params->get('show_category_title')) : ?>
			<span class="subheading-category"><?php echo $this->category->title;?></span>
		<?php endif; ?>
	</h2>
<?php endif;
if ($this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
	<div class="category-desc">
		<?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
			<img src="<?php echo $this->category->getParams()->get('image'); ?>"/>
		<?php endif;
		if ($this->params->get('show_description') && $this->category->description) :
			echo JHtml::_('content.prepare', $this->category->description);
		endif; ?>
		<div class="clearfix"></div>
	</div>
<?php endif; ?>
<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" id="adminForm" name="adminForm">
	<?php if ($this->params->get('show_pagination_limit')) : ?>
	<div class="display-limit">
		<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>
	<?php endif;
	if (empty($this->items)) : ?>
		<div class="no_entries"><?php echo JText::sprintf('COM_SERMONSPEAKER_NO_ENTRIES', JText::_('COM_SERMONSPEAKER_SERIES')); ?></div>
	<?php else : ?>
		<!-- Begin Data -->
		<?php
		$config['count'] = 0;
		$model	= $this->getModel('Sermons');
		foreach($this->items as $item) :
			$model->setState('serie.id', $item->id);
			$sermons = $model->getItems(); ?>
			<div>
				<?php if($item->avatar) : ?>
					<img src="<?php echo SermonspeakerHelperSermonspeaker::makelink($item->avatar); ?>" style="float:right; margin-top:25px;">
				<?php endif; ?>
				<h3 class="contentheading"><?php echo $this->escape($item->series_title); ?></h3>
				<?php if ($canEdit || ($canEditOwn && ($user->id == $item->created_by))) : ?>
					<ul class="actions">
						<li class="edit-icon">
							<?php echo JHtml::_('icon.edit', $item, $this->params, array('type' => 'serie')); ?>
						</li>
					</ul>
				<?php endif;
				if (in_array('seriessermon:description', $this->col_serie)): ?>
					<p><?php echo JHTML::_('content.prepare', $item->series_description); ?></p>
				<?php endif; ?>
			</div>
			<div style="margin-left:10%;">
				<?php foreach($sermons as $sermon) : 
					$config['count'] ++;?>
					<h4 style="margin-left:-5%;">
						<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonRoute($sermon->slug)); ?>">
							<?php echo $this->escape($sermon->sermon_title);
							if (in_array('seriessermon:date', $this->columns) and ($sermon->sermon_date != '0000-00-00 00:00:00')):
								echo ' ('.JHTML::Date($sermon->sermon_date, JText::_($this->params->get('date_format')), true).')';
							endif; ?>
						</a>
					</h4>
					<?php if ($canEdit || ($canEditOwn && ($user->id == $sermon->created_by))) : ?>
						<ul class="actions">
							<li class="edit-icon">
								<?php echo JHtml::_('icon.edit', $sermon, $this->params, array('type' => 'sermon')); ?>
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
						$player = SermonspeakerHelperSermonspeaker::getPlayer($sermon, $config);
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
	<?php endif; ?>
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