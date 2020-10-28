<?php
namespace Sga\DeveloperToolbar\Block\Toolbar;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Interception\DefinitionInterface;
use Sga\DeveloperToolbar\Block\Toolbar\AbstractBlock;

class Profiler extends AbstractBlock
{
    public function getCode()
    {
        return 'profiler';
    }

    public function getLabel()
    {
        return 'Profiler';
    }

    public function getHtmlTreeProfiler($tree = [], $level = 0)
    {
        if (empty($tree)) {
            $tree = $this->_getTreeProfiler();
        }

        $html = '<ul class="'.($level === 0 ? 'tree-root' : '').'">';
        foreach ($tree as $k => $treeNode) {
            $hasChildren = isset($treeNode['children']) && count($treeNode['children']) > 0;

            $html .= '<li class="'.($hasChildren ? 'has-children' : '').'">';
            if ($hasChildren) {
                $html .= '<i class="icon">+</i>';
            }
            $timeRender = $this->_getTimeRender($treeNode['data']);
            $disabled = '';
            if (preg_match('#^OBSERVER:#', $k)) {
                $observer = str_replace('OBSERVER:','', $k);
                $disabled = $this->_isObserverDisabled($observer) ? 'disabled' : '';
            }
            $html .= '<span class="name '.$this->getColorCss($timeRender).' '.$disabled.'">'.$k.' ('.$timeRender.' sec)</span>';

            $infos = [];
            if (isset($treeNode['data']['count'])) {
                $infos[] = 'Count: ' . $treeNode['data']['count'];
            }
            if (isset($treeNode['data']['realmem'])) {
                $infos[] = 'Memory Real Usage: ' . $this->formatBytes($treeNode['data']['realmem']);
            }
            if (isset($treeNode['data']['emalloc'])) {
                $infos[] = 'Memory Allocated: ' . $this->formatBytes($treeNode['data']['emalloc']);
            }
            if (count($infos) > 0) {
                $html .= '<div class="detail" style="display:none">' . implode('<br/>', $infos) . '</div>';
            }

            if ($hasChildren) {
                $html .= $this->getHtmlTreeProfiler($treeNode['children'], $level + 1);
            }
            $html .= '</li>';
        }
        $html .= '</ul>';

        return $html;
    }

    protected function _getTreeProfiler()
    {
        $tree = [];
        if (isset($this->_profiler)) {
            $timers = $this->_profiler->getTimers();
            foreach ($timers as $k => $line) {
                $parts = explode('->', $k);

                $cursor =& $tree;
                foreach ($parts as $i => $part) {
                    if (!isset($cursor[$part]) || !is_array($cursor[$part])) {
                        $cursor[$part]['children'] = [];
                    }
                    if (($i + 1) === count($parts)) {
                        $cursor[$part]['data'] = $line;
                    }

                    $cursor =& $cursor[$part]['children'];
                }
            }
        }

        return $tree;
    }

    protected function _getTimeRender($node)
    {
        if ($node['start'] !== false) {
            $time = $this->_helperRegister->getTimeEndProfiler() - $node['start'];
        } else {
            $time = $node['sum'];
        }

        return $this->roundNumber($time);
    }

    protected function _isObserverDisabled($name)
    {
        $observers = $this->_helperRegister->getObservers();
        foreach ($observers as $observer) {
            if ($observer['name'] === $name) {
                return isset($observer['disabled']) ? (bool)$observer['disabled'] : false;
            }
        }
        return false;
    }
}
