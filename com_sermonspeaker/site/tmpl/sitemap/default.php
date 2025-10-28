<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$freq = $this->params->get('freq', 'weekly');
$prio = $this->params->get('prio', 0.5);
$uri  = Uri::getInstance();
$base = $uri->getScheme() . '://' . $uri->getHost();

if ($port = $uri->getPort())
{
	$base .= ':' . $port;
}
echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">
	<?php foreach ($this->sermons as $item) :
		if ($item->created != '0000-00-00 00:00:00'):
			$date = $item->created;
		elseif ($item->sermon_date != '0000-00-00 00:00:00'):
			$date = $item->sermon_date;
		else:
			$date = '';
		endif; ?>
		<url>
			<loc><?php echo $base . Route::_(Sermonspeaker\Component\Sermonspeaker\Site\Helper\RouteHelper::getSermonRoute($item->slug, $item->catid, $item->language)); ?></loc>
			<?php
			if ($date) : ?>
				<lastmod><?php echo HTMLHelper::date($date, 'c'); ?></lastmod><?php endif; ?>
			<changefreq><?php echo $freq; ?></changefreq>
			<priority><?php echo $prio; ?></priority>
		</url>
	<?php endforeach; ?>
</urlset>
