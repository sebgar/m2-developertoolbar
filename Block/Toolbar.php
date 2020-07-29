<?php
namespace Sga\DeveloperToolbar\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Sga\DeveloperToolbar\Model\Session;

class Toolbar extends Template
{
    protected $_jsonSerializer;
    protected $_session;

    public function __construct(
        Context $context,
        Json $jsonSerializer,
        Session $session,
        array $data = []
    ) {
        $this->_jsonSerializer = $jsonSerializer;

        parent::__construct($context, $data);

        $this->_session = $session;
    }

    public function getJsonSerializer()
    {
        return $this->_jsonSerializer;
    }

    public function getUrlBuilder()
    {
        return $this->_urlBuilder;
    }

    public function canDisplay()
    {
        return true;
    }

    public function getRequestKey()
    {
        return $this->_session->getRequestKey();
    }

    public function getChildren()
    {
        return $this->_layout->getChildBlocks($this->getNameInLayout());
    }

    public function isShow()
    {
        return $this->_session->getCanAddOtherRequest();
    }

    public function getHtmlOtherRequests()
    {
        $html = '';

        if ($this->_session->getCanAddOtherRequest()) {
            // display all the request in session
            $requests = $this->_session->getHtmlRequests();
            if (is_array($requests)) {
                foreach ($requests as $request) {
                    $html .= $request;
                }
            }

            // clean session
            $this->_session->cleanHtmlRequests();
        }

        return $html;
    }
}
