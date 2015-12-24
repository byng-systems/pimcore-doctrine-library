# Pimcore doctrine plugin

This plugin allows developers to use doctrine orm to manage objects outside of pimcore.

# Usage

## Installation

Add the plugin in composer.json
```json
"require": {
    "byng-systems/pimcore-doctrine-library": "dev-master"
}
```
You will also need to add a post-install script to install the doctrine cli script. If you don't add the following line then you will have to manually copy 'cli-config.php' from inside the plugin folder to your document root.
```json
"scripts": {
    "post-install-cmd": "Byng\\Pimcore\\Doctrine\\Composer\\CliManager::postInstall",
    "post-update-cmd": "Byng\\Pimcore\\Doctrine\\Composer\\CliManager::postInstall"
}
```

## Setup

Add the following to 'website/var/config/startup.php'. Set the $entityDir to wherever you wish to create your entities.
```php
$entityDir = PIMCORE_DOCUMENT_ROOT . "/website/lib/Entity";
$setup = new \Byng\Pimcore\Doctrine\Setup([$entityDir]);
$em = $setup->init();
```
You can store the entity manager reference ($em) in your DI container or Zend_Registry if you wish. You can also retrieve it from the setup class from anywhere in your code base:
```php
$em = \Byng\Pimcore\Doctrine\Setup::getEntityManager();
```

## Test

Open a terminal and 'cd' to your document root and run the following command:
```bash
./vendor/bin/doctrine
```
You should see a list of all available doctrine commands

## Example

Create a product entity

NB: You'll probably have to add the 'Entity' namespace to your autoloader.

website/lib/Entity/Product.php
```php
<?php
namespace Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="products")
 **/
class Product
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue 
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}
```

Create the products table using the doctrine cli

```bash
./vendor/bin/doctrine orm:schema-tool:update --force
```

Create a repository class to handle product entities

website/lib/Entity/Repository/ProductRepository.php
```php
<?php
namespace Entity\Repository;

use Byng\Pimcore\Doctrine\AbstractRepository;

class ProductRepository extends AbstractRepository
{
    const ENTITY_CLASS = "Entity\\Product";
    
    /**
     *
     * @return string
     */
    protected function getEntityClass()
    {
        return static::ENTITY_CLASS;
    }
}

```

Finally we can write code to persist our entity

```php
<?php
use Entity\Repository\ProductRepository;
use Byng\Pimcore\Doctrine\Setup;
use Entity\Product;

$product = new Product();
$product->setName("Test");

$repository = new ProductRepository(Setup::getEntityManager());
$repository->save($product);
```