<?php
namespace Sga\DeveloperToolbar\Block\Toolbar;

use Sga\DeveloperToolbar\Block\Toolbar\AbstractBlock;

class Time extends AbstractBlock
{
    public function getCode()
    {
        return 'time';
    }

    public function getLabel()
    {
        return $this->roundNumber($this->_helperRegister->getTimeEndProfiler() - $this->_profiler->getStartTime()).' sec';
    }

    public function hasContent()
    {
        return false;
    }
}
