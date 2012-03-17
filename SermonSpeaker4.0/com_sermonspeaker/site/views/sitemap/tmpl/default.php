<?php
defined('_JEXEC') or die('Restricted access');
echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($this->sermons as $item): ?>
	<url>
		<loc><?php echo JURI::root().trim(JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug).'&Itemid=1379'), '/'); ?></loc>
		<lastmod><?php echo JHtml::Date($item->sermon_date, 'c'); ?></lastmod>
		<changefreq>weekly</changefreq>
		<priority>0.5</priority>
	</url>
<?php endforeach; ?>
</urlset>