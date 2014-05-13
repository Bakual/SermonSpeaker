<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site.Layouts
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   (C) 2014 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

/**
 * $item     object     The sermon item
 * $params   JRegistry  The item params
 * $columns  JRegistry  The columns to show
 */
extract($displayData);
?>
<div class="page-header">
	<h2 itemprop="name">
		<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug)); ?>" itemprop="url">
			<?php echo $item->title; ?>
		</a>
	</h2>
	<?php if (!$item->state) : ?>
		<span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
	<?php endif; ?>
</div>
<?php if ($item->pic) : ?>
	<div class="img-polaroid pull-right item-image">
		<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakerRoute($item->slug)); ?>" itemprop="url">
			<img src="<?php echo SermonspeakerHelperSermonspeaker::makelink($item->pic); ?>" itemprop="image">
		</a>
	</div>
<?php endif; ?>
<div class="article-info speaker-info muted">
	<dl class="article-info">
		<dt class="article-info-term"><?php  echo JText::_('JDETAILS'); ?></dt>
		<?php
		if (in_array('speaker:category', $columns) and $item->category_title) : ?>
			<dd>
				<div class="category-name">
					<?php echo JText::_('JCATEGORY'); ?>:
					<a href="<?php echo JRoute::_(SermonspeakerHelperRoute::getSpeakersRoute($item->catslug)); ?>" itemprop="genre">
						<?php echo $item->category_title; ?>
					</a>
				</div>
			</dd>
		<?php endif;

		if (in_array('speaker:hits', $columns)) : ?>
			<dd>
				<div class="hits">
					<i class="icon-eye-open"></i>
					<meta itemprop="interactionCount" content="UserPageVisits:<?php echo $item->hits; ?>" />
					<?php echo JText::_('JGLOBAL_HITS'); ?>:
					<?php echo $item->hits; ?>
				</div>
			</dd>
		<?php endif;

		if ($item->website) : ?>
			<dd>
				<div class="website">
					<i class=" icon-out-2"></i>
					<a href="<?php echo $item->website; ?>" itemprop="sameAs">
						<?php echo JText::_('COM_SERMONSPEAKER_FIELD_WEBSITE_LABEL'); ?>
					</a>
				</div>
			</dd>
		<?php endif; ?>
	</dl>
</div>
<?php if ($params->get('show_tags', 1) and !empty($item->tags)) :
	$tagLayout = new JLayoutFile('joomla.content.tags');
	echo $tagLayout->render($item->tags->itemTags); ?>
<?php endif;

if (in_array('speaker:intro', $columns) and $item->intro) : ?>
	<div itemprop="description">
		<?php echo JHtml::_('content.prepare', $item->intro, '', 'com_sermonspeaker.intro'); ?>
	</div>
<?php endif;

if(in_array('speaker:bio', $columns) and $item->bio) : ?>
	<div itemprop="description">
		<?php echo JHtml::_('content.prepare', $item->bio, '', 'com_sermonspeaker.bio'); ?>
	</div>
<?php endif;
