<?php
defined('_JEXEC') or die('Restricted access');
$freq	= $this->params->get('freq', 'weekly');
$prio	= $this->params->get('prio', 0.5);
$uri	= JURI::getInstance();
$base	= $uri->getScheme().'://'.$uri->getHost();
if($port = $uri->getPort()){
	$base	.= ':'.$port;
}
echo '<?xml version="1.0" encoding="UTF-8" ?>';?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($this->sermons as $item): ?>
	<url>
		<loc><?php echo $base.JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug)); ?></loc>
		<lastmod><?php echo JHtml::Date($item->sermon_date, 'c'); ?></lastmod>
		<changefreq><?php echo $freq; ?></changefreq>
		<priority><?php echo $prio; ?></priority>
	</url>
<?php endforeach; ?>
</urlset>