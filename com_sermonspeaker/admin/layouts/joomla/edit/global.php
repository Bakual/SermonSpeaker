<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2018 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

defined('_JEXEC') or die;

/** @var JViewLegacy $displayData */
$form = $displayData->getForm();

$fields = $displayData->get('fields') ?: array(
	array('category', 'catid'),
	array('parent', 'parent_id'),
	'tags',
	array('published', 'state', 'enabled'),
	'featured',
	'podcast',
	'sticky',
	'access',
	'language',
	'note',
	'version_note',
);

$hiddenFields = $displayData->get('hidden_fields') ?: array();
/* if (!JLanguageMultilang::isEnabled())
{
	$hiddenFields[] = 'language';
} */
if (!JComponentHelper::getParams('com_sermonspeaker')->get('save_history', 0))
{
	$hiddenFields[] = 'version_note';
}

$html   = array();
$html[] = '<fieldset class="form-vertical">';

foreach ($fields as $field)
{
	$field = is_array($field) ? $field : array($field);
	foreach ($field as $f)
	{
		if ($form->getField($f))
		{
			if (in_array($f, $hiddenFields))
			{
				$form->setFieldAttribute($f, 'type', 'hidden');
			}

			$html[] = $form->getControlGroup($f);
			break;
		}
	}
}

$html[] = '</fieldset>';

echo implode('', $html);
