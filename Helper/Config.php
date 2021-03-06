<?php

namespace Sga\DeveloperToolbar\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_ENABLED_FO = 'dev/sga_developertoolbar/enabled_fo';

    public function isEnabledFo($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED_FO,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
