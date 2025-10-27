<?php

/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\Extension;

use Joomla\CMS\Association\AssociationServiceInterface;
use Joomla\CMS\Association\AssociationServiceTrait;
use Joomla\CMS\Categories\CategoryServiceInterface;
use Joomla\CMS\Categories\CategoryServiceTrait;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\Factory;
use Joomla\CMS\Fields\FieldsFormServiceInterface;
use Joomla\CMS\Fields\FieldsServiceTrait;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper as LibraryContentHelper;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Tag\TagServiceInterface;
use Joomla\CMS\Tag\TagServiceTrait;
use Sermonspeaker\Component\Sermonspeaker\Administrator\Helper\SermonspeakerHelper;
use Sermonspeaker\Component\Sermonspeaker\Administrator\Service\HTML\AdministratorService;
use Sermonspeaker\Component\Sermonspeaker\Administrator\Service\HTML\Icon;
use Psr\Container\ContainerInterface;

\defined('_JEXEC') or die;

/**
 * Component class for com_sermonspeaker
 *
 * @since  7.0.0
 */
class SermonspeakerComponent extends MVCComponent implements
	BootableExtensionInterface,
	CategoryServiceInterface,
	FieldsFormServiceInterface,
	AssociationServiceInterface,
	RouterServiceInterface,
	TagServiceInterface
{
	use AssociationServiceTrait;
	use RouterServiceTrait;
	use HTMLRegistryAwareTrait;
	use CategoryServiceTrait, TagServiceTrait, FieldsServiceTrait
	{
		CategoryServiceTrait::getTableNameForSection insteadof TagServiceTrait;
		CategoryServiceTrait::getStateColumnForSection insteadof TagServiceTrait;
		CategoryServiceTrait::prepareForm insteadof FieldsServiceTrait;
	}

	/** @var array Supported functionality */
	protected $supportedFunctionality = [
		'core.state' => true,
	];

	/**
	 * The trashed condition
	 *
	 * @since   7.0.0
	 */
	public const CONDITION_NAMES = [
		self::CONDITION_PUBLISHED   => 'JPUBLISHED',
		self::CONDITION_UNPUBLISHED => 'JUNPUBLISHED',
		self::CONDITION_ARCHIVED    => 'JARCHIVED',
		self::CONDITION_TRASHED     => 'JTRASHED',
	];

	/**
	 * The archived condition
	 *
	 * @since   7.0.0
	 */
	public const CONDITION_ARCHIVED = 2;

	/**
	 * The published condition
	 *
	 * @since   7.0.0
	 */
	public const CONDITION_PUBLISHED = 1;

	/**
	 * The unpublished condition
	 *
	 * @since   7.0.0
	 */
	public const CONDITION_UNPUBLISHED = 0;

	/**
	 * The trashed condition
	 *
	 * @since   7.0.0
	 */
	public const CONDITION_TRASHED = -2;

	/**
	 * Booting the extension. This is the function to set up the environment of the extension like
	 * registering new class loaders, etc.
	 *
	 * If required, some initial set up can be done from services of the container, eg.
	 * registering HTML services.
	 *
	 * @param   ContainerInterface  $container  The container
	 *
	 * @return  void
	 *
	 * @since   7.0.0
	 */
	public function boot(ContainerInterface $container)
	{
		$this->getRegistry()->register('sermonspeakeradministrator', new AdministratorService());
		$this->getRegistry()->register('sermonspeakericon', new Icon());

		// The layout joomla.content.icons does need a general icon service
		$this->getRegistry()->register('icon', $this->getRegistry()->getService('sermonspeakericon'));
	}

	/**
	 * Returns a valid section for the given section. If it is not valid then null
	 * is returned.
	 *
	 * @param   string  $section  The section to get the mapping for
	 * @param   object  $item     The item
	 *
	 * @return  string|null  The new section
	 *
	 * @since   7.0.0
	 */
	public function validateSection($section, $item = null)
	{
//        if (Factory::getApplication()->isClient('site')) {
		// On the front end we need to map some sections
//            switch ($section) {
		// Editing an article
//                case 'form':
		// Category list view
//                case 'featured':
//                case 'category':
//                    $section = 'article';
//            }
//        }

//        if ($section != 'article') {
		// We don't know other sections
//            return null;
//        }

		return $section;
	}

	/**
	 * Returns valid contexts
	 *
	 * @return  array
	 *
	 * @since   7.0.0
	 */
	public function getContexts(): array
	{
		Factory::getLanguage()->load('com_sermonspeaker', JPATH_ADMINISTRATOR);

		$contexts = [
			'com_sermonspeaker.sermon'     => Text::_('COM_SERMONSPEAKER'),
			'com_sermonspeaker.categories' => Text::_('JCATEGORY'),
		];

		return $contexts;
	}

	/**
	 * Returns the table for the count items functions for the given section.
	 *
	 * @param   ?string  $section  The section
	 *
	 * @return  string|null
	 *
	 * @since   7.0.0
	 */
	protected function getTableNameForSection(?string $section = null)
	{
		return '#__sermon_sermons';
	}

	/**
	 * Returns a table name for the state association
	 *
	 * @param   ?string  $section  An optional section to separate different areas in the component
	 *
	 * @return  string
	 *
	 * @since   7.0.0
	 */
	public function getWorkflowTableBySection(?string $section = null): string
	{
		return '#__sermon_sermons';
	}

	/**
	 * Returns the model name, based on the context
	 *
	 * @param   string  $context  The context of the workflow
	 *
	 * @return string
	 *
	 * @since   7.0.0
	 */
	public function getModelName($context): string
	{
		$parts = explode('.', $context);

		if (\count($parts) < 2)
		{
			return '';
		}

		array_shift($parts);

		$modelname = array_shift($parts);

		return ucfirst($modelname);
	}

	/**
	 * Adds Count Items for Category Manager.
	 *
	 * @param   \stdClass[]  $items    The category objects
	 * @param   string       $section  The section
	 *
	 * @return  void
	 *
	 * @since   7.0.0
	 */
	public function countItems(array $items, string $section)
	{
		$config = (object) [
			'related_tbl'         => 'sermon_' . $section,
			'state_col'           => 'state',
			'group_col'           => 'catid',
			'relation_type'       => 'category_or_group',
			'uses_workflows'      => true,
			'workflows_component' => 'com_sermonspeaker',
		];

		LibraryContentHelper::countRelations($items, $config);
	}

	/**
	 * Adds Count Items for Tag Manager.
	 *
	 * @param   \stdClass[]  $items      The content objects
	 * @param   string       $extension  The name of the active view.
	 *
	 * @return  void
	 *
	 * @throws  \Exception
	 * @since   7.0.0
	 */
	public function countTagItems(array $items, string $extension)
	{
		$parts   = explode('.', $extension);
		$section = \count($parts) > 1 ? $parts[1] : null;

		$config = (object) [
			'related_tbl'   => ($section === 'category' ? 'categories' : 'sermons'),
			'state_col'     => ($section === 'category' ? 'published' : 'state'),
			'group_col'     => 'tag_id',
			'extension'     => $extension,
			'relation_type' => 'tag_assigments',
		];

		LibraryContentHelper::countRelations($items, $config);
	}

	/**
	 * Prepares the category form
	 *
	 * @param   Form          $form  The form to prepare
	 * @param   array|object  $data  The form data
	 *
	 * @return void
	 *
	 * @since   7.0.0
	 */
	public function prepareForm(Form $form, $data)
	{
//      Probably Workflow Stuff
//		SermonspeakerHelper::onPrepareForm($form, $data);
	}
}
