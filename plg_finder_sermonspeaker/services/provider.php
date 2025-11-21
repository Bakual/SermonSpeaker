<?php
/**
 * @package     SermonSpeaker
 * @subpackage  Plugin.Finder
 * @author      Thomas Hunziker <admin@sermonspeaker.net>
 * @copyright   © 2025 - Thomas Hunziker
 * @license     http://www.gnu.org/licenses/gpl.html
 **/

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Sermonspeaker\Plugin\Finder\Sermonspeaker\Extension\Sermonspeaker;

defined('_JEXEC') or die;

return new class () implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   7.0.0
     */
    public function register(Container $container): void
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $plugin     = new Sermonspeaker(
                    (array) PluginHelper::getPlugin('finder', 'sermonspeaker')
                );
                $plugin->setApplication(Factory::getApplication());
	            $plugin->setDatabase($container->get(DatabaseInterface::class));

                return $plugin;
            }
        );
    }
};
