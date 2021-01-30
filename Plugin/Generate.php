<?php
namespace Sga\DeveloperToolbar\Plugin;

use Magento\Framework\App\Response\Http as Subject;
use Magento\Framework\ObjectManagerInterface;
use Sga\DeveloperToolbar\Helper\Register;
use Sga\DeveloperToolbar\Helper\Data as HelperData;
use Sga\DeveloperToolbar\Model\Session;

class Generate
{
    protected $_objectManager;
    protected $_helperRegister;
    protected $_helperData;
    protected $_session;

    protected $_response;

    public function __construct(
        ObjectManagerInterface $objectManager,
        Register $helperRegister,
        HelperData $helperData,
        Session $session
    ){
        $this->_objectManager = $objectManager;
        $this->_helperRegister = $helperRegister;
        $this->_helperData = $helperData;
        $this->_session = $session;
    }

    public function afterSendResponse(Subject $subject)
    {
        $this->_response = $subject;

        $this->_helperRegister->stopProfilerTime();

        if ($this->_canRender()) {
            $layout = $this->_objectManager->create('\Magento\Framework\View\Layout');

            $layout->getUpdate()->addHandle('developertoolbar')->load();
            $layout->generateXml();
            $layout->generateElements();

            // generate new key before generation
            $this->_session->generateRequestKey();

            if ($this->_canDisplay()) {
                // flag session to add the other request
                $this->_session->setCanAddOtherRequest(true);

                // generate all toolbars
                echo $layout->getOutput();
            } else {
                // flag session to add the other request
                $this->_session->setCanAddOtherRequest(false);

                // render html of toolbar only
                ob_start();
                echo $layout->getBlock('developertoolbar.toolbar')->toHtml();
                $html = ob_get_contents();
                ob_end_clean();

                // save html in session
                $this->_session->setHtmlRequests($html);
            }
        }
    }

    protected function _canDisplay()
    {
        // is ajax request
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            return false;
        }

        // is not index.php
        if (strpos($_SERVER['SCRIPT_NAME'], '/index.php') === false) {
            return false;
        }

        // is redirect
        if ($this->_response->isRedirect()) {
            return false;
        }

        return true;
    }

    protected function _canRender()
    {
        // is not index.php
        if (strpos($_SERVER['SCRIPT_NAME'], '/static.php') > 0) {
            return false;
        }

        if (!$this->_helperData->isEnable()) {
            return false;
        }

        return true;
    }
}
