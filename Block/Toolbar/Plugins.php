<?php
namespace Sga\DeveloperToolbar\Block\Toolbar;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Interception\DefinitionInterface;
use Sga\DeveloperToolbar\Block\Toolbar\AbstractBlock;

class Plugins extends AbstractBlock
{
    protected $_types;

    public function getCode()
    {
        return 'plugin';
    }

    public function getLabel()
    {
        return 'Plugins';
    }

    public function getPlugins()
    {
        if (is_null($this->_types)) {
            $this->_types = [];
            $pluginList = ObjectManager::getInstance()->get('Magento\Framework\Interception\PluginList\PluginList');

            $reflection = new \ReflectionClass($pluginList);

            $processed = $reflection->getProperty('_processed');
            $processed->setAccessible(true);
            $processed = $processed->getValue($pluginList);

            $inherited = $reflection->getProperty('_inherited');
            $inherited->setAccessible(true);
            $inherited = $inherited->getValue($pluginList);

            $types = [
                DefinitionInterface::LISTENER_BEFORE => 'before',
                DefinitionInterface::LISTENER_AROUND => 'around',
                DefinitionInterface::LISTENER_AFTER => 'after'
            ];

            foreach ($processed as $currentKey => $processDef) {
                if (preg_match('/^(.*)_(.*)___self$/', $currentKey, $matches) or preg_match('/^(.*?)_(.*?)_(.*)$/', $currentKey, $matches)) {
                    $type = $matches[1];
                    $method = $matches[2];
                    if (!empty($inherited[$type])) {
                        foreach ($processDef as $keyType => $pluginsNames) {
                            if (!is_array($pluginsNames)) {
                                $pluginsNames = [$pluginsNames];
                            }

                            foreach ($pluginsNames as $pluginName) {
                                if (!empty($inherited[$type][$pluginName])) {
                                    $sortOrder = $inherited[$type][$pluginName]['sortOrder'];
                                    $prefix = $types[$keyType];
                                    $this->_types[$type][$method][$prefix][$sortOrder][] = [
                                        'plugin' => $inherited[$type][$pluginName]['instance'],
                                        'plugin_name' => $pluginName,
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }

        ksort($this->_types);
        return $this->_types;
    }
}
