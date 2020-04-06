<?php
namespace Sga\DeveloperToolbar\Plugin\Event;

use Magento\Framework\App\State as AppState;
use Sga\DeveloperToolbar\Helper\Register;
use Sga\DeveloperToolbar\Helper\Config;

class Manager
{
    protected $_helperRegister;
    protected $_helperConfig;
    protected $_appState;

    public function __construct(
        Register $helperRegister,
        Config $helperConfig,
        AppState $appState
    ) {
        $this->_helperRegister = $helperRegister;
        $this->_helperConfig = $helperConfig;
        $this->_appState = $appState;
    }

    protected function _canCatch()
    {
        // is not enable in bo
        if ($this->_appState->getAreaCode() === 'adminhtml' && !$this->_helperConfig->isEnabledBo()) {
            return false;
        }

        // is not enable in fo
        if ($this->_appState->getAreaCode() === 'frontend' && !$this->_helperConfig->isEnabledFo()) {
            return false;
        }

        return true;
    }

    public function beforeDispatch($interceptor, $eventName, $data=[])
    {
        if ($this->_canCatch()) {
            $this->_helperRegister->addEvent($eventName, $data, true);
        }
    }

    public function afterDispatch($interceptor, $result, $eventName, $data=[])
    {
        if ($this->_canCatch()) {
            $this->_helperRegister->addEvent($eventName, $data, false);
        }
    }
}
