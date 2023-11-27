<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2022 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$base = Uri::base();

$js = 'function CheckProgress() {
		var xmlhttp = new XMLHttpRequest();
		var t = 0;
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				var data = JSON.parse(xmlhttp.responseText);
				if (data.status==1){
					var progressbar = document.getElementById("progressbar");
					progressbar.style.width = data.msg + "%";
					progressbar.innerHTML = data.msg + "%";
					timeout = setTimeout(CheckProgress,100);
				} else if (data.status==2){
					if (!t){
						document.getElementById("status").innerHTML = "<span class=\"badge bg-important\">' . Text::_('COM_SERMONSPEAKER_WRITING_FILE') . '</span>";
						t = 1;
					}
					var progressbar = document.getElementById("progressbar");
					progressbar.style.width = data.msg + "%";
					progressbar.innerHTML = data.msg + "%";
					if (data.msg == 100){
						document.getElementById("status").innerHTML = "<span class=\"badge bg-success\">' . Text::_('COM_SERMONSPEAKER_DONE') . '</span>";
						document.getElementById("link").style.display = "block";
						progressbar.classList.add("bg-success");
						progressbar.classList.remove("progress-bar-animated");
					}
					if (data.msg < 100){
						timeout = setTimeout(CheckProgress,100);
					}
				} else {
					alert(data.msg);
					parent.document.getElementById("sbox-btn-close").click();
				}
			}
		}
		xmlhttp.open("GET","' . $base . 'index.php?option=com_sermonspeaker&task=serie.checkprogress&format=json&tmpl=component&id=' . $this->item->id . '",true);
		xmlhttp.send();
	}
	function CallZip() {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				var data = JSON.parse(xmlhttp.responseText);
				if (data.status==1){
					document.getElementById("link").innerHTML = "<a class=\"btn btn-primary\" href=\""+data.msg+"\">' . Text::_('COM_SERMONSPEAKER_DOWNLOADSERIES_LABEL') . '</a>";
				} else {
					alert(data.msg);
					parent.document.getElementById("sbox-btn-close").click();
				}
			}
		}
		xmlhttp.open("GET","' . $base . 'index.php?option=com_sermonspeaker&task=serie.download&format=json&tmpl=component&id=' . $this->item->id . '",true);
		xmlhttp.send();
		timeout = setTimeout(CheckProgress,100);
	}
	window.onload = CallZip;
';
$this->getDocument()->addScriptDeclaration($js);
?>
<div class="ss-seriesdownload-container">
	<h3><?php echo $this->item->title; ?></h3>
	<div id="status"><span class="badge bg-info"><?php echo Text::_('COM_SERMONSPEAKER_PREPARING_DOWNLOAD'); ?></span>
	</div>
	<br/>
	<div class="progress">
		<div id="progressbar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
			 style="width: 0">0%
		</div>
	</div>
	<br/><br/>
	<div id="link" style="display:none;"></div>
</div>
