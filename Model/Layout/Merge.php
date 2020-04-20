<?php
namespace Sga\DeveloperToolbar\Model\Layout;

use Magento\Framework\App\State;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\Filesystem\File\ReadFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Layout\LayoutCacheKeyInterface;

class Merge extends \Magento\Framework\View\Model\Layout\Merge
{
    protected $_fileSource;
    protected $_pageLayoutFileSource;
    protected $_readFactory;
    protected $_appState;

    public function __construct(
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Framework\Url\ScopeResolverInterface $scopeResolver,
        \Magento\Framework\View\File\CollectorInterface $fileSource,
        \Magento\Framework\View\File\CollectorInterface $pageLayoutFileSource,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Cache\FrontendInterface $cache,
        \Magento\Framework\View\Model\Layout\Update\Validator $validator,
        \Psr\Log\LoggerInterface $logger,
        ReadFactory $readFactory,
        \Magento\Framework\View\Design\ThemeInterface $theme = null,
        $cacheSuffix = '',
        LayoutCacheKeyInterface $layoutCacheKey = null,
        SerializerInterface $serializer = null
    ) {
        $this->_fileSource = $fileSource;
        $this->_pageLayoutFileSource = $pageLayoutFileSource;
        $this->_readFactory = $readFactory;
        $this->_appState = $appState;

        parent::__construct(
            $design, $scopeResolver, $fileSource, $pageLayoutFileSource, $appState, $cache, $validator, $logger,
            $readFactory, $theme, $cacheSuffix, $layoutCacheKey, $serializer
        );
    }

    public function getHandlesInformations()
    {
        $handlesInformations = [];

        $layoutStr = '';
        $theme = $this->_getPhysicalTheme($this->getTheme());
        $updateFiles = $this->_fileSource->getFiles($theme, '*.xml');
        $updateFiles = array_merge($updateFiles, $this->_pageLayoutFileSource->getFiles($theme, '*.xml'));
        $useErrors = libxml_use_internal_errors(true);
        foreach ($updateFiles as $file) {
            /** @var $fileReader \Magento\Framework\Filesystem\File\Read */
            $fileReader = $this->_readFactory->create($file->getFilename(), DriverPool::FILE);
            $fileStr = $fileReader->readAll($file->getName());
            $fileStr = $this->_substitutePlaceholders($fileStr);
            /** @var $fileXml \Magento\Framework\View\Layout\Element */
            $fileXml = $this->_loadXmlString($fileStr);
            if (!$fileXml instanceof \Magento\Framework\View\Layout\Element) {
                libxml_clear_errors();
                continue;
            }
            if (!$file->isBase() && $fileXml->xpath(self::XPATH_HANDLE_DECLARATION)) {
                continue;
            }
            $handleName = basename($file->getFilename(), '.xml');
            $tagName = $fileXml->getName() === 'layout' ? 'layout' : 'handle';
            $handleAttributes = ' id="' . $handleName . '"' . $this->_renderXmlAttributes($fileXml);
            $handleStr = '<' . $tagName . $handleAttributes . '>' . $fileXml->innerXml() . '</' . $tagName . '>';
            $layoutStr .= $handleStr;

            ///// BEGIN SGA
            if ($tagName === 'handle') {
                $handlesInformations[$handleName][] = [
                    'file' => $file,
                    'xml' => $fileXml
                ];
            }
            ///// END SGA
        }
        libxml_use_internal_errors($useErrors);

        return $handlesInformations;
    }
}
