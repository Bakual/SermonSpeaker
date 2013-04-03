<?php
defined('_JEXEC') or die;
$freq	= $this->params->get('freq', 'weekly');
$prio	= $this->params->get('prio', 0.5);
$uri	= JURI::getInstance();
$base	= $uri->getScheme().'://'.$uri->getHost();
if($port = $uri->getPort()){
	$base	.= ':'.$port;
}
echo '<?xml version="1.0" encoding="UTF-8" ?>';?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($this->sermons as $item):
	if ($item->created != '0000-00-00 00:00:00'):
		$date	= $item->created;
	elseif ($item->sermon_date != '0000-00-00 00:00:00'):
		$date	= $item->sermon_date;
	else:
		$date	= '';
	endif; ?>
	<url>
		<loc><?php echo $base.JRoute::_(SermonspeakerHelperRoute::getSermonRoute($item->slug)); ?></loc>
		<?php if ($date): ?><lastmod><?php echo JHtml::Date($date, 'c'); ?></lastmod><?php endif; ?>
 		<changefreq><?php echo $freq; ?></changefreq>
		<priority><?php echo $prio; ?></priority>
	</url>
<?php endforeach; ?>
</urlset>