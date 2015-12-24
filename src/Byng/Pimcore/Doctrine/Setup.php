<?php

namespace Byng\Pimcore\Doctrine;

use Pimcore\Config;
use Doctrine\ORM\Tools\Setup as DoctrineSetup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Cache\Cache;

class Setup
{
    /**
     * 
     * @var array
     */
    private $entityPaths;

    /**
     * 
     * @var boolean
     */
    private $isDevMode;

    /**
     * 
     * @var string
     */
    private $proxyDir;

    /**
     * 
     * @var \Doctrine\Common\Cache\Cache
     */
    private $cache;

    /**
     * 
     * @var \Doctrine\ORM\EntityManager
     */
    private static $em;

    /**
     * Constructor. Initialize required properties.
     * 
     * @param array $entityPaths
     * @param bool  $isDevMode
     *
     * @throws \Byng\Pimcore\Doctrine\EntityPathNotFoundException
     */
    public function __construct(array $entityPaths = [], $isDevMode = false)
    {
        if (empty($entityPaths)) {
            throw new EntityPathException("Must supply atleast one entities path");
        }

        foreach ($entityPaths as $path) {
            if (!is_dir($path)) {
                throw new EntityPathException("$path is not a directory");
            }
        }

        $this->entityPaths = $entityPaths;
        $this->isDevMode = $isDevMode;
    }

    /**
     * 
     * @param string $proxyDir
     */
    public function setProxyDir($proxyDir)
    {
        $this->proxyDir = $proxyDir;
    }

    /**
     * 
     * @param \Doctrine\Common\Cache\Cache $cache
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Initialize the entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function init()
    {
        $config = DoctrineSetup::createAnnotationMetadataConfiguration(
            $this->entityPaths,
            $this->isDevMode,
            $this->proxyDir,
            $this->cache,
            false
        );
        
        $em = EntityManager::create($this->getDbParams(), $config);

        $platform = $em->getConnection()->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');

        static::$em = $em;
        return $em;
    }

    /**
     * get the configured entity manager. Must call init() first to
     * create it.
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    public static function getEntityManager()
    {
        return static::$em;
    }

    /**
     * 
     * 
     * @return array
     */
    protected function getDbParams()
    {
        $config = Config::getSystemConfig();
        $db = $config->database;

        return array(
            'driver'   => strtolower($db->adapter),
            'user'     => $db->params->username,
            'password' => $db->params->password,
            'dbname'   => $db->params->dbname,
        );
    }

}
