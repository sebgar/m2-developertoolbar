<?php
namespace Sga\DeveloperToolbar\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\View\LayoutInterface;

class Register extends AbstractHelper
{
    protected $_layout;
    protected $_events = [];
    protected $_observers = [];
    protected $_collections = [];
    protected $_models = [];
    protected $_blocks = [];
    protected $_blocksRender = [];

    protected $_timeEndProfiler;

    public function __construct(
        Context $context,
        LayoutInterface $layout
    ) {
        $this->_layout = $layout;

        parent::__construct($context);
    }

    public function getTimeEndProfiler()
    {
        return $this->_timeEndProfiler;
    }

    public function stopProfilerTime()
    {
        $this->_timeEndProfiler = microtime(true);
    }

    public function getLayout()
    {
        return $this->_layout;
    }

    public function addObserver($observerConfig, $wrapper, $flagStart = true)
    {
        $data = $observerConfig;
        $data['event'] = $wrapper->getEvent()->getName();
        $key = md5(serialize($data));

        if ($flagStart) {
            if (!isset($this->_observers[$key])) {
                $data['call_number'] = 0;
                $this->_observers[$key] = $data;
            }

            $this->_observers[$key]['call_number']++;
            $this->_observers[$key]['disabled'] = isset($data['disabled']) ? $data['disabled'] : false;
            $this->_observers[$key]['time'] = 0;
            $this->_observers[$key]['start'] = microtime(true);
        } else {
            if (isset($this->_observers[$key]['start'])) {
                $this->_observers[$key]['time'] += microtime(true) - $this->_observers[$key]['start'];
                unset($this->_observers[$key]['start']);
            }
        }
    }

    public function getObservers()
    {
        return $this->_observers;
    }

    public function addEvent($eventName, $data, $flagStart = true)
    {
        if ($flagStart) {
            if (!isset($this->_events[$eventName])) {
                $this->_events[$eventName] = [
                    'event' => $eventName,
                    'nbr' => 0,
                    'time' => 0,
                    'args' => array_keys($data)
                ];
            }
            $this->_events[$eventName]['nbr']++;
            $this->_events[$eventName]['start'] = microtime(true);

            switch ($eventName) {
                case 'core_collection_abstract_load_before':
                    $this->addCollection($data['collection'], 'start');
                    break;

                case 'core_collection_abstract_load_after':
                    $this->addCollection($data['collection'], 'end');
                    break;

                case 'model_load_before':
                    $this->addModel($data['object']);
                    break;

                case 'core_layout_block_create_after':
                    $this->addBlock($data['block']);
                    break;

                case 'view_block_abstract_to_html_before':
                    $this->addBlockInfoRender($data['block'], 'start');
                    break;

                case 'view_block_abstract_to_html_after':
                    $this->addBlockInfoRender($data['block'], 'end');
                    break;

                default:
                    break;

            }
        } else {
            if (isset($this->_events[$eventName]['start'])) {
                $this->_events[$eventName]['time'] += microtime(true) - $this->_events[$eventName]['start'];
                unset($this->_events[$eventName]['start']);
            }
        }
    }

    public function getEvents()
    {
        return $this->_events;
    }

    public function addCollection($collection, $key)
    {
        $class = get_class($collection);
        if ($key === 'start') {
            if (empty($this->_collections[$class])) {
                $this->_collections[$class] = [
                    'class' => $class,
                    'nbr' => 0,
                    'queries' => []
                ];
            }
            $this->_collections[$class]['nbr']++;
        } elseif ($key === 'end') {
            $this->_collections[$class]['queries'][] = $collection->getSelect()->assemble();
        }
    }

    public function getCollections()
    {
        return $this->_collections;
    }

    public function addModel($model)
    {
        $class = get_class($model);
        if (empty($this->_models[$class])) {
            $this->_models[$class] = ['class'=>$class, 'nbr'=>0];
        }
        $this->_models[$class]['nbr']++;
    }

    public function getModels()
    {
        return $this->_models;
    }

    public function addBlock($block)
    {
        $class = get_class($block);
        if (empty($this->_blocks[$class])) {
            $this->_blocks[$class] = ['class'=>$class, 'nbr'=>0];
        }
        $this->_blocks[$class]['nbr']++;
    }

    public function getBlocks()
    {
        return $this->_blocks;
    }

    public function addBlockInfoRender($block, $key)
    {
        $k = $block->getNameInLayout();
        if (!isset($this->_blocksRender[$k])) {
            $this->_blocksRender[$k] = [];
        }

        $this->_blocksRender[$k][$key] = microtime(true);
    }

    public function getBlocksRender()
    {
        return $this->_blocksRender;
    }
}
