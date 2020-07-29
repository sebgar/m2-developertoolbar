<?php
namespace Sga\DeveloperToolbar\Block\Toolbar;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template\Context;
use Sga\DeveloperToolbar\Block\Toolbar;
use Sga\DeveloperToolbar\Helper\Register;
use Sga\DeveloperToolbar\Helper\Data as HelperData;
use Sga\DeveloperToolbar\Model\Session;

abstract class AbstractBlock extends Toolbar
{
    protected $_profiler;
    protected $_helperRegister;
    protected $_helperData;
    protected $_productMetaData;
    protected $_resource;
    protected $_directory;

    protected $_scale;

    public function __construct(
        Context $context,
        Json $jsonSerializer,
        Session $session,
        Register $helperRegister,
        HelperData $helperData,
        ProductMetadataInterface $productMetaData,
        ResourceConnection $resource,
        DirectoryList $directory,
        array $data = []
    ) {
        $this->_profiler = $_SERVER['SGA_PROFILER'];
        $this->_helperRegister = $helperRegister;
        $this->_helperData = $helperData;
        $this->_productMetaData = $productMetaData;
        $this->_resource = $resource;
        $this->_directory = $directory;

        parent::__construct($context, $jsonSerializer, $session, $data);

        $this->_preload();
    }

    public function getUrlBuilder()
    {
        return $this->_urlBuilder;
    }

    public function getHelperRegister()
    {
        return $this->_helperRegister;
    }

    public function getHelperData()
    {
        return $this->_helperData;
    }

    public function getAppState()
    {
        return $this->_appState;
    }

    public function getProductMetaData()
    {
        return $this->_productMetaData;
    }

    abstract public function getLabel();

    abstract public function getCode();

    public function hasContent()
    {
        return true;
    }

    public function canDisplay()
    {
        return true;
    }

    protected function _preload()
    {

    }

    protected function _getScales()
    {
        if (!isset($this->_scale)) {
            $max = 0;
            $sum = 0;

            $_timers = $this->_profiler->getTimers();
            $i = 0;
            foreach ($_timers as $name => $line) {
                if ($name !== 'magento') {
                    $s = $line['sum'];
                    if ($s > $max) {
                        $max = $s;
                    }

                    $sum += $s;
                    $i++;
                }
            }

            $moy = ($sum / $i);
            $step = ($max - $moy) / 3;
            $this->_scale = array(
                'start' => $moy,
                'step' => $step
            );
        }
        return $this->_scale;
    }

    public function getColorCss($time)
    {
        $scales = $this->_getScales();

        $class = '';
        if ($time > $scales['start'] && $time <= $scales['start'] + $scales['step']) {
            $class = ' min';
        } elseif ($time > $scales['start'] + $scales['step'] && $time <= $scales['start'] + $scales['step'] * 2) {
            $class = ' moy';
        } elseif ($time > $scales['start'] + $scales['step'] * 2) {
            $class = ' max';
        }

        return $class;
    }

    public function formatBytes($size)
    {
        $size = abs($size);
        $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
        if ($size == 0) {
            return 0;
        } else {
            return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i];
        }
    }

    public function roundNumber($number, $precision = 3)
    {
        return round($number, $precision);
    }
}
