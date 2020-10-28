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
        if (isset($this->_profiler)) {
            return $this->roundNumber($this->_helperRegister->getTimeEndProfiler() - $this->_profiler->getStartTime()).' sec';
        } else {
            return '-';
        }
    }

    public function hasContent()
    {
        return false;
    }
}
