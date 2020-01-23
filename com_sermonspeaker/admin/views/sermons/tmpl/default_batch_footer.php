<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2019 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

?>
<button type="button" class="btn btn-secondary" data-dismiss="modal"
	onclick="document.getElementById('batch-category-id').value='';document.getElementById('batch-language-id').value='';document.getElementById('batch-tag-id').value='';document.getElementById('batch-speaker-id').value='';document.getElementById('batch-serie-id').value=''">
	<?php echo Text::_('JCANCEL'); ?>
</button>
<button type="submit" class="btn btn-success" onclick="Joomla.submitbutton('sermon.batch');return false;">
	<?php echo Text::_('JGLOBAL_BATCH_PROCESS'); ?>
</button>
