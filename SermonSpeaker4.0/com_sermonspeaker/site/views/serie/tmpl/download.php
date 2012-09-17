<?php
defined('_JEXEC') or die;

JHTML::_('behavior.mootools');
$js	= 'function CheckProgress() {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				var data = JSON.decode(xmlhttp.responseText);
				console.log(xmlhttp.responseText);
				console.log(xmlhttp.responseXML);
				if (data.status==1){
					if (data.msg == 100){
						document.getElementById("status").innerHTML = "'.JText::_('COM_SERMONSPEAKER_DONE').'";
						document.getElementById("link").style.display = "block";
					}
					setCount(data.msg);
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
<script language="javascript" src="media/com_sermonspeaker/percent_bar/percent_bar.js">/*
Event-based progress bar- By Brian Gosselin at http://scriptasylum.com/bgaudiodr
Featured on DynamicDrive.com
For full source, visit http://www.dynamicdrive.com
*/</script>
<br/>
<div id="link" style="display:none;"></div>
</div>