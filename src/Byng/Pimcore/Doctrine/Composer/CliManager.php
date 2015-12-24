<?php

namespace Byng\Pimcore\Doctrine\Composer;

use Composer\Script\Event;
use Composer\Util\Filesystem;
use Composer\Installer\PackageEvent;

class CliManager
{

    public static function postInstall(Event $event)
    {
        $config = $event->getComposer()->getConfig();
        $vendorDir = $config->get('vendor-dir');
        $rootPath = dirname($vendorDir);

        if(!file_exists($rootPath."/cli-config.php")) {
            copy(
                $vendorDir . "/byng-systems/pimcore-doctrine-library/cli-config.php",
                $rootPath . "/cli-config.php"
            );
        }
    }

}