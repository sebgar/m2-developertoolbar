<?php
namespace Sga\DeveloperToolbar\Block\Toolbar;

use Sga\DeveloperToolbar\Block\Toolbar\AbstractBlock;

class Events extends AbstractBlock
{
    public function getCode()
    {
        return 'events';
    }

    public function getLabel()
    {
        return 'Events';
    }

    public function getEvents()
    {
        return $this->_helperRegister->getEvents();
    }

    public function getEventObservers($event)
    {
        $list = [];
        $observers = $this->_helperRegister->getObservers();
        foreach ($observers as $observer) {
            if ($observer['event'] === $event) {
                $list[] = $observer;
            }
        }
        return $list;
    }
}
