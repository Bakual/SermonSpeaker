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

?>

<div align="left">

<p>This humble guide may not explain things adequately. In the event you have questions, please visit our forum at 
<a href="http://joomlacode.org/gf/project/sermon_speaker/forum/" target=_new>Sermon Speaker Forum</a>  

<h2>Table of Contents</h2>
<p>
<h3>1. Introduction / About this Sermon Software</h3>
	This component allows upload of sermons to a Joomla website. You may track the activity of the hit counts for each speaker, each series, and each sermon that has been selected using the
	<img src="<?php echo JURI::root(); ?>components/com_sermonspeaker/images/stats.png"  height="38" width="38" border="0" alt="Statistics Manager" /> <b>"statistics"</b> button.<br />
	
<h3>2. Setting up the speakers</h3>
	<strong>Setting up the configuration:</strong><br />
	<p>
	You will have to check the php.ini file to be sure that the following parameters are set so that you can upload files that are as big as the sermon size:<br /><br />
	     ; Maximum size of POST data that PHP will accept.  (this assumes that no sermon will be over 20 megs in size)<br />
	     post_max_size = 20M<br />
<br />
	     ; Maximum allowed size for uploaded files.<br />
	     upload_max_filesize = 20M<br />
	     
<h3>3. Learning the work flow</h3>
<h4>Adding a new sermon</h4>
	1. To add a new sermon (mp3, flv and wmv files are currently supported), you should select the "<b>Media Manager</b>" button.<img src="<?php echo JURI::root(); ?>components/com_sermonspeaker/images/upload.png" height="38" width="38" border="0" alt="Media Manager" /><br/>
	2. You then need to  selecting the file from your PC using the "browse" button that is located on the form displayed.<br/>
	3. Next, select the "<b>upload</b>" button <img src="<?php echo JURI::root(); ?>administrator/images/upload_f2.png" alt="Upload" align="middle" name="upload" height="38" width="38" border="0" /><br/>
	4. The browser will then "work" on uploading your file.  It will take a little while depending on the size of the sermon file uploaded.<br/>
	5. The browser will automatically take you to the directory of the file that you just uploaded. You can click on the file and copy its path, you will need to insert this path when you create a new sermon.<br/>
	6. To do so, select the "<b>Add/Edit Sermon</b>" button and use the "<b>Add</b>" button to create a new sermon.<br/>
	7. The only information that is important will be the name of the sermon, the mp3(file name) path, and the sermon speaker.<br/>
	8. Then you need to select the publish/unpublish icons to set the disposition of your newly uploaded file. If you upload your files with an FTP client, specify the path relative to your Joomla installation directory (e.g. "components/com_sermonspeaker/media/default_sermon.mp3").<br/> 

