<?php

namespace Leon\BswBundle\Controller\Traits;

use Doctrine\Persistence\ObjectRepository;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Exception\RepositoryException;
use Leon\BswBundle\Repository\FoundationRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @property AbstractController $container
 * @property LoggerInterface    $logger
 */
trait Database
{
    /**
     * Get entity manager
     *
     * @param string $db
     *
     * @return ObjectManager
     */
    public function em(string $db): ObjectManager
    {
        return $this->getDoctrine()->getManager($db);
    }

    /**
     * Get instance for repository
     *
     * @param string $table
     * @param string $db
     *
     * @return ObjectRepository|FoundationRepository|ObjectManager|EntityRepository
     * @throws
     */
    public function repo(string $table, string $db = null)
    {
        $dbAuto = Helper::parseDoctrineName($table);

        /**
         * @var ManagerRegistry $manager
         */
        $manager = $this->getDoctrine();
        $collect = $manager->getConnectionNames();
        $db = $db ?? $dbAuto ?? Abs::DOCTRINE_DEFAULT;

        if (!isset($collect[$db])) {
            throw new RepositoryException("Doctrine connections '{$db}' don't exist");
        }

        /**
         * @var ObjectRepository|FoundationRepository|ObjectManager|EntityRepository $repo
         */
        $this->logger->debug("Use doctrine connection named {$db}");
        $repo = $manager->getRepository($table); // Switch db with arguments 2 has bug
        $repo->setEm($this->em($db)); // Fix bug of switch db

        return $repo;
    }

    /**
     * Get instance for query
     *
     * @param string $table
     * @param string $alias
     * @param string $db bug in Symfony4.3
     *
     * @return QueryBuilder
     * @throws
     */
    public function query(string $table, string $alias = null, string $db = null): QueryBuilder
    {
        $repository = $this->repo($table, $db);
        $alias = $alias ?: Helper::tableNameToAlias($table);

        return $repository->createQueryBuilder($alias);
    }

    /**
     * Get instance like PDO
     *
     * @param string $db
     *
     * @return Connection
     * @throws
     */
    public function pdo(string $db = null)
    {
        /**
         * @var Registry   $registry
         * @var Connection $connection
         */
        $registry = $this->container->get('doctrine');
        $db = $db ?? Abs::DOCTRINE_DEFAULT;
        $connection = $registry->getConnection($db);

        $this->logger->debug("Use pdo connection named {$db}");

        return $connection;
    }
}
