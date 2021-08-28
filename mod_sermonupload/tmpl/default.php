<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Module.SermonUpload
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2020 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * @var array                     $types
 * @var string                    $identifier
 */

HTMLHelper::_('stylesheet', 'com_sermonspeaker/frontendupload.css', array('relative' => true));
?>
<div class="sermonupload">
	<div id="upload_limit" class="card bg-light">
		<div class="card-body">
			<?php echo Text::sprintf('MOD_SERMONUPLOAD_UPLOAD_LIMIT', ModSermonuploadHelper::getMaxUploadValue()); ?>
		</div>
	</div>
	<?php foreach ($types as $type): ?>
		<?php $id = $identifier . $type; ?>
		<div id="<?php echo $id; ?>_drop" class="drop-area">
			<div class="upload-header"><?php echo Text::_('MOD_SERMONUPLOAD_UPLOAD_' . $type . 'FILE'); ?></div>
			<div id="plupload_<?php echo $id; ?>" class="uploader">
				<div id="filelist_<?php echo $id; ?>" class="filelist"></div>
				<a id="browse_<?php echo $id; ?>" href="javascript:" class="btn btn-small">
					<?php echo Text::_('MOD_SERMONIPLOAD_UPLOAD'); ?>
				</a>
			</div>
		</div>
	<?php endforeach; ?>
</div>
