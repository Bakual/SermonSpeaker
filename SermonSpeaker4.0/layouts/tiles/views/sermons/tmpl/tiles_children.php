<?php
defined('_JEXEC') or die;
$class	= 'first';
if (count($this->children[$this->category->id]) AND $this->maxLevel != 0) : ?>
	<?php foreach($this->children[$this->category->id] as $id => $child) :
		if ($this->params->get('show_empty_categories_cat') or $child->getNumItems(true) or $child->hasChildren()) :
			if (!$image = $child->getParams()->get('image')):
				$image = 'media/com_sermonspeaker/images/category.png';
			endif;
			$title = ($this->params->get('show_cat_num_items_cat')) ? $this->escape($child->title).' ('.$child->getNumItems(true).')' : $this->escape($child->title); ?>
			<div class="tile level<?php echo $child->level; ?> <?php echo $class; ?>">
			<?php $class = ''; ?>
				<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSermonsRoute($child->id));?>" title="<?php echo $title; ?>">
					<img border="0" align="middle" src="<?php echo $image; ?>"/>
					<?php if ($child->level == 1): ?>
						<span class="item-title">
							<?php echo $title; ?>
						</span>
					<?php endif; ?>
				</a>
				<?php if ($child->hasChildren()) :
					$this->children[$child->id] = $child->getChildren();
					$this->category = $child;
					$this->maxLevel--;
					if ($this->maxLevel != 0) :
						echo $this->loadTemplate('children');
					endif;
					$this->category = $child->getParent();
					$this->maxLevel++;
				endif; ?>
			</div>
		<?php endif;
	endforeach; ?>
<?php endif;