<?php

namespace Sga\DeveloperToolbar\Block\Toolbar;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template\Context;
use Sga\DeveloperToolbar\Block\Toolbar\AbstractBlock;
use Sga\DeveloperToolbar\Helper\Data as HelperData;
use Sga\DeveloperToolbar\Helper\Register;
use Sga\DeveloperToolbar\Model\Layout\Merge as LayoutMerge;
use Sga\DeveloperToolbar\Model\Session;

class Layout extends AbstractBlock
{
    protected $_layoutMerge;

    protected $_blocks;
    protected $_treeBlocks;
    protected $_blocksFoundInTree = [];
    protected $_handlesInformations;

    public function __construct(
        Context $context,
        Json $jsonSerializer,
        Session $session,
        Register $helperRegister,
        HelperData $helperData,
        ProductMetadataInterface $productMetaData,
        ResourceConnection $resource,
        DirectoryList $directory,
        LayoutMerge $layoutMerge,
        array $data = []
    ) {
        $this->_layoutMerge = $layoutMerge;

        parent::__construct($context, $jsonSerializer, $session, $helperRegister, $helperData, $productMetaData, $resource, $directory, $data);
    }

    public function getCode()
    {
        return 'layout';
    }

    public function getLabel()
    {
        return 'Layout';
    }

    protected function _preload()
    {
        $this->getBlocks();
        $this->_getTreeBlocks();
        parent::_preload();
    }

    public function getHandles()
    {
        $handles = $this->_helperRegister->getLayout()->getUpdate()->getHandles();

        $xml = $this->getUpdatesXml();
        foreach ($xml->xpath('//update/@handle') as $handle) {
            $handles[] = (string)$handle;
        }

        return $handles;
    }

    public function getHandleInformations($handle)
    {
        if (!isset($this->_handlesInformations)) {
            $this->_handlesInformations = $this->_layoutMerge->getHandlesInformations();
        }

        return isset($this->_handlesInformations[$handle]) ? $this->_handlesInformations[$handle] : [];
    }

    public function getHandleFileFrom($file)
    {
        $file = str_replace($this->_directory->getRoot(), '', $file);
        if (preg_match('#^/vendor/#', $file)) {
            return 'Vendor';
        } elseif (preg_match('#^/app/code/#', $file)) {
            return 'Module';
        } elseif (preg_match('#^/app/design/#', $file)) {
            return 'Theme';
        }
    }

    public function getUpdatesXml()
    {
        $xmlString = $this->_helperRegister->getLayout()->getUpdate()->asString();
        $useErrors = libxml_use_internal_errors(true);
        $xml = simplexml_load_string('<layouts xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' . $xmlString . '</layouts>', \Magento\Framework\View\Layout\Element::class);
        libxml_use_internal_errors($useErrors);
        return $xml;
    }

    public function getBlocks()
    {
        if (!isset($this->_blocks)) {
            $layout = $this->_helperRegister->getLayout();

            $reflection = new \ReflectionClass($layout);

            $structure = $reflection->getProperty('structure');
            $structure->setAccessible(true);
            $structure = $structure->getValue($layout);

            $this->_blocks = $structure->exportElements();
            $layout = $this->_helperRegister->getLayout();
            $blocksRender = $this->_helperRegister->getBlocksRender();
            foreach ($this->_blocks as $name => &$block) {
                $b = $layout->getBlock($name);
                if (false !== $b) {
                    if ((string)$b->getCacheLifetime() !== '') {
                        $block['lifetime'] = $b->getCacheLifetime();
                    }
                    $block['file'] = str_replace($this->_directory->getRoot(), '', $b->getTemplateFile());
                    $block['class_name'] = get_class($b);
                    if (!empty($block['class_name'])) {
                        $reflectionClass = new \ReflectionClass($b);
                        $block['class_file'] = str_replace($this->_directory->getRoot(), '', $reflectionClass->getFileName());
                    }

                    if ($b instanceof \Magento\Cms\Block\Block) {
                        $block['identifier'] = $b->getBlockId();
                    }

                    if (isset($blocksRender[$name])) {
                        if (isset($blocksRender[$name]['start']) && isset($blocksRender[$name]['end'])) {
                            $block['time'] = $this->roundNumber($blocksRender[$name]['end'] - $blocksRender[$name]['start']);
                        }
                    }
                }
            }
        }
        return $this->_blocks;
    }

    public function getBlocksByTime()
    {
        $list = [];
        $this->getBlocks();
        foreach ($this->_blocks as $name => $block) {
            if ($block['type'] === 'block' && isset($block['time'])) {
                $list[$name] = $block['time'];
            }
        }
        arsort($list);

        $sorted = [];
        foreach ($list as $name => $time) {
            $sorted[$name] = $this->_blocks[$name];
        }

        return $sorted;
    }

