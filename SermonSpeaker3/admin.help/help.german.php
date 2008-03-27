<?php
/**
* @version $Id:  2.5.1$
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

?>

<div align="left">

<p>This humble guide may not explain things adequately. In the event you have a question, please visit our forum at 
<a href="http://joomlacode.org/gf/project/sermon_speaker/" target=_new>http://joomlacode.org/gf/project/sermon_speaker/</a>  

<h2>Table of Contents</h2>
<p>
<h3>1. Introduction / About this Sermon Software</h3><br />
	This component allows one to upload/describe sermons to a Joomla website. You may track the activity of the hit counts for each speaker, each series, and each sermon that has been selected using the
	<img src="<?php echo JURI::root(); ?>components/com_sermonspeaker/images/stats.png"  height="48" width="48" border="0" alt="Statistics Manager" /> <b>"statistics"</b> button.<br />
<h3>2. Setting up the speakers</h3><br />
	&nbsp;&nbsp;a. Setting up the configuration<br />
	You will have to check the php.ini file to be sure that the following parameters are set so that you can upload files that are as big as the sermon size:<br /><br />
	     ; Maximum size of POST data that PHP will accept.  (this assumes that no sermon will be over 20 megs in size... at least we hope so!)<br />
	     post_max_size = 20M<br />
<br />
	     ; Maximum allowed size for uploaded files.<br />
	     upload_max_filesize = 20M<br />
<h3>2. Learning the work flow</h3>
<h4>Adding a new sermon</h4>
	To add a new sermon, you should select the "<b>Media Manager</b>" button.<img src="<?php echo JURI::root(); ?>components/com_sermonspeaker/images/upload.png" height="48" width="48" border="0" alt="Media Manager" /><br/>
	You then need to  selecting the file from your PC using the "browse" button that is located on the form displayed.<br/>
	Next, select the "<b>upload</b>" button <img src="<?php echo JURI::root(); ?>administrator/images/upload_f2.png" alt="Upload" align="middle" name="upload" border="0" />
	The browser will then "work" on uploading your file.  It will take a little while depending on the size of the sermon file uploaded.<br/>
	The browser will automatically take you to the screen which will detail the information about the sermon file that you just uploaded.
	The only information important there will be the name of the sermon, the mp3 Path, and the sermon speaker.<br/><br/>
	You will then need select the publish/unpublish icons to set the disposition of your newly uploaded file. If you upload your files with an FTP client, specify the path relative to your 
  Joomla installation directory (eg "components/com_sermonspeaker/media/default_sermon.mp3") 
<h4>Adding a directory</h4>
	When uploading sermons that belong to a sermon series, it may be useful to create a sub-directory to hold those files.  You may do that by entering the name of the directory in the
	"Create Directory" text box.  The directory is created when you select the "<b>create</b>" button:
	<img src="<?php echo JURI::root(); ?>administrator/images/new_f2.png" alt="Upload" align="middle" name="upload" border="0" /><br/>
	If you want to create a subdirectory, enter the path relative to the "/" directory and select "<b>create</b>" button. So if you already have a folder called "Test" and you 
	want to create a folder called "Files" inside of "Test", enter "/Test/Files" into the "Create Directory" text box (don't forget the first "/"!).
<h4>Uploading Images</h4>
	You may want to upload images of the speakers to this directory.  Keeping your images in the com_sermonspeaker directory will "protect" them from other project users.
	You may upload the images just as you upload the audio files.  Suggestion: make a directory for the images.
<h4>Series Avatars</h4>
	Want to have fancy graphics for you series?  To do this, you must upload your graphics into the avatar directory.  If you don't, the avatars will not show up when you want to associate them with your series.  If you 
	do not want avatars for your series, then select "none" for the avatar.  Be sensible about the size of your avatars, and test them out to be sure "that's what you want".  You may upload your avatars as
	.jpg, .gif, .bmp or .png.
<h4>Deletion of Avatars</h4>
	When deleting Avatars, you should use the Media tool.  The media tool will delete any references to that file from the database.  If you delete it any other way, you will still have orphaned references to that file in your database.
<h4>Creating Code Links</h4>
	You will probably want to associate the image files you upload to the speakers.  To do that, you will need to copy the location of that image file.  This is done by selecting
	the image of interest, and then do a copy (cnt-c) of the text string in the "<b>Code</b>" text box.  You will then have to bring up the page to edit speakers and enter this string into the
	"<b>picture</b>" selection box and do a "save"<img src="<?php echo JURI::root(); ?>administrator/images/save_f2.png" alt="save" align="middle" name="save" border="0" />
<h4>Enabling Flash MP3 Player and Search Box</h4>
	If you want to enable the integrated Flash MP3 Player, you can enable the player in the configuration section.<br>
	If you need to change the bitrate of your MP3 files, I recommend <a href="http://cdexos.sourceforge.net" target="_blank">http://cdexos.sourceforge.net</a>.<br> 
	Search functionality is enabled by default, you could disable this box in the configuration section, too.
<h4>Podcast / RSS Feeds</h4>
  Sermonspeaker now supports Podcasting and RSS Feeds. To use this functionality, first download, install and enable mod_sermoncast. Then enable Podcast and/or RSS feeds in the configuration page. 
  There, you can also configure some more options. You should only change MIME type and encoding if you know what you're doing...
<h4>Deletion of Sermons</h4>
	Eventually, you may want to delete sermons to make room for more disk space.  You do this by using the Media Manager and selecting the trash-can
	<img src="<?php echo JURI::root(); ?>administrator/components/com_media/images/edit_trash.gif" alt="delete" align="middle" name="delete" border="0" />
	icon.  This will cause a pop-up to be displayed to make sure you want to do this.  If you agree, then
	the mp3 file will be discarded and the records in the database will be erased that refer to it.
<h4>Deletion of Speakers and Series</h4>
	Use the forms used to manage speakers and series items to delete the records.
	Note: You may delete a speaker or a series, but the audio files will not be deleted,
	so you will have to delete each ofthem as necessary..
<h3>Upgrades</h3>
	To do an upgrade of SermonSpeaker from one version to the next, you must first backup your media files.  (That is your mp3 files and images.)<br> 
	A easy way to backup your files is to do the following: move the "xxx\components\com_sermonspeaker\media" to directory "xxx\components\media". You can do this with your ftp client (Filezilla, or whatever you use to load files to your site.)<br> 
	Next, you go to the administrator for "installing components", select the SermonSpeaker component and do a "de-install".<br>
	The de-install will not blow away your database.  However, your directories with all the media will be blown away..  We moved the directory to avoid this unplesant event.<br>
	Now you may install the new version of SermonSpeaker. <br> 
	After successfully installing the new version of Sermon Speaker, replace the newly created "xxx\components\com_sermonspeaker\media" directory with "xxx\components\media" that you squirreled away earlier. <br>
	That's it!  You are good to go!  
</p>
<a name="copyright"></a><h3>Copyright Information</h3>
<p>Speaker Directory is released under the <a href="http://www.gnu.org" target="_blank">GNU/GPL</a> copyright.
</div>
