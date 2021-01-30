<?php
namespace Sga\DeveloperToolbar\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\State as AppState;
use Sga\DeveloperToolbar\Helper\Config;

class Data extends AbstractHelper
{
    const COOKIE_NAME_DISPLAY = 'dt_display';

    protected $_helperConfig;
    protected $_appState;

    public function __construct(
        Config $helperConfig,
        AppState $appState
    ){
        $this->_helperConfig = $helperConfig;
        $this->_appState = $appState;
    }

    public function isEnable()
    {
        // is not enable in fo
        if ($this->_appState->getAreaCode() === 'frontend' && !$this->_helperConfig->isEnabledFo()) {
            return false;
        }

        return true;
    }

    public function formatNodeXmlToHtml($node, $level = 0, $reservedClassNames = Array())
    {
        $nl = '<br/>';

        $classes = array(in_array($node->getName(), $reservedClassNames) ? 'node-' . $node->getName() : $node->getName());
        if ($level > 0) {
            $classes[] = 'pad';
        }

        $attribute = '';
        $attributes = $node->attributes();
        if ($attributes) {
            foreach ($attributes as $key => $value) {
                if ($key == 'remove') {
                    $classes[] = 'remove';
                }

                $attribute .= ' ' . $key . '="' . str_replace('"', '\"', (string)$value) . '"';
            }
        }

        $str = '<div class="' . implode(' ', $classes) . '">&lt;' . $node->getName();
        $str .= $attribute;

        if ($node->hasChildren()) {
            $str .= '&gt;' . $nl;
            foreach ($node->children() as $child) {
                $level++;
                $str .= $this->formatNodeXmlToHtml($child, $level, $reservedClassNames);
                $level--;
            }
            $str .= '&lt;/' . $node->getName() . '&gt;' . $nl;
        } else {
            $value = (string)$node;
            if (strlen($value)) {
                $str .= '&gt;' . $node->xmlentities($value) . '&lt;/' . $node->getName() . '&gt;' . $nl;
            } else {
                $str .= '/&gt;' . $nl;
            }
        }

        $str .= '</div>';

        return $str;
    }
}
