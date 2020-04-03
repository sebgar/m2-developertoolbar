<?php
namespace Sga\DeveloperToolbar\Plugin;

use Magento\Framework\App\Response\Http;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\State as AppState;
use Sga\DeveloperToolbar\Helper\Register;
use Sga\DeveloperToolbar\Helper\Config;

class Generate
{
    protected $_objectManager;
    protected $_helperRegister;
    protected $_helperConfig;
    protected $_appState;

    public function __construct(
        ObjectManagerInterface $objectManager,
        Register $helperRegister,
        Config $helperConfig,
        AppState $appState
    ){
        $this->_objectManager = $objectManager;
        $this->_helperRegister = $helperRegister;
        $this->_helperConfig = $helperConfig;
        $this->_appState = $appState;
    }

    public function afterSendResponse(Http $subject)
    {
        $this->_helperRegister->stopProfilerTime();

        if ($this->_canDisplay()) {
            $layout = $this->_objectManager->create('\Magento\Framework\View\Layout');

            $layout->getUpdate()->addHandle('developertoolbar')->load();
            $layout->generateXml();
            $layout->generateElements();

            echo $layout->getOutput();
        }
    }

    protected function _canDisplay()
    {
        // is not enable in bo
        if ($this->_appState->getAreaCode() === 'adminhtml' && !$this->_helperConfig->isEnabledBo()) {
            return false;
        }

        // is not enable in fo
        if ($this->_appState->getAreaCode() === 'frontend' && !$this->_helperConfig->isEnabledFo()) {
            return false;
        }

        // is ajax request
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            return false;
        }

        // is not index.php
        if ($_SERVER['SCRIPT_NAME'] !== '/index.php') {
            return false;
        }

        return true;
    }
}
