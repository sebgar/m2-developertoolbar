<?php
namespace Sga\DeveloperToolbar\Block\Toolbar;

use Sga\DeveloperToolbar\Block\Toolbar\AbstractBlock;

class Models extends AbstractBlock
{
    public function getCode()
    {
        return 'models';
    }

    public function getLabel()
    {
        return 'Models';
    }

    public function getModels()
    {
        return $this->_helperRegister->getModels();
    }

    public function getCollections()
    {
        return $this->_helperRegister->getCollections();
    }
}
