<?php
namespace Sga\DeveloperToolbar\Block\Toolbar;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Interception\DefinitionInterface;
use Sga\DeveloperToolbar\Block\Toolbar\AbstractBlock;

class Server extends AbstractBlock
{
    public function getCode()
    {
        return 'server';
    }

    public function getLabel()
    {
        return 'Server';
    }

    protected function phpInfoCssLambda($value)
    {
        return ".phpinfodisplay " . preg_replace( "/,/", ",.phpinfodisplay ", $value );
    }

    public function getHtmlPhpInfo()
    {
        $profiler = $_SERVER['SGA_PROFILER'];
        $_SERVER['SGA_PROFILER'] = null;

        ob_start();
        phpinfo();
        preg_match('%<style type="text/css">(.*?)</style>.*?(<body>.*</body>)%s', ob_get_clean(), $matches);
        $html = "<style type='text/css'>" . PHP_EOL .
            join(
                PHP_EOL,
                array_map(
                    array(&$this, "phpInfoCssLambda"),
                    preg_split( '/\n/', trim($matches[1]))
                )
            ) .
            "</style>" . PHP_EOL .
            $matches[2];

        $_SERVER['SGA_PROFILER'] = $profiler;

        return $html;
    }
}
