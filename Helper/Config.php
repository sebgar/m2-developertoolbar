<?php

namespace Sga\DeveloperToolbar\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    protected $_scopeConfig;

    const XML_PATH_ENABLED_BO = 'dev/sga_developertoolbar/enabled_bo';
    const XML_PATH_ENABLED_FO = 'dev/sga_developertoolbar/enabled_fo';

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Context $context
    )
    {
        $this->_scopeConfig = $scopeConfig;

        parent::__construct($context);
    }

    public function isEnabledBo($store = null)
    {
        return $this->_scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED_BO,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function isEnabledFo($store = null)
    {
        return $this->_scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED_FO,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
