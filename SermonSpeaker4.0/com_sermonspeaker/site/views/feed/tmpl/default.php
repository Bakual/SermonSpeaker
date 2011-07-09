<?php
defined('_JEXEC') or die('Restricted access');

// required channel elements
echo '<?xml version="1.0" encoding="utf-8" ?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:atom="http://www.w3.org/2005/Atom">
   <channel>
	<title>'.$this->channel->title.'</title>
	<link>'.$this->channel->link.'</link>
	<atom:link href="http://'.$this->channel->atomlink.'" rel="self" type="application/rss+xml" />
	<description>'.$this->channel->description.'</description>';
// optional channel elements
echo '
	<generator>SermonSpeaker 4.1</generator>
	<lastBuildDate>'.gmdate("r").'</lastBuildDate>';
if ($this->channel->language!="") {
	echo '
	<language>'.$this->channel->language.'</language>';
}
if ($this->channel->manEditor != "") {
	echo '
	'.$this->channel->manEditor;
}
if ($this->channel->copyright!="") {
	echo '
	<copyright>'.$this->channel->copyright.'</copyright>';
}
// iTunes tags
echo '
	<itunes:summary>'.$this->channel->itSummary.'</itunes:summary>';
if ($this->channel->itAuthor != "") {
	echo '
	<itunes:author>'.$this->channel->itAuthor.'</itunes:author>';
}
if($this->channel->itCategories != '') {
	echo $this->channel->itCategories;
}
if ($this->channel->itSubtitle != "") {
	echo '
	<itunes:subtitle>'.$this->channel->itSubtitle.'</itunes:subtitle>';
}
if (is_array($this->channel->itOwner)) {
	echo '
	<itunes:owner>';
	if($this->channel->itOwner['name']) {
		echo '
		<itunes:name>'.$this->channel->itOwner['name'].'</itunes:name>';
	}
	if($this->channel->itOwner['email']) {
		echo '
		<itunes:email>'.$this->channel->itOwner['email'].'</itunes:email>';
	}
	echo '
	</itunes:owner>';
}
if ($this->channel->itImage) {
	echo '
	<itunes:image href="'.$this->channel->itImage.'" />
	<image>
		<url>'.$this->channel->itImage.'</url>
		<title>'.$this->channel->title.'</title>
		<link>'.$this->channel->link.'</link>
		<description>'.$this->channel->description.'</description>
	</image>';
}
	echo '
	<itunes:explicit>no</itunes:explicit>';
if ($this->channel->itKeywords != "") {
	echo '
	<itunes:keywords>'.$this->channel->itKeywords.'</itunes:keywords>';
}
if ($this->channel->itNewfeedurl) {
	echo '
	<itunes:new-feed-url>'.$this->channel->itNewfeedurl.'</itunes:new-feed-url>';
}
// starting with items
foreach ($this->items as $item) {
	echo '
	<item>
		<title>'.$item->title.'</title>
		<link>'.$item->link.'</link>
		<guid>'.$item->guid.'</guid>
		'.$item->author.'
		<description>'.$item->description.'</description>
		<pubDate>'.$item->date.'</pubDate>';
	if ($item->enclosure) {
        echo '
		<enclosure url="'.$item->enclosure['url'].'" length="'.$item->enclosure['length'].'" type="'.$item->enclosure['type'].'"></enclosure>';
	}
	// iTunes specific: per item
		echo '
		<itunes:author>'.$item->itAuthor.'</itunes:author>
		<itunes:duration>'.$item->itDuration.'</itunes:duration>
		<itunes:explicit>no</itunes:explicit>
		<itunes:keywords>'.$item->itKeywords.'</itunes:keywords>
		<itunes:subtitle>'.$item->itSubtitle.'</itunes:subtitle>
		<itunes:summary>'.$item->itSummary.'</itunes:summary>
		<itunes:image>'.$item->itImage.'</itunes:image>
	</item>';
}
echo '
   </channel>
</rss>';