<?php
if (PHP_SAPI !== 'cli') {
    $_SERVER['SGA_PROFILER'] = new \Sga\DeveloperToolbar\Model\Profiler\Driver\Standard\Stat();
    \Magento\Framework\Profiler::applyConfig(
        [
            'drivers' => [
                [
                    'output' => 'Sga\DeveloperToolbar\Model\Profiler\Driver\Output\DevNull',
                    'stat' => $_SERVER['SGA_PROFILER'],
                ]
            ]
        ],
        BP,
        false
    );
}

\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Sga_DeveloperToolbar',
    __DIR__
);
