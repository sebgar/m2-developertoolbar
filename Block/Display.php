<?php
namespace Sga\DeveloperToolbar\Block;

use Sga\DeveloperToolbar\Block\Toolbar;

class Display extends Toolbar
{
    public function isShow()
    {
        if (!isset($_COOKIE['dt_display']) || ($_COOKIE['dt_display'] == 'show')) {
            return true;
        }

        return false;
    }
}
