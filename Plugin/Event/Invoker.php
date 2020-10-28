<?php
namespace Sga\DeveloperToolbar\Plugin\Event;

use Magento\Framework\Event\InvokerInterface as Subject;
use Sga\DeveloperToolbar\Helper\Register;
use Sga\DeveloperToolbar\Helper\Data as HelperData;

class Invoker
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

    public function beforeDispatch(Subject $subject, $observerConfig, $wrapper)
    {
        $this->_helperRegister->addObserver($observerConfig, $wrapper, true);
    }

    public function afterDispatch(Subject $subject, $result, $observerConfig, $wrapper)
    {
        $this->_helperRegister->addObserver($observerConfig, $wrapper, false);
    }
}
