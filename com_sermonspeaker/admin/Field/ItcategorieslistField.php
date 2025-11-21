<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Administrator
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Administrator\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

/**
 * Itcategorieslist Field class for the SermonSpeaker.
 *
 * @package        SermonSpeaker
 * @since          4.0
 */
class ItcategorieslistField extends ListField
{
	/**
	 * The form field type.
	 *
	 * @var        string
	 * @since    1.6
	 */
	protected $type = 'Itcategorieslist';

	/**
	 * Method to get the field options.
	 *
	 * @return    array    The field option objects.
	 * @throws \Exception
	 * @since    1.6
	 */
	public function getOptions(): array
	{
		// Initialize variables.
		$options             = array();
		$options[0]['value'] = 0;
		$options[0]['text']  = Text::_('JNONE');

		// Categories which no longer exists.
		$missing = array(
			'Arts > Literature',
			'Business > Business News',
			'Business > Management & Marketing',
			'Business > Shopping',
			'Education > Education',
			'Education > Education Technology',
			'Education > Higher Education',
			'Education > K-12',
			'Education > Language Courses',
			'Education > Training',
			'Games & Hobbies > Automotive',
			'Games & Hobbies > Aviation',
			'Games & Hobbies > Hobbies',
			'Games & Hobbies > Other Games',
			'Games & Hobbies > Video Games',
			'Government & Organizations > Local',
			'Government & Organizations > National',
			'Government & Organizations > Non-Profit',
			'Government & Organizations > Regional',
			'Health > Alternative Health',
			'Health > Fitness & Nutrition',
			'Health > Self-Help',
			'Health > Sexuality',
			'News & Politics',
			'Religion & Spirituality > Other',
			'Science & Medicine > Medicine',
			'Science & Medicine > Natural Sciences',
			'Science & Medicine > Social Sciences',
			'Society & Culture > History',
			'Sports & Recreation > Amateur',
			'Sports & Recreation > College & High School',
			'Sports & Recreation > Outdoor',
			'Sports & Recreation > Professional',
			'Technology > Gadgets',
			'Technology > Tech News',
			'Technology > Podcasting',
			'Technology > Software How-To',
		);

		if (in_array($this->value, $missing))
		{
			$fieldlabel = Text::_($this->element['label']);
			$message    = Text::sprintf('COM_SERMONSPEAKER_ITCAT_MISSING', $this->value, $fieldlabel);
			Factory::getApplication()->enqueueMessage($message, 'warning');
		}

		// List of iTunes Categories (from https://help.apple.com/itc/podcasts_connect/#/itc9267a2f12)
		$cats['Arts']                    = array('Books', 'Design', 'Fashion & Beauty', 'Food', 'Performing Arts', 'Visual Arts');
		$cats['Business']                = array('Careers', 'Entrepreneurship', 'Investing', 'Management', 'Marketing', 'Non-Profit');
		$cats['Comedy']                  = array('Comedy Interviews', 'Improv', 'Stand-Up');
		$cats['Education']               = array('Courses', 'How To', 'Language Learning', 'Self-Improvement');
		$cats['Fiction']                 = array('Comedy Fiction', 'Drama', 'Science Fiction');
		$cats['Government']              = array();
		$cats['History']                 = array();
		$cats['Health & Fitness']        = array('Alternative Health', 'Fitness', 'Medicine', 'Mental Health', 'Nutrition', 'Sexuality');
		$cats['Kids & Family']           = array('Education for Kids', 'Parenting', 'Pets & Animals', 'Stories for Kids');
		$cats['Leisure']                 = array('Animation & Manga', 'Automotive', 'Aviation', 'Crafts', 'Games', 'Hobbies', 'Home & Garden', 'Video Games');
		$cats['Music']                   = array('Music Commentary', 'Music History', 'Music Interviews');
		$cats['News']                    = array('Business News', 'Daily News', 'Entertainment News', 'News Commentary', 'Politics', 'Sports News', 'Tech News');
		$cats['Religion & Spirituality'] = array('Buddhism', 'Christianity', 'Hinduism', 'Islam', 'Judaism', 'Religion', 'Spirituality');
		$cats['Science']                 = array('Astronomy', 'Chemistry', 'Earth Sciences', 'Life Sciences', 'Mathematics', 'Natural Sciences', 'Nature', 'Physics', 'Social Sciences');
		$cats['Society & Culture']       = array('Documentary', 'Personal Journals', 'Philosophy', 'Places & Travel', 'Relationships');
		$cats['Sports']                  = array('Baseball', 'Basketball', 'Cricket', 'Fantasy Sports', 'Football', 'Golf', 'Hockey', 'Rugby', 'Running', 'Soccer', 'Swimming', 'Tennis', 'Volleyball', 'Wilderness', 'Wrestling');
		$cats['Technology']              = array();
		$cats['True Crime']              = array();
		$cats['TV & Film']               = array('After Shows', 'Film History', 'Film Interviews', 'Film Reviews', 'TV Reviews');

		$i = 0;
		foreach ($cats as $key => $value)
		{
			$i++;
			$options[$i]['value'] = $key;
			$text                 = Text::_('COM_SERMONSPEAKER_ITCAT_' . strtoupper(str_replace(array(' ', '&'), '-', $key)));
			$options[$i]['text']  = $text;
			if ($value)
			{
				foreach ($value as $subvalue)
				{
					$i++;
					$options[$i]['value'] = $key . ' > ' . $subvalue;
					$subtext              = Text::_('COM_SERMONSPEAKER_ITCAT_' . strtoupper(str_replace(array(' ', '&'), '-', $key . '--' . $subvalue)));
					$options[$i]['text']  = $text . ' > ' . $subtext;
				}
			}
		}

		return array_merge(parent::getOptions(), $options);
	}
}
