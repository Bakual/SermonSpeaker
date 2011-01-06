<?php
/**
* @version $Id:  3.3$
* @package sermonSpeaker
* @Email martin.zh@gmail.com
* @Website - http://joomlacode.org/gf/project/sermon_speaker/
* @copyright Copyright (C) 2006 Steve Shiflett. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* This file is part of sermonSpeaker.
* sermonSpeaker is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* sermonSpeaker is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* See COPYRIGHT.php for copyright notices and details.
*/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
?>

<div align="left">
<p>This humble guide may not explain things adequately. In the event you have questions, please visit our forum at 
<a href="http://joomlacode.org/gf/project/sermon_speaker/forum/" target=_new>Sermon Speaker Forum</a></p>
<h2>1. Introduction / About this SermonSpeaker Software</h2>
<p>This component allows upload of sermons to a Joomla website. You may track the activity of the hit counts for each speaker, each series, and each sermon that has been selected using the <a href="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=statistics'); ?>"><img src="<?php echo JURI::base(); ?>components/com_sermonspeaker/images/stats-16.png" border="0" title="Statistics" alt="Statistics" /></a> button or the "Statistics" menu entry.</p>
<h2>2. Setting up the configuration</h2>
<p>You will have to check the php.ini file to be sure that the following parameters are set so that you can upload files that are as big as the sermon size:</p>
<ul>
	<li>Maximum size of POST data that PHP will accept. (this assumes that no sermon will be over 20 megs in size):
		<pre>post_max_size = 20M</pre></li>
	<li>Maximum allowed size for uploaded files:
		<pre>upload_max_filesize = 20M</pre></li>
</ul>
<h2>3. Learning the work flow</h2>
<h3>Adding a new sermon</h3>
<ol>
	<li>First make sure you have saved the settings in SermonSpeaker once as it will give you all sort of errors if you didn't do that.</li>
	<li>To add a new sermon (mp3, m4a, flv, mp4 and wmv files are currently supported), you first have to upload the file. For this you can use any FTP client, the SermonSpeaker frontend upload or the Joomla Media Manager, accessible in the "Site" Menu (You can set the directory for the Media Manager in the Joomla Global Configuration). Upload the file to the directory you have specified in the SermonSpeaker settings. Default is /images for new installations and /components/com_sermonspeaker/media for older ones.</li>
	<li>After the file is placed onto the server click the <a href="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=sermons'); ?>"><img src="<?php echo JURI::base(); ?>components/com_sermonspeaker/images/sermon-16.png" border="0" title="Add/Edit Sermons" alt="Add/Edit Sermons" /></a> button or the Sermons menu entry.</li>
	<li>There you use the "New" button to create a new sermon in SermonSpeaker.
	<li>The only information that is important will be the Sermon Title, the Sermon Path, and the Speaker.</li>
	<li>Then you need to select the publish/unpublish icons to set the disposition of your newly uploaded file. If you upload your files with an FTP client, specify the path relative to your Joomla installation directory (e.g. "images/sermons/sermon.mp3").</li>
</ol>
<h3>Series Avatars</h3>
<p>Do you want to have fancy graphics for you series? To do this, you must upload your graphics into the avatar directory set in the settings. If you do not want avatars for your series, then select "none" for the avatar. Be sensible about the size of your avatars, and test them out to be sure "that's what you want". You may upload your avatars as .jpg, .gif, .bmp or .png files.</p>
<h3>Enabling Flash MP3 Player</h3>
<ul>
	<li>If you want to enable the integrated Flash MP3 Player, you would enable the player in the configuration section.</li>
	<li>If you need to change the bitrate of your MP3 files, I recommend <a href="http://cdexos.sourceforge.net" target="_blank">CDex - Open Source Digital Audio CD Extractor</a>.</li>
</ul>
<h3>Podcast / RSS Feeds</h3>
<p>Sermonspeaker supports Podcasting and RSS Feeds. You can use this functionality either by creating a menu entry for the RSS Feed, or use the Modul SermonCast which will show PodCast and RSS links in a modul position.<br>
In the SermonSpeaker parameters you can configure many options for the feed and podcast. You should only change MIME type and encoding if you know what you're doing.</p>
<h3>Additional files</h3>
<p>You can publish an additional file (e.g. Slides, Notes etc) among your sermons. To do so, enter the path in the "Additional file name" box in the sermon form. Use the format "/images/sermons/sermon.pdf" and specify a filename or description in the "Additional file name / description" box. The dropdown list will contain all .pdf, .zip, .ppt and .doc-files found in the sermons directory.<br>
Sermonspeaker will add an icon depending on the filetype. If you want to replace the built-in icons you can do this by placing them in the "/components/com_sermonspeaker/icons". The icon file must be named as "extension.png" (for PDF files "pdf.png").</p>
<h3>Deletion of Sermons</h3>
<p>Eventually you may want to delete sermons to make room for more disk space. You can do this deleting the sermon in the Sermons Manager. Deleting the sermon there will also try to delete the file on the server.
If you just want to hide the sermon from public view, unpublish it instead.</p>
<h3>Deletion of Speakers and Series</h3>
<p>Use the Speaker/Series Manager that you used to manage speakers and series items to delete the records.
Note: You may delete a speaker or a series, but the audio files will not be deleted you will have to delete each of them as separately.</p>
<h3>Using the Frontend Upload Wizard</h3>
<p>To enable the frontent upload wizard, first enable it in the parameter settings for SermonSpeaker. You now have two ways how the frontend upload wizard can work:
<ul>
	<li>Set a strong password (a-z,A-Z,0-9) and set a taskname to something unique (eg "mychurchuploading") - this makes it harder for automated attack tools to start a passwort attack. The link to the wizard is then like (Keep this link private to increase security):<pre>http://www.mychurch.com/index.php?option=com_sermonspeaker&view=frontendupload&frup=mychurchuploading</pre></li>
	<li>Set allowed usergroups. In this case no password is needed, but the user needs to be logged in. You have to specify every usergroup that needs access, 'Registered' doesn't include 'Author'. They now can access the wizard by a menu entry or the link <pre>http://www.mychurch.com/index.php?option=com_sermonspeaker&view=frontendupload</pre></li>
</ul>
After uploading a MP3 file, SermonSpeaker can check and populate some fields according to the MP3 ID3 tags - if you don't need this feature set all ID3 tag configurations to "-".</p>
<h2>4: Upgrades</h2>
<p>Before doing anything, it is strongly recommended to create a backup of your files and database. Check out JoomlaPack if you don't already have a backup tool.<br>
Since Version 3.3, SermonSpeaker and its modules support upgrading - there are no more upgrade packages available. Simple install the new version, the database and all files are upgraded automatically. After upgrading, go to the configuration page and check out the new features. It is important that you save the config to update the config files (if you don't save it, SermonSpeaker maybe produce errors!)</p>
<h2>5: Copyright Information</h2>
<p>Speaker Directory is released under the <a href="http://www.gnu.org" target="_blank">GNU/GPL</a> copyright.</p>
</div>