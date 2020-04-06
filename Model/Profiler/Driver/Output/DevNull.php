<?php
namespace Sga\DeveloperToolbar\Model\Profiler\Driver\Output;

use Magento\Framework\Profiler\Driver\Standard\Stat;
use Magento\Framework\Profiler\Driver\Standard\OutputInterface;

class DevNull implements OutputInterface
{
    public function display(Stat $stat)
    {
        //echo '';
    }
}
