<?php
namespace Sga\DeveloperToolbar\Model;

use Magento\Framework\Session\SessionManager;

class Session extends SessionManager
{
    const MAX_REQUEST = 10;

    protected function _getRandomString($length)
    {
        return bin2hex(openssl_random_pseudo_bytes(5));
    }

    public function generateRequestKey()
    {
        $str = $this->_getRandomString(8);
        $this->setData('request_key', $str);
        return $str;
    }

    public function getRequestKey()
    {
        $key = $this->getData('request_key');
        if (null === $key) {
            $key = $this->generateRequestKey();
        }
        return $key;
    }

    public function getNumberOfRequest()
    {
        $list = $this->getData('html_requests');
        if (is_array($list)) {
            return count($list);
        } else {
            return 0;
;        }
    }

    public function getHtmlRequests($requestKey = null)
    {
        $html = $this->getData('html_requests');
        if (null === $requestKey) {
            return $html;
        } elseif (isset($html[$requestKey])) {
            return $html[$requestKey];
        }

        return null;
    }

    public function setHtmlRequests($value, $requestKey = null)
    {
        $list = $this->getData('html_requests');
        if (null === $requestKey) {
            if (is_array($list) && count($list) >= self::MAX_REQUEST - 1) {
                array_shift($list);
            }

            $key = $this->getRequestKey();
            $list[$key] = $value;
        } else {
            $list[$requestKey] = $value;
        }
        $this->setData('html_requests', $list);

        return $this;
    }

    public function cleanHtmlRequests($requestKey = null)
    {
        if (!isset($requestKey)) {
            $this->setData('html_requests', array());
        } else {
            $this->setHtmlRequests('', $requestKey);
        }

        return $this;
    }
}
