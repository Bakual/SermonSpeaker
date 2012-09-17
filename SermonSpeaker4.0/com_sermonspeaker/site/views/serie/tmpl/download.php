<?php
defined('_JEXEC') or die;
JHtml::_('script', 'system/progressbar.js', true, true);

$js	= 'function CheckProgress() {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				var data = JSON.decode(xmlhttp.responseText);
				if (data.status==1){
					if (data.msg == 100){
						document.getElementById("status").innerHTML = "'.JText::_('COM_SERMONSPEAKER_DONE').'";
						document.getElementById("link").style.display = "block";
					}
					progress_bar.set(data.msg);
					if (data.msg < 100){
						setTimeout(CheckProgress,100);
					}
				} else {
					alert(data.msg);
					parent.document.getElementById("sbox-btn-close").click();
				}
			}
		}
		xmlhttp.open("GET","index.php?option=com_sermonspeaker&task=serie.checkprogress&format=json&id='.$this->item->id.'",true);
		xmlhttp.send();
	}
	function CallZip() {
		progress_bar = new Fx.ProgressBar(document.id("progress"));
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				var data = JSON.decode(xmlhttp.responseText);
				if (data.status==1){
					document.getElementById("link").innerHTML = "<a href=\""+data.msg+"\">'.JText::_('COM_SERMONSPEAKER_DOWNLOADSERIES_LABEL').'</a>";
				} else {
					alert(data.msg);
					parent.document.getElementById("sbox-btn-close").click();
				}
			}
		}
		xmlhttp.open("GET","index.php?option=com_sermonspeaker&task=serie.download&format=json&id='.$this->item->id.'",true);
		xmlhttp.send();
		setTimeout(CheckProgress,100);
	}
	window.onload = CallZip;
';
$this->document->addScriptDeclaration($js);
?>
<div class="ss-seriesdownload-container">
<h3><?php echo $this->item->series_title; ?></h3>
<div id="status"><?php echo JText::_('COM_SERMONSPEAKER_PREPARING_DOWNLOAD'); ?></div>
<br/>
<span id="progress"></span>
<br/><br/>
<div id="link" style="display:none;"></div>
</div>