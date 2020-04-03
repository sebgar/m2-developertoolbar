<?php
namespace Sga\DeveloperToolbar\Plugin\Event;

use Sga\DeveloperToolbar\Helper\Register;

class Invoker
{
    protected $_helperRegister;

    public function __construct(
        Register $helperRegister
    ) {
        $this->_helperRegister = $helperRegister;
    }

    public function beforeDispatch($class, $observerConfig, $wrapper)
    {
        $this->_helperRegister->addObserver($observerConfig, $wrapper, true);
    }

    public function afterDispatch($class, $result, $observerConfig, $wrapper)
    {
        $this->_helperRegister->addObserver($observerConfig, $wrapper, false);
    }
}
