<?php


namespace PhpMyAdminBundle\Repository;

use Doctrine\ORM\EntityManager;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAware;
use Knp\Component\Pager\Paginator;

/**
 * Class GenericRepository
 * @package PhpMyAdminBundle\Repository
 */
abstract class GenericRepository
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Paginator
     */
    private $paginator;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager() {
        return $this->entityManager;
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection() {
        return $this->getEntityManager()->getConnection();
    }

    /**
     * @return string
     */
    abstract function getTable();

    /**
     * Sets the KnpPaginator instance.
     *
     * @param Paginator $paginator
     *
     * @return PaginatorAware
     */
    public function setPaginator(Paginator $paginator)
    {
        $this->paginator = $paginator;

        return $this;
    }

    /**
     * Returns the KnpPaginator instance.
     *
     * @return Paginator
     */
    public function getPaginator()
    {
        return $this->paginator;
    }
}