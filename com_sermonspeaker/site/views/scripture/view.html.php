<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;

/**
 * HTML View class for the SermonSpeaker Component
 *
 * @since  5
 */
class SermonspeakerViewScripture extends HtmlView
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @since ?
	 */
	public function display($tpl = null)
	{
		$id        = Factory::getApplication()->input->get('id', 0, 'int');
		$separator = Text::_('COM_SERMONSPEAKER_SCRIPTURE_SEPARATOR');
		$wa        = Factory::getApplication()->getDocument()->getWebAssetManager();

		// Get Params
		$this->params = ComponentHelper::getParams('com_sermonspeaker');

		// Add Javascript for Scripture
		$javascript = "function AddScripture() {
			var error = 0;
			var book = document.getElementById('book');
			var cap1 = parseInt(document.getElementById('cap1').value);
			var cap2 = parseInt(document.getElementById('cap2').value);
			var vers1 = parseInt(document.getElementById('vers1').value);
			var vers2 = parseInt(document.getElementById('vers2').value);
			var freetext = document.getElementById('text').value;
			if (!freetext){
				if (cap1 == cap2 || !cap2){
					if (vers1 == vers2){
						document.getElementById('vers2').value = '';
						vers2 = 0;
					} else if (vers2 && vers1 > vers2){
						document.getElementById('vers2').className += ' invalid';
						var error = 1;
					}
					document.getElementById('cap2').value = '';
					cap2 = 0;
				} else if (cap2 && cap1 > cap2){
					document.getElementById('cap2').className += ' invalid';
					var error = 1;
				}
				if (book.value == 0){
					document.getElementById('book').className += ' invalid';
					if(document.getElementById('book_chzn')){
						document.getElementById('book_chzn').className += ' invalid';
					}
					var error = 1;
				}
				if (error){
					alert('" . Text::_('JGLOBAL_VALIDATION_FORM_FAILED') . "');
					return;
				}
			}
			var value = book.value + '|' + cap1 + '|' + vers1 + '|' + cap2 + '|' + vers2 + '|' + freetext;
			if (freetext){
				var text = freetext;
			} else {
				var text = book.options[book.selectedIndex].text;
				if (cap1){
					text += ' ' + cap1;
					if (vers1){
						text += '" . $separator . "' + vers1;
					}
					if (cap2 || vers2){
						text += '-';
						if (cap2){
							text += cap2;
							if (vers2){
								text += '" . $separator . "' + vers2;
							}
						} else {
							text += vers2;
						}
					}
				}
			}";

		if ($id)
		{
			$javascript .= "var id = " . $id . ";
				window.parent.document.getElementById('jform_scripture_'+id).value = value;
				window.parent.document.getElementById('jform_scripture_text_'+id).value = text;
				window.parent.SqueezeBox.close();
			}
			window.onload = function(){
				value = window.parent.document.getElementById('jform_scripture_" . $id . "').value;
				split = value.split('|');
				if(split[0] > 0){
					document.getElementById('book').value = split[0];
				}
				if(split[1] > 0){document.getElementById('cap1').value = split[1];}
				if(split[2] > 0){document.getElementById('vers1').value = split[2];}
				if(split[3] > 0){document.getElementById('cap2').value = split[3];}
				if(split[4] > 0){document.getElementById('vers2').value = split[4];}
				document.getElementById('text').value = split[5];
			}";
		}
		else
		{
			$javascript .= "var id = parseInt(window.parent.document.getElementById('scripture_id').value);
				window.parent.document.getElementById('scripture_span').innerHTML +=
				'<span id=\"scripture_span_' + id + '\">\
					<input type=\"hidden\" name=\"jform[scripture][' + id + ']\" id=\"jform_scripture_' + id + '\" value=\"' + value + '\" />\
					<div class=\"input-group\">\
						<span class=\"input-group-btn\">\
							<button class=\"btn btn-secondary\" onclick=\"delete_scripture(' + id + ');\"><span class=\"fas fa-trash\"></span></button>\
						</span>\
						<input readonly=\"readonly\" type=\"text\" class=\"readonly form-control scripture unsaved\" size=\"30\" name=\"jform[scripture_text][' + id + ']\"\
							id=\"jform_scripture_text_' + id + '\" value=\"' + text + '\" />\
					</div>\
				</span>';
				window.parent.document.getElementById('scripture_id').value = id+1;
			}";
		}

		$wa->addInlineScript($javascript);

		$wa->addInlineScript("document.addEventListener('DOMContentLoaded', function() {
				setTimeout(function() {document.getElementById('book').focus()}, 500);
			});
		");
		$wa->addInlineScript("document.addEventListener('DOMContentLoaded', function() {
				let bookDropdown = document.getElementById('book');
				bookDropdown.addEventListener('change', function() {
					document.getElementById('cap1').value = '';
					document.getElementById('vers1').value = '';
					document.getElementById('cap2').value = '';
					document.getElementById('vers2').value = '';
				})
			});
		");

		parent::display($tpl);
	}
}
