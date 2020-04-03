<?php
namespace Sga\DeveloperToolbar\Block\Toolbar;

use Sga\DeveloperToolbar\Block\Toolbar\AbstractBlock;

class Request extends AbstractBlock
{
    public function getCode()
    {
        return 'request';
    }

    public function getLabel()
    {
        return 'Request';
    }

    public function getWebsite()
    {
        return $this->_storeManager->getWebsite();
    }

    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    public function getParams()
    {
        return $this->_request->getParams();
    }
}
