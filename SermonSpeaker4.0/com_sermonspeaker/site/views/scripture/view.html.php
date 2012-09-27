<?php
defined('_JEXEC') or die;
class SermonspeakerViewScripture extends JViewLegacy
{
	function display( $tpl = null )
	{
		$id			= JRequest::getInt('id', 0);
		$separator	= JText::_('COM_SERMONSPEAKER_SCRIPTURE_SEPARATOR');
		// add Javascript for Scripture
		$javascript	= "function AddScripture() {
			var book = document.getElementById('book');
			var cap1 = document.getElementById('cap1').value;
			var cap2 = document.getElementById('cap2').value;
			var vers1 = document.getElementById('vers1').value;
			var vers2 = document.getElementById('vers2').value;
			var freetext = document.getElementById('text').value;
			if (cap1 == cap2){
				cap2 = 0;
			}
			var value = book.value + '|' + cap1 + '|' + vers1 + '|' + cap2 + '|' + vers2 + '|' + freetext;
			if (freetext){
				var text = freetext;
			} else {
				var text = book.options[book.selectedIndex].text;
				if (cap1){
					text += ' ' + cap1;
					if (vers1){
						text += '".$separator."' + vers1;
					}
					if (cap2 || vers2){
						text += '-';
						if (cap2){
							text += cap2;
							if (vers2){
								text += '".$separator."' + vers2;
							}
						} else {
							text += vers2;
						}
					}
				}
			}";
		if ($id){
			$javascript .= "var id = ".$id.";
				window.parent.document.getElementById('jform_scripture_'+id).value = value;
				window.parent.document.getElementById('jform_scripture_text_'+id).value = text;
				window.parent.SqueezeBox.close();
			}
			window.onload = function(){
				value = window.parent.document.getElementById('jform_scripture_".$id."').value;
				split = value.split('|');
				if(split[0] > 0){document.getElementById('book').value = split[0];}
				if(split[1] > 0){document.getElementById('cap1').value = split[1];}
				if(split[2] > 0){document.getElementById('vers1').value = split[2];}
				if(split[3] > 0){document.getElementById('cap2').value = split[3];}
				if(split[4] > 0){document.getElementById('vers2').value = split[4];}
				document.getElementById('text').value = split[5];
			}";
		} else {
			$javascript .= "var id = parseInt(window.parent.document.getElementById('scripture_id').value);
				window.parent.document.getElementById('scripture_span').innerHTML += '<span id=\"scripture_span_' + id + '\"><input type=\"hidden\" name=\"jform[scripture][' + id + ']\" id=\"jform_scripture_' + id + '\" value=\"' + value + '\" /><img src=\"".JURI::root()."media/com_sermonspeaker/images/delete.png\" class=\"pointer\" onClick=\"delete_scripture(' + id + ');\"><input readonly=\"readonly\" class=\"readonly scripture\" size=\"30\" name=\"jform[scripture_text][' + id + ']\" id=\"jform_scripture_text_' + id + '\" value=\"' + text + '\" /><label></label></span>';
				window.parent.document.getElementById('scripture_id').value = id+1;
				window.parent.SqueezeBox.close();
			}";
		}
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($javascript);
		parent::display($tpl);
	}
}