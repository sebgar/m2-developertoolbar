<?php
namespace Sga\DeveloperToolbar\Block;

use Sga\DeveloperToolbar\Block\Toolbar;

class Chooser extends Toolbar
{
    public function canDisplay()
    {
        $displayed = parent::canDisplay();
        $nb = $this->_session->getNumberOfRequest();
        return $displayed && $nb > 0 ? true : false;
    }

    public function getHtmlRequests()
    {
        return $this->_session->getHtmlRequests();
    }
}
