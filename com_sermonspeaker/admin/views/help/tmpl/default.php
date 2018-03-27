<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;
?>
<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=help'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<h1>SermonSpeaker <?php echo $this->version; ?></h1>
		<h2>Support</h2>
		<div>If you need help with SermonSpeaker, the following links may be helpful for you:
			<ul>
				<li><a href="http://www.sermonspeaker.net/documentation.html">Documentation</a></li>
				<li><a href="http://www.sermonspeaker.net/faq.html">FAQ</a></li>
				<li><a href="http://www.sermonspeaker.net/forum.html">Forums</a></li>
			</ul>
		</div>
		<h2>License</h2>
		<div>SermonSpeaker is released under the <a href="http://www.gnu.org/licenses/gpl.html">GNU/GPL license</a></div>
		<div>Please note that the included flash players have their own licenses:
			<ul>
				<li><a href="http://www.longtailvideo.com/players/">JW Player</a> from <a href="http://www.longtailvideo.com/">LongTail Video</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/3.0/">Creative Commons Attribution-NonCommercial-ShareAlike</a> and thus free for non-commercial use. Commercial licenses are available.</li>
				<li><a href="http://flowplayer.org/">FlowPlayer</a> is licensed under <a href="http://flowplayer.org/download/license_gpl.htm">GPL</a> and thus free for use. The copyright logo can be removed with a commercial license.</li>
				<li><a href="http://wpaudioplayer.com/">WordPress Audio Player</a> from <a href="http://www.1pixelout.net/">1 Pixel Out</a> is licensed under <a href="http://wpaudioplayer.com/license/">MIT</a> and thus free for use. When using keep in mind that it only supports mp3 audiofiles.</li>
			</ul>
		</div>
		<h2>Classes</h2>
		<div>SermonSpeaker includes the following extern classes besides the players mentioned above:
			<ul>
				<li><a href="http://http://www.plupload.com/">Plupload</a> for the Uploader.</li>
				<li><a href="http://getid3.sourceforge.net">GetID3</a> for the ID3 tags lookup.</li>
				<li><a href="http://undesigned.org.za/2007/10/22/amazon-s3-php-class">Amazon S3 PHP Class</a> for the Amazon S3 support.</li>
				<li><a href="https://github.com/eduardocereto/GA-Code-Samples">Google Analytics Code Samples</a> from <a href="http://www.cardinalpath.com">Cardinal Path</a> for the Vimeo GA support.</li>
			</ul>
		</div>
	</div>
</form>
