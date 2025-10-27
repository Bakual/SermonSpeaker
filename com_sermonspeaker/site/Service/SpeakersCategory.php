<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Component.Site
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2023 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

namespace Sermonspeaker\Component\Sermonspeaker\Site\Service;

use Joomla\CMS\Categories\Categories;

\defined('_JEXEC') or die;

/**
 * Content Component Category Tree
 *
 * @since  7.0
 */
class SpeakersCategory extends Categories
{
    /**
     * Class constructor
     *
     * @param   array  $options  Array of options
     *
     * @since   7.0
     */
    public function __construct($options = [])
    {
        $options['table']     = '#__sermon_speakers';
        $options['extension'] = 'com_sermonspeaker.speakers';

        parent::__construct($options);
    }
}
