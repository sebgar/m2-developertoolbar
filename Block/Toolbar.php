<?php
namespace Sga\DeveloperToolbar\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Serialize\Serializer\Json;

class Toolbar extends Template
{
    protected $_jsonSerializer;

    public function __construct(
        Context $context,
        Json $jsonSerializer,
        array $data = []
    ) {
        $this->_jsonSerializer = $jsonSerializer;

        parent::__construct($context, $data);
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
        return '1';
    }

    public function getChildren()
    {
        return $this->_layout->getChildBlocks($this->getNameInLayout());
    }

    public function isShow()
    {
        if (!isset($_COOKIE['xdt_display']) || ($_COOKIE['xdt_display'] == 'show')) {
            return true;
        }

        return false;
    }
}
