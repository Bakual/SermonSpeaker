<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;

?>
<form action="<?php echo Route::_('index.php?option=com_sermonspeaker&view=help'); ?>" method="post" name="adminForm"
	  id="adminForm">
	<div id="j-main-container" class="j-main-container">
		<div class="card m-3">
			<h1 class="card-header">SermonSpeaker <?php echo $this->version; ?></h1>
			<div class="card-body">
				<h2 class="card-title">Support</h2>
				<p class="card-text">If you need help with SermonSpeaker, the following links may be helpful for
					you:</p>
				<a href="http://www.sermonspeaker.net/documentation.html" class="card-link">Documentation</a>
				<a href="http://www.sermonspeaker.net/faq.html" class="card-link">FAQ</a>
				<a href="http://www.sermonspeaker.net/forum.html" class="card-link">Forums</a>
			</div>
		</div>
		<div class="card m-3">
			<div class="card-body">
				<h2 class="card-title">License</h2>
				<div class="card-text">
					SermonSpeaker is released under the <a href="http://www.gnu.org/licenses/gpl.html">GNU/GPL
						license</a><br>
				</div>
			</div>
		</div>
		<div class="card m-3">
			<div class="card-body">
				<h2 class="card-title">Classes</h2>
				<div class="card-text">SermonSpeaker includes the following extern classes:
					<ul>
						<li><a href="http://http://www.plupload.com/">Plupload</a> for the Uploader.</li>
						<li><a href="http://getid3.sourceforge.net">GetID3</a> for the ID3 tags lookup.</li>
						<li><a href="https://aws.amazon.com/de/sdk-for-php/">Amazon AWS SDK for PHP</a>
							for the Amazon S3 support.
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</form>