<h4>Adding a directory</h4>
	1. When uploading sermons that belong to a sermon series, it may be useful to create a sub-directory to hold those files.  You may do that by entering the name of the directory in the "Create Directory" text box.  The directory is created when you select the "<b>create</b>" button:<img src="<?php echo JURI::root(); ?>administrator/images/new_f2.png" alt="Upload" align="middle" name="upload" height="38" width="38" border="0" /><br/>
	2. If you want to create a subdirectory, enter the path relative to the "/" directory and select "<b>create</b>" button. If you already have a folder called "Test" and you want to create a folder called "Files" inside of "Test", enter "/Test/Files" into the "Create Directory" text box (don't forget the first "/"!).<br/>
	
<h4>Uploading Images</h4>
	You may want to upload images of the speakers to this directory. Keeping your images in the com_sermonspeaker directory will "protect" them from other project users.
	You may upload the images just as you uploaded the audio files.  A suggestion: make a directory for the images.

<h4>Series Avatars</h4>
	Do you want to have fancy graphics for you series? To do this, you must upload your graphics into the Avatar directory.  If you don't the avatars will not show up when you want to associate them with your series. If you 
	do not want avatars for your series, then select "none" for the avatar.  Be sensible about the size of your avatars, and test them out to be sure "that's what you want".  You may upload your avatars as .jpg, .gif, .bmp or .png. files.

<h4>Deletion of Avatars</h4>
	When deleting Avatars, you should use the Media tool.  The Media tool will delete any references to that file from the database.  If you delete it in any other way, you will still have orphaned references to that file in your database.

<h4>Creating Code Links</h4>
	You will most likely want to associate the image files you upload to the speakers.  To do that, you will need to copy the location of that image file.  This is done by selecting
	the image, and then do a copy (cnt-c) of the text string in the "<b>Code</b>" text box.  You will then have to bring up the page to edit speakers and enter this string into the
	"<b>picture</b>" selection box and do a "save"<img src="<?php echo JURI::root(); ?>administrator/images/save_f2.png" alt="save" align="middle" name="save" height="38" width="38" border="0" />

<h4>Enabling Flash MP3 Player and Search Box</h4>
	If you want to enable the integrated Flash MP3 Player, you would enable the player in the configuration section.<br>
	If you need to change the bitrate of your MP3 files, I recommend <a href="http://cdexos.sourceforge.net" target="_blank">CDex - Open Source Digital Audio CD Extractor</a>.<br> 
	Search functionality is enabled by default, you can disable this in the configuration section.

<h4>Podcast / RSS Feeds</h4>
  Sermonspeaker now supports Podcasting and RSS Feeds. To use this functionality, first download, install and enable mod_sermoncast. Then enable Podcast and/or RSS feeds in the configuration page. 
  In the configuration page you can configure more options. You should only change MIME type and encoding if you know what you're doing.

<h4>Additional files</h4>
  You can publish an additional file (e.g. Slides, Notes etc) among your sermons. To do so, simply enter the path in the "Additional file name" box in the series screen. Use the format
  "/components/com_sermonspeaker/media/default_sermon.pdf" and specify a filename or description in the "Additional file name / description" box.<br>
  Sermonspeaker will add an icon depending on the filetype. If you want to replace the built-in icons you can do this by placing them in the 
  "components/com_sermonspeaker/icons". The icon file must be named as "extension.png" (for PDF files "pdf.png").

<h4>Deletion of Sermons</h4>
	Eventually you may want to delete sermons to make room for more disk space. You can do this by using the Media Manager and selecting the trash-can
	<img src="<?php echo JURI::root(); ?>administrator/components/com_media/images/edit_trash.gif" alt="delete" align="middle" name="delete" height="38" width="38" border="0" />
	icon.  This will cause a pop-up to be displayed to make sure you want to do this.  If you agree then the mp3 file will be discarded and the records in the database will be erased that refer to it.

<h4>Deletion of Speakers and Series</h4>
	Use the forms that you used to manage speakers and series items to delete the records.
	Note: You may delete a speaker or a series, but the audio files will not be deleted you will have to delete each of them as separately.

<h4>Using the Frontend Upload Wizard</h4>
  To enable the frontent upload wizard, first set a strong password (a-z,A-Z,0-9) in the configuration section. Enter it twice and hit enter. Then set the taskname to
  something unique (eg "mychurchuploading") - this makes it harder for automated attack tools to start a passwort attack. The link to the wizard is like:<br>
  <pre>http://www.mychurch.com/index.php?option=com_sermonspeaker&task=mychurchuploading</pre>(Keep this link private to increase security)<br>
  After uploading a MP3 file, SermonSpeaker can check and populate some fields according to the MP3 ID3 tags - if you don't need this feature set all ID3 tag configurations 
  to "-".

<h3>Upgrades</h3>
	Before doing anything, it is strongly recommended to create a backup of your files and database. Check out JoomlaPack if you don't already have a backup tool.<br>
  Since Version 3.3, SermonSpeaker and its modules support upgrading - there are no more upgrade packages available. Simple install the new version, 
  the database and all files are upgraded automatically. After upgrading, go to the configuration page and check out the new features. It is important that 
  you save the config to update the config files (if you don't save it, SermonSpeaker maybe produce errors!)
	<p>
<a name="copyright"></a><h3>Copyright Information</h3>
<p>Speaker Directory is released under the <a href="http://www.gnu.org" target="_blank">GNU/GPL</a> copyright.
</div>
