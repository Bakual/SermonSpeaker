<?php
defined('_JEXEC') or die('Restricted access');

echo '<?xml version="1.0" encoding="utf-8" ?>';
?>

<rss version="2.0" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd">
    <channel>
        <title><?php echo $this->rss->title; ?></title>
        <link><?php echo $this->rss->link; ?></link>
        <description><?php echo $this->rss->description; ?></description>
<?php if($this->rss->itCategory1 != '' || $this->rss->itCategory2 != '' || $this->rss->itCategory3 != '') {
			echo $this->make_itCat($this->rss->itCategory1);
			echo $this->make_itCat($this->rss->itCategory2);
			echo $this->make_itCat($this->rss->itCategory3);
		} ?>
        <lastBuildDate><?php echo gmdate("r"); // todo: include Timezone from Joomla ?></lastBuildDate>
        <generator>SermonSpeaker 3.4</generator>
<?php if ($this->rss->language!="") { ?>
        <language><?php echo $this->rss->language; ?></language>
<?php } 
	if ($this->rss->copyright!="") { ?>
        <copyright><?php echo $this->rss->copyright; ?></copyright>
<?php }
	if ($this->rss->itSubtitle != "") { ?>
        <itunes:subtitle><?php echo $this->rss->itSubtitle; ?></itunes:subtitle>
<?php }
	if ($this->rss->itAuthor != "") { ?>
        <itunes:author><?php echo $this->rss->itAuthor; ?></itunes:author>
<?php }
	if (is_array($this->rss->itOwner)) { ?>
        <itunes:owner>
<?php if($this->rss->itOwner['name']) { ?>
            <itunes:name><?php echo $this->rss->itOwner['name']; ?></itunes:name>
<?php }
		if($this->rss->itOwner['email']) { ?>
            <itunes:email><?php echo $this->rss->itOwner['email']; ?></itunes:email>
<?php } ?>
    	</itunes:owner>
<?php }
	if ($this->rss->itImage != "") { ?>
        <itunes:image href="<?php echo $this->rss->itImage; ?>"></itunes:image>
        <image>
            <link><?php echo $this->rss->link; ?></link>
            <url><?php echo $this->rss->itImage; ?></url>
            <title><?php echo $this->rss->title; ?></title>
            <description>
            <?php echo $this->rss->description."\n"; ?>
            </description>
        </image>
<?php } ?>
        <itunes:explicit>no</itunes:explicit>
<?php if ($this->rss->itKeywords != "") { ?>
        <itunes:keywords><?php echo $this->rss->itKeywords; ?></itunes:keywords>
<?php }
	if ($this->rss->newfeedurl) { ?>
        <itunes:new-feed-url><?php echo $this->rss->newfeedurl; ?></itunes:new-feed-url>
<?php }
	// starting with items
	foreach ($this->items as $item) { ?>
        <item>
<?php if ($item->enclosure) { 
		$enc = $item->enclosure; ?>
            <enclosure url="<?php echo $enc['url']; ?>" length="<?php echo $enc['length']; ?>" type="<?php echo $enc['type']; ?>"></enclosure>
<?php } ?>
            <title><?php echo $item->title; ?></title>
            <link><?php echo $item->link; ?></link>
            <author><?php echo $item->author; ?></author>
            <description></description>
<?php if ($item->date != "") { ?>
            <pubDate><?php echo JHTML::Date($item->date, "%a, %d %b %Y %H:%M:%S"); ?></pubDate>
<?php }
		if ($item->guid != "") { ?>
            <guid><?php echo $item->guid; ?></guid>
<?php }
		// iTunes specific: per item
		if ($item->itAuthor != "") { ?>
            <itunes:author><?php echo $item->itAuthor; ?></itunes:author>
<?php }
		if ($item->itDuration != "") { ?>
            <itunes:duration><?php echo $item->itDuration; ?></itunes:duration>
<?php } ?>
            <itunes:explicit>no</itunes:explicit>
<?php if ($item->itKeywords != "") { ?>
            <itunes:keywords><?php echo $item->itKeywords; ?></itunes:keywords>
<?php }
		if ($item->itSubtitle != "") { ?>
            <itunes:subtitle><?php echo $item->itSubtitle; ?></itunes:subtitle>
<?php } ?>
        </item>
<?php } ?>
    </channel>
</rss>