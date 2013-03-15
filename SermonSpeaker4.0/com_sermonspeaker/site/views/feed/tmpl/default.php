<?php defined('_JEXEC') or die;
$params	= $this->params;

// required channel elements
echo '<?xml version="1.0" encoding="utf-8" ?>'; ?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
	<title><?php echo $this->make_xml_safe($params->get('sc_title')); ?></title>
	<link><?php echo JURI::root(); ?></link>
	<atom:link href="<?php echo $this->make_xml_safe(JURI::getInstance()->toString()); ?>" rel="self" type="application/rss+xml" />
	<description><?php echo $this->make_xml_safe($params->get('description')); ?></description>
<?php // optional channel elements ?>
	<generator>SermonSpeaker 4.5</generator>
	<lastBuildDate><?php echo gmdate("r"); ?></lastBuildDate>
<?php if ($params->get('itLanguage')) : ?>
	<language><?php echo $this->make_xml_safe($params->get('itLanguage')); ?></language>
<?php endif;
if ($params->get('editor')) :
	if ($params->get('editorEmail')) : ?>
	<managingEditor><?php echo $this->make_xml_safe($params->get('editorEmail')); ?> (<?php echo $this->make_xml_safe($params->get('editor')); ?>)</managingEditor>
<?php else : ?>
	<dc:creator><?php echo $this->make_xml_safe($params->get('editor')); ?></dc:creator>
<?php endif;
endif;
if ($params->get('copyright')) : ?>
	<copyright><?php echo $this->make_xml_safe($params->get('copyright')); ?></copyright>
<?php endif;
// Custom Code
	echo $params->get('sc_custom');
// iTunes tags ?>
	<itunes:summary><?php echo $this->make_xml_safe($params->get('description')); ?></itunes:summary>
<?php if ($params->get('editor')) : ?>
	<itunes:author><?php echo $this->make_xml_safe($params->get('editor')); ?></itunes:author>
<?php endif;
if ($params->get('itCategory1')) : ?>
	<itunes:category text="<?php echo $this->make_itCat($params->get('itCategory1'));
endif;
if ($params->get('itCategory2')) : ?>
	<itunes:category text="<?php echo $this->make_itCat($params->get('itCategory2'));
endif;
if ($params->get('itCategory3')) : ?>
	<itunes:category text="<?php echo $this->make_itCat($params->get('itCategory3'));
endif;
if ($params->get('itSubtitle')) : ?>
	<itunes:subtitle><?php echo $this->make_xml_safe($params->get('itSubtitle')); ?></itunes:subtitle>
<?php endif;
if ($params->get('itOwnerName') or $params->get('itOwnerEmail')) : ?>
	<itunes:owner>
	<?php if ($params->get('itOwnerName')) : ?>
		<itunes:name><?php echo $params->get('itOwnerName'); ?></itunes:name>
	<?php endif;
	if ($params->get('itOwnerEmail')) : ?>
		<itunes:email><?php echo $params->get('itOwnerEmail'); ?></itunes:email>
	<?php endif; ?>
	</itunes:owner>
<?php endif;
if ($params->get('itImage')) : ?>
	<itunes:image href="<?php echo SermonspeakerHelperSermonspeaker::makeLink($params->get('itImage'), true); ?>" />
<?php endif;
if ($params->get('rssImage')) : ?>
	<image>
		<url><?php echo SermonspeakerHelperSermonspeaker::makeLink($params->get('rssImage'), true); ?></url>
		<title><?php echo $this->make_xml_safe($params->get('sc_title')); ?></title>
		<link><?php echo JURI::root(); ?></link>
		<description><?php echo $this->make_xml_safe($params->get('description')); ?></description>
	</image>
<?php endif; ?>
	<itunes:explicit>no</itunes:explicit>
<?php if ($params->get('itKeywords')) : ?>
	<itunes:keywords><?php echo $this->make_xml_safe($params->get('itKeywords')); ?></itunes:keywords>
<?php endif;
if ($params->get('itRedirect')) : ?>
	<itunes:new-feed-url><?php echo $this->make_xml_safe($params->get('itRedirect')); ?></itunes:new-feed-url>
<?php endif;
// starting with items
foreach ($this->items as $item) :
	$item_link	= SermonspeakerHelperRoute::getSermonRoute($item->id);
	$notes		= $this->getNotes($item->notes); ?>
	<item>
		<title><?php echo $this->make_xml_safe($item->sermon_title); ?></title>
		<link><?php echo $this->make_xml_safe(JURI::root().trim(JRoute::_($item_link), '/')); ?></link>
		<guid><?php echo $this->make_xml_safe(JURI::root().$item_link); ?></guid>
<?php // todo: maybe add email of speaker if present (not yet in database), format is emailadress (name) and then use author instead ?>
		<dc:creator><?php echo $this->make_xml_safe($item->name); ?></dc:creator>
		<description><?php echo $notes; ?></description>
		<pubDate><?php echo JHtml::Date($item->sermon_date, 'r', true); ?></pubDate>
<?php if ($enclosure = $this->getEnclosure($item)) : ?>
		<enclosure url="<?php echo $enclosure['url']; ?>" length="<?php echo $enclosure['length']; ?>" type="<?php echo $enclosure['type']; ?>"></enclosure>
<?php endif;
// iTunes specific: per item
if ($item->picture) : ?>
		<itunes:image href="<?php echo SermonspeakerHelperSermonspeaker::makeLink($item->picture, true); ?>" />
<?php elseif ($params->get('itImage')) : ?>
		<itunes:image href="<?php echo SermonspeakerHelperSermonspeaker::makeLink($params->get('itImage'), true); ?>" />
<?php endif; ?>
		<itunes:author><?php echo $this->make_xml_safe($item->name); ?></itunes:author>
		<itunes:duration><?php echo SermonspeakerHelperSermonSpeaker::insertTime($item->sermon_time); ?></itunes:duration>
		<itunes:explicit>no</itunes:explicit>
		<itunes:keywords><?php echo $this->getKeywords($item); ?></itunes:keywords>
		<itunes:subtitle><?php echo (strlen($notes) > 255) ? mb_substr($notes, 0, 252, 'UTF-8').'...' : $notes; ?></itunes:subtitle>
		<itunes:summary><?php echo (strlen($notes) > 4000) ? mb_substr($notes, 0, 3997, 'UTF-8').'...' : $notes; ?></itunes:summary>
	</item>
<?php endforeach; ?>
  </channel>
</rss>