<?php

require_once 'pimcore/cli/startup.php';

use Byng\Pimcore\Doctrine\Setup;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

$entityManager = Setup::getEntityManager();
return ConsoleRunner::createHelperSet($entityManager);