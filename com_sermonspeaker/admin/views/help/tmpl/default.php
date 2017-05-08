<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2016 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;
?>
<form action="<?php echo JRoute::_('index.php?option=com_sermonspeaker&view=help'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div id="j-sidebar-container" class="col-md-2">
			<?php echo $this->sidebar; ?>
		</div>
		<div class="col-md-10">
			<div id="j-main-container" class="j-main-container">
				<div class="card">
					<div class="card-header"><h1>SermonSpeaker <?php echo $this->version; ?></h1></div>
					<div class="card-block">
						<h2 class="card-title">Support</h2>
						<p class="card-text">If you need help with SermonSpeaker, the following links may be helpful for you:</p>
						<a href="http://www.sermonspeaker.net/documentation.html" class="card-link">Documentation</a>
						<a href="http://www.sermonspeaker.net/faq.html" class="card-link">FAQ</a>
						<a href="http://www.sermonspeaker.net/forum.html" class="card-link">Forums</a>
					</div>
				</div>
				<div class="card">
					<div class="card-block">
						<h2 class="card-title">License</h2>
						<p class="card-text">
							SermonSpeaker is released under the <a href="http://www.gnu.org/licenses/gpl.html">GNU/GPL license</a><br>
							Please note that the included flash players have their own licenses:
							<ul>
								<li><a href="http://www.longtailvideo.com/players/">JW Player</a> from <a href="http://www.longtailvideo.com/">LongTail Video</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/3.0/">Creative Commons Attribution-NonCommercial-ShareAlike</a> and thus free for non-commercial use. Commercial licenses are available.</li>
								<li><a href="http://flowplayer.org/">FlowPlayer</a> is licensed under <a href="http://flowplayer.org/download/license_gpl.htm">GPL</a> and thus free for use. The copyright logo can be removed with a commercial license.</li>
								<li><a href="http://wpaudioplayer.com/">WordPress Audio Player</a> from <a href="http://www.1pixelout.net/">1 Pixel Out</a> is licensed under <a href="http://wpaudioplayer.com/license/">MIT</a> and thus free for use. When using keep in mind that it only supports mp3 audiofiles.</li>
							</ul>
						</p>
					</div>
				</div>
				<div class="card">
					<div class="card-block">
						<h2 class="card-title">Classes</h2>
						<p class="card-text">
							SermonSpeaker includes the following extern classes besides the players mentioned above:
							<ul>
								<li><a href="http://http://www.plupload.com/">Plupload</a> for the Uploader.</li>
								<li><a href="http://getid3.sourceforge.net">GetID3</a> for the ID3 tags lookup.</li>
								<li><a href="http://undesigned.org.za/2007/10/22/amazon-s3-php-class">Amazon S3 PHP Class</a> for the Amazon S3 support.</li>
								<li><a href="https://github.com/eduardocereto/GA-Code-Samples">Google Analytics Code Samples</a> from <a href="http://www.cardinalpath.com">Cardinal Path</a> for the Vimeo GA support.</li>
							</ul>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
