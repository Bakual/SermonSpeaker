import { JoomlaEditor, JoomlaEditorButton } from 'editor-api';

/**
 * @package     SermonSpeaker
 * @subpackage  Component.Media
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/
document.addEventListener('DOMContentLoaded', function () {
    var elements = document.getElementsByClassName('lookup-button');
    var lookup = function (elem) {
        var lookupElement = document.getElementById(elem.target.dataset.lookup);
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange=function(){
            if (xmlhttp.readyState==4 && xmlhttp.status==200){
                var data = jQuery.parseJSON(xmlhttp.responseText);
                if (data.status==1){
                    if(data.filename_title==false || document.getElementById("jform_title").value==""){
                        document.getElementById("jform_title").value = data.title;
                        document.getElementById("jform_alias").value = data.alias;
                    }
                    if(data.sermon_number && document.getElementById("jform_sermon_number")){
                        document.getElementById("jform_sermon_number").value = data.sermon_number;
                    }
                    if(data.sermon_date && document.getElementById("jform_sermon_date")){
                        document.getElementById("jform_sermon_date").value = data.sermon_date;
                    }
                    if(data.sermon_time && document.getElementById("jform_sermon_time")){
                        document.getElementById("jform_sermon_time").value = data.sermon_time;
                    }
                    if(data.series_id){
                        window.processModalSelect('Serie', 'jform_series_id', data.series_id, data.series_title);
                    }
                    if(data.speaker_id){
                        window.processModalSelect('Speaker', 'jform_speaker_id', data.speaker_id, data.speaker_title);
                    }
                    if(data.notes && document.getElementById("jform_notes")){
                        JoomlaEditor.get("jform_notes").replaceSelection(data.notes);
                    }
                    var splits = lookupElement.id.split("_");
                    var field = splits[0]+"_"+splits[1];
                    if(data.filesize){
                        if(document.getElementById(field+"size")){
                            document.getElementById(field+"size").value = data.filesize;
                        }
                    }
                    if(data.audio){
                        var info;
                        info = "<div class=\"clearfix\"><dl class=\"row id3-info\">";
                        jQuery.each(data.audio, function(key,val){
                            info += "<dt class=\"col-sm-3\">"+key+"</dt><dd class=\"col-sm-9\">"+val+"</dd>";
                        })
                        info += "</dl></div>";
                        jQuery(lookupElement).parents(".controls").children(".id3-info").remove();
                        jQuery(lookupElement).parents(".controls").prepend(info);
                    }
                    if(data.not_found){
                        var notice = new Array();
                        if (data.not_found.series){
                            notice.push(Joomla.Text._("COM_SERMONSPEAKER_SERIE") + ": " + data.not_found.series);
                        }
                        if (data.not_found.speakers){
                            notice.push(Joomla.Text._("COM_SERMONSPEAKER_SPEAKER") + ": " + data.not_found.speakers);
                        }
                        notice.push(Joomla.Text._("COM_SERMONSPEAKER_ID3_NO_MATCH_FOUND"));
                        var messages = {"notice":notice};
                        Joomla.renderMessages(messages);
                    }
                } else {
                    alert(data.msg);
                }
            }
        }
        xmlhttp.open("POST","index.php?option=com_sermonspeaker&task=file.lookup&format=json",true);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send("file="+lookupElement.value);
    };
    Array.from(elements).forEach(function(element) {
        element.addEventListener('click', lookup);
    });
});
