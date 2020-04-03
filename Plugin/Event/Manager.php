<?php
namespace Sga\DeveloperToolbar\Plugin\Event;

use Sga\DeveloperToolbar\Helper\Register;

class Manager
{
    protected $_helperRegister;

    public function __construct(
        Register $helperRegister
    ) {
        $this->_helperRegister = $helperRegister;
    }

    public function beforeDispatch($interceptor, $eventName, $data=[])
    {
        $this->_helperRegister->addEvent($eventName, $data, true);
    }

    public function afterDispatch($interceptor, $result, $eventName, $data=[])
    {
        $this->_helperRegister->addEvent($eventName, $data, false);
    }
}
