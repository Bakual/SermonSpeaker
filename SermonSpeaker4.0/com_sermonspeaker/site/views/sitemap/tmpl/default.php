<?php
defined('_JEXEC') or die('Restricted access');
$itemid	= $this->params->get('menuitem', 0);
$freq	= $this->params->get('freq', 'weekly');
$prio	= $this->params->get('prio', 0.5);
echo '<?xml version="1.0" encoding="UTF-8" ?>';?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($this->sermons as $item): ?>
	<url>
		<loc><?php echo JURI::root().trim(JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug).'&Itemid='.$itemid), '/'); ?></loc>
		<lastmod><?php echo JHtml::Date($item->sermon_date, 'c'); ?></lastmod>
		<changefreq><?php echo $freq; ?></changefreq>
		<priority><?php echo $prio; ?></priority>
	</url>
<?php endforeach; ?>
</urlset>