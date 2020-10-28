<?php
namespace Sga\DeveloperToolbar\Plugin\Event;

use Magento\Framework\Event\ManagerInterface as Subject;
use Sga\DeveloperToolbar\Helper\Register;
use Sga\DeveloperToolbar\Helper\Data as HelperData;

class Manager
{
    protected $_helperRegister;
    protected $_helperData;

    public function __construct(
        Register $helperRegister,
        HelperData $helperData
    ) {
        $this->_helperRegister = $helperRegister;
        $this->_helperData = $helperData;
    }

    public function beforeDispatch(Subject $subject, $eventName, $data=[])
    {
        $this->_helperRegister->addEvent($eventName, $data, true);
    }

    public function afterDispatch(Subject $subject, $result, $eventName, $data=[])
    {
        $this->_helperRegister->addEvent($eventName, $data, false);
    }
}
