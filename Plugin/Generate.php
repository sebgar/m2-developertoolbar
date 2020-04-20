<?php
namespace Sga\DeveloperToolbar\Plugin;

use Magento\Framework\App\Response\Http as Subject;
use Magento\Framework\ObjectManagerInterface;
use Sga\DeveloperToolbar\Helper\Register;
use Sga\DeveloperToolbar\Helper\Data as HelperData;

class Generate
{
    protected $_objectManager;
    protected $_helperRegister;
    protected $_helperData;

    public function __construct(
        ObjectManagerInterface $objectManager,
        Register $helperRegister,
        HelperData $helperData
    ){
        $this->_objectManager = $objectManager;
        $this->_helperRegister = $helperRegister;
        $this->_helperData = $helperData;
    }

    public function afterSendResponse(Subject $subject)
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
        if (!$this->_helperData->isEnable()) {
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
