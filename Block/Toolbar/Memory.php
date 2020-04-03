<?php
namespace Sga\DeveloperToolbar\Block\Toolbar;

use Sga\DeveloperToolbar\Block\Toolbar\AbstractBlock;

class Memory extends AbstractBlock
{
    public function getCode()
    {
        return 'memory';
    }

    public function getLabel()
    {
        $infos[] = '<span title="real usage">'.$this->formatBytes(memory_get_usage(true)).'</span>';
        $infos[] = '<span title="allocated">'.$this->formatBytes(memory_get_usage()).'</span>';
        return implode('/', $infos);
    }

    public function hasContent()
    {
        return false;
    }
}
