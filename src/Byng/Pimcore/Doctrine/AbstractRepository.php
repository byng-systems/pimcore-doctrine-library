<?php

namespace Byng\Pimcore\Doctrine;

use Doctrine\ORM\EntityManager;

abstract class AbstractRepository
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->setEntityManager($entityManager);
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * 
     * @return \ByngLogBundle\Entity\Repository\AbstractRepository
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Retrieve all objects of the managed entity
     * 
     * @return array
     */
    public function findAll()
    {
        return $this->getEntityRepository()->findAll();
    }

    /**
     * Fetch a single object by its id
     * 
     * @param int $id
     * 
     * @return entity
     */
    public function findById($id)
    {
        return $this->getEntityRepository()->find($id);
    }

    /**
     * Delete an entity
     * 
     * @param entity $entity
     */
    public function delete($entity)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($entity);
        $entityManager->flush($entity);
    }

    /**
     * Save an entity. A shorthand method for calling persist and flush.
     * 
     * @param object $entity
     */
    public function save($entity)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush($entity);
    }

    /**
     * 
     * @return \ByngLogBundle\Entity\Repository\AbstractRepository
     */
    public function getEntityRepository()
    {
        return $this->getEntityManager()->getRepository(static::getEntityClass());
    }

    /**
     * Returns the fully-qualified class name of the single entity class that is
     * to be provided by a service extending this class
     *
     * @return string
     */
    protected abstract function getEntityClass();
}