    public function getBlocksNotUsed()
    {
        $list = [];
        $this->getBlocks();
        foreach ($this->_blocks as $name => $block) {
            if ($block['type'] === 'block' && !isset($block['time']) && !in_array($name, $this->_blocksFoundInTree)) {
                $list[$name] = $block;
            }
        }
        return $list;
    }

    public function getHtmlTreeBlocks($tree = [], $level = 0)
    {
        if (empty($tree)) {
            $t = $this->_getTreeBlocks();
            $tree = count($t) > 0 ? [$t] : [];
        }

        $html = '<ul class="'.($level === 0 ? 'tree-root' : '').'">';
        foreach ($tree as $treeNode) {
            $hasChildren = isset($treeNode['children']) && count($treeNode['children']) > 0;

            $html .= '<li class="type-'.$treeNode['type'].' '.($hasChildren ? 'has-children' : '').'">';
            if ($hasChildren) {
                $html .= '<i class="icon">+</i>';
            }
            $timeRender = $this->_getTimeRender($treeNode);
            $alias = $treeNode['alias'] !== '' && $treeNode['alias'] !== $treeNode['name'] ? ' (alias : '.$treeNode['alias'].')' : '';
            $html .= '<span class="name '.$this->getColorCss($timeRender).'">' . $treeNode['name'] . $alias . ' (' . $timeRender . ' sec)</span>';

            $infos = [];
            if (isset($treeNode['class_name']) && $treeNode['class_name'] != '') {
                $infos[] = 'Class: ' . $treeNode['class_name'] . ' (' . $treeNode['class_file'] . ')';
            }
            if (isset($treeNode['file']) && $treeNode['file'] != '') {
                $infos[] = 'Template: ' . $treeNode['file'];
            }
            if (count($infos) > 0) {
                $html .= '<div class="detail" style="display:none">' . implode('<br/>', $infos) . '</div>';
            }

            if ($hasChildren) {
                $html .= $this->getHtmlTreeBlocks($treeNode['children'], $level + 1);
            }
            $html .= '</li>';
        }
        $html .= '</ul>';

        return $html;
    }

    protected function _getTreeBlocks()
    {
        if (!isset($this->_treeBlocks)) {
            $this->getBlocks();
            $this->_treeBlocks = $this->_buildTreeBlocks();
        }

        return $this->_treeBlocks;
    }

    protected function _buildTreeBlocks($name = 'root', $alias = '')
    {
        $block = $this->_getBlockByName($name);
        if ($block) {
            $this->_blocksFoundInTree[] = $name;

            $treeBlocks = [
                'name' => $name,
                'alias' => $alias,
                'type' => $block['type'],
                'label' => isset($block['label']) ? $block['label'] : '',
                'file' => '',
                'class_name' => '',
                'class_file' => '',
            ];

            if (isset($block['file'])) {
                $treeBlocks['file'] = $block['file'];
                $treeBlocks['class_name'] = $block['class_name'];
                $treeBlocks['class_file'] = $block['class_file'];
            }

            if (isset($block['children'])) {
                foreach ($block['children'] as $childName => $childAlias) {
                    $treeBlocks['children'][] = $this->_buildTreeBlocks($childName, $childAlias);
                }
            }
        } else {
            $treeBlocks = [];
        }

        return $treeBlocks;
    }

    protected function _getBlockByName($name)
    {
        return (!empty($this->_blocks[$name])) ? $this->_blocks[$name] : false;
    }

    protected function _getTimeRender($node)
    {
        $blocksRender = $this->_helperRegister->getBlocksRender();
        if (isset($blocksRender[$node['name']])) {
            if (isset($blocksRender[$node['name']]['start']) && isset($blocksRender[$node['name']]['end'])) {
                $timeRender = $this->roundNumber($blocksRender[$node['name']]['end'] - $blocksRender[$node['name']]['start']);
            } else {
                $timeRender = 0;
            }
        } else {
            $timeRender = $this->roundNumber($this->_getNodeRenderTotal($node, $blocksRender));
        }

        return $timeRender;
    }

    protected function _getNodeRenderTotal($node, $blocksRender)
    {
        $count = 0;
        if (isset($blocksRender[$node['name']]['start']) && isset($blocksRender[$node['name']]['end'])) {
            $count += $this->roundNumber($blocksRender[$node['name']]['end'] - $blocksRender[$node['name']]['start']);
        } elseif (!empty($node['children'])) {
            foreach ($node['children'] as $child) {
                $count += $this->_getNodeRenderTotal($child, $blocksRender);
            }
        }
        return $count;
    }
}
