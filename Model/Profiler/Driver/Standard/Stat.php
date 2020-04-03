<?php
namespace Sga\DeveloperToolbar\Model\Profiler\Driver\Standard;

use Magento\Framework\Profiler\Driver\Standard\Stat as BaseStat;

class Stat extends BaseStat
{
    public function getTimers()
    {
        return $this->_timers;
    }

    public function getStartTime()
    {
        $timeStart = 0;
        foreach ($this->_timers as $timer) {
            if ($timer['start'] > 0) {
                if ($timeStart === 0 || $timeStart > $timer['start']) {
                    $timeStart = $timer['start'];
                }
            }
        }

        return $timeStart;
    }

    public function getGeneratedTime()
    {
        $timeStart = 0;
        $timeEnd = 0;
        foreach ($this->_timers as $timer) {
            if ($timer['start'] > 0) {
                if ($timeStart === 0 || $timeStart > $timer['start']) {
                    $timeStart = $timer['start'];
                }
                if ($timeEnd === 0 || $timeEnd < $timer['start']) {
                    $timeEnd = $timer['start'];
                }
            }
        }

        return $timeEnd - $timeStart;
    }
}
