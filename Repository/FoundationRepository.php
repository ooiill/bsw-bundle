<?php

namespace Leon\BswBundle\Repository;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\Event\Subscriber\Paginate\Doctrine\ORM\QuerySubscriber;
use Knp\Component\Pager\Pagination\AbstractPagination;
use Knp\Component\Pager\Pagination\SlidingPagination;
use Knp\Component\Pager\Paginator as KnpPaginator;
use Leon\BswBundle\Component\Helper;
use Leon\BswBundle\Entity\FoundationEntity;
use Leon\BswBundle\Module\Doctrine\ArrayHydration;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorDebugExit;
use Leon\BswBundle\Module\Exception\EntityException;
use Leon\BswBundle\Module\Exception\LogicException;
use Leon\BswBundle\Module\Exception\RepositoryException;
use Leon\BswBundle\Module\Traits as MT;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Psr\Log\LoggerInterface;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository as SFRepository;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManager;
use Throwable;

abstract class FoundationRepository extends SFRepository
{
    use MT\Init,
        MT\Magic,
        MT\Message;

    /**
     * @const string
     */
    const HINT_COUNT = QuerySubscriber::HINT_COUNT;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $doctrineName = Abs::DOCTRINE_DEFAULT;

    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Expr
     */
    protected $expr;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var string
     */
    protected $pk;

    /**
     * @var array
     */
    protected $filter = [];

    /**
     * MasterRepository constructor.
     *
     * @param ContainerInterface  $container
     * @param ManagerRegistry     $registry
     * @param ManagerRegistry     $doctrine
     * @param ValidatorInterface  $validator
     * @param TranslatorInterface $translator
     * @param LoggerInterface     $logger
     *
     * @throws
     */
    public function __construct(
        ContainerInterface $container,
        ManagerRegistry $registry,
        ManagerRegistry $doctrine,
        ValidatorInterface $validator,
        TranslatorInterface $translator,
        LoggerInterface $logger
    ) {
        if (!isset($this->entity)) {
            $this->entity = str_replace('Repository', 'Entity', static::class);
            $this->entity = substr($this->entity, 0, -6);
        }

        if (!class_exists($this->entity)) {
            throw new EntityException("Entity not exits `{$this->entity}`");
        }

        parent::__construct($registry, $this->entity);

        $this->container = $container;
        $this->doctrine = $doctrine;
        $this->validator = $validator;
        $this->translator = $translator;
        $this->logger = $logger;

        if ($autoDoctrine = Helper::parseDoctrineName(static::class)) {
            $this->doctrineName = $autoDoctrine;
        }

        // $this->setEm($this->getEntityManager());
        $this->setEm($this->doctrine->getManager($this->doctrineName));
        $this->expr = new Expr();
        $this->pk = $this->_class->getSingleIdentifierColumnName();

        $this->init();
    }

    /**
     * Get instance for query
     *
     * @return QueryBuilder
     * @throws
     */
    public function query(): QueryBuilder
    {
        return $this->createQueryBuilder(Helper::tableNameToAlias($this->entity));
    }

    /**
     * Get entity manager
     *
     * @return EntityManager
     * @throws
     */
    public function em(): EntityManager
    {
        if (!$this->em->isOpen()) {
            $this->em = $this->em->create(
                $this->em->getConnection(),
                $this->em->getConfiguration()
            );
        }

        return $this->em;
    }

    /**
     * Set entity manager
     *
     * @param ObjectManager|EntityManager $em
     *
     * @return $this
     */
    public function setEm(ObjectManager $em): self
    {
        $this->em = $em;
        $this->_em = $em;

        return $this;
    }

    /**
     * @return Connection
     */
    public function pdo(): Connection
    {
        return $this->em()->getConnection();
    }

    /**
     * Get validator error
     *
     * @param ConstraintViolationListInterface $error
     * @param int                              $index
     *
     * @return array
     */
    protected function error(ConstraintViolationListInterface $error, int $index = 0): array
    {
        $message = $error->get($index)->getMessage();
        $messageHandling = $this->translator->trans($message, [], 'messages');

        $fields = $error->get($index)->getPropertyPath();
        $fields = $fieldsHanding = Helper::stringToArray($fields);

        foreach ($fieldsHanding as $key => $field) {
            $field = Helper::stringToLabel($field);
            $field = $this->translator->trans($field, [], 'fields');
            $fieldsHanding[$key] = $field;
        }

        $fields = implode(', ', $fields);
        $fieldsHanding = implode(', ', $fieldsHanding);

        $this->logger->error(
            "Persistence error with field `{$fields}` in {$this->entity}, {$message}, {$messageHandling}"
        );

        return ["{$messageHandling} ({$fieldsHanding})", "{$message} ({$fields})"];
    }

    /**
     * Persistence
     *
     * @param FoundationEntity $entity
     * @param array            $attributes
     * @param array|null       $group
     *
     * @return false|int
     * @throws
     */
    protected function persistence(FoundationEntity $entity, array $attributes, ?array $group = null)
    {
        $entity->attributes($attributes);

        // validator
        $error = $this->validator->validate($entity, null, $group);
        if (count($error)) {
            $error = $this->error($error);

            return $this->push(current($error), Abs::TAG_VALIDATOR);
        }

        $em = $this->em();

        // persistence
        try {
            $em->persist($entity);
            $em->flush();
            $em->clear();
        } catch (Throwable $e) {
            return $this->push($e->getMessage(), Abs::TAG_PERSISTENCE);
        }

        return $entity->{$this->pk};
    }

    /**
     * @return string
     */
    public function pk(): string
    {
        return $this->pk;
    }

    /**
     * Transactional
     *
     * @param callable $logic
     * @param bool     $throw
     *
     * @return false|mixed|null
     * @throws
     *
     * @license Transaction block cannot in foreach, just foreach block in transaction
     */
    public function transactional(callable $logic, bool $throw = true)
    {
        $em = $this->em();
        $em->beginTransaction();

        try {
            $result = call_user_func_array($logic, [$this]);
            $em->flush();
            $em->commit();

            return $result === false ? null : $result;

        } catch (Throwable $error) {

            $em->close();
            $em->rollBack();

            $message = "{$error->getMessage()} in {$error->getFile()} line {$error->getLine()}";
            $this->logger->warning("Transactional process failed, {$message}");

            if ($error instanceof ValidatorException) {
                return $this->push($error->getMessage(), Abs::TAG_ROLL_VALIDATOR);
            }

            if ($error instanceof LogicException) {
                return $this->push($error->getMessage(), Abs::TAG_ROLL . $error->getCode());
            }

            if (!$throw) {
                return $this->push($error->getMessage());
            }

            throw new RepositoryException($error->getMessage());
        }
    }

    /**
     * Newly
     *
     * @param array $attributes
     *
     * @return false|int
     * @throws
     */
    public function newly(array $attributes)
    {
        return $this->persistence(new $this->entity, $attributes, [Abs::VALIDATOR_GROUP_NEWLY]);
    }

    /**
     * Newly multiple
     *
     * @param array $batch
     * @param int   $per
     * @param bool  $throw
     *
     * @return false|int
     * @throws
     */
    public function newlyMultiple(array $batch, int $per = null, bool $throw = true)
    {
        return $this->transactional(
            function () use ($batch, $per) {

                $i = 0;
                $per = ($per ?? Abs::MULTIPLE_PER);
                $em = $this->em();

                foreach ($batch as $record) {

                    /**
                     * @var FoundationEntity $entity
                     */
                    $entity = new $this->entity;
                    $entity->attributes($record);

                    // validator
                    $error = $this->validator->validate($entity, null, [Abs::VALIDATOR_GROUP_NEWLY]);
                    if (count($error)) {
                        $error = $this->error($error);
                        throw new ValidatorException(current($error));
                    }

                    $i++;
                    $em->persist($entity);

                    if ($i % $per === 0) {
                        $em->flush();
                        $em->clear();
                    }
                }

                $em->flush();
                $em->clear();

                return count($batch);
            },
            $throw
        );
    }

    /**
     * Newly or modify
     *
     * @param array    $criteria
     * @param array    $attributes
     * @param callable $exists
     *
     * @return false|int
     * @throws
     */
    public function newlyOrModify(array $criteria, array $attributes, callable $exists = null)
    {
        $record = $this->findOneBy($criteria);
        $group = [Abs::VALIDATOR_GROUP_MODIFY];

        if (empty($record)) {

            // newly
            $record = new $this->entity;
            $group = [Abs::VALIDATOR_GROUP_NEWLY];
            $attributes = array_merge($criteria, $attributes);

        } elseif ($exists) {

            // modify
            $attributes = call_user_func_array($exists, [$record, $attributes]);
            Helper::callReturnType($attributes, Abs::T_ARRAY, 'Newly or modify handler');
        }

        /**
         * @var FoundationEntity $record
         */
        return $this->persistence($record, $attributes, $group);
    }

    /**
     * Away
     *
     * @param array $criteria
     * @param bool  $throw
     *
     * @return false|int
     * @throws
     */
    public function away(array $criteria, bool $throw = true)
    {
        $batch = $this->findBy($criteria);
        if (empty($batch)) {
            return 0;
        }

        return $this->transactional(
            function () use ($batch) {

                $em = $this->em();
                foreach ($batch as $entity) {
                    $entity = $em->merge($entity);
                    $em->remove($entity);
                }

                $em->flush();
                $em->clear();

                return count($batch);
            },
            $throw
        );
    }

    /**
     * Away (strict match one)
     *
     * @param array $criteria
     *
     * @return int
     * @throws
     */
    public function awayOnlyOne(array $criteria)
    {
        $total = $this->count($criteria);
        if ($total !== 1) {
            return 0;
        }

        /**
         * @var FoundationEntity $entity
         */
        $entity = $this->findOneBy($criteria);

        $em = $this->em();
        $em->remove($entity);
        $em->flush();
        $em->clear();

        return 1;
    }

    /**
     * Modify
     *
     * @param array $criteria
     * @param array $attributes
     * @param int   $per
     * @param bool  $throw
     *
     * @return false|int
     * @throws
     */
    public function modify(array $criteria, array $attributes, int $per = null, bool $throw = true)
    {
        $batch = $this->findBy($criteria);
        if (empty($batch)) {
            return 0;
        }

        return $this->transactional(
            function () use ($batch, $attributes, $per) {

                $i = 0;
                $per = ($per ?? Abs::MULTIPLE_PER);
                $em = $this->em();

                foreach ($batch as $entity) {

                    /**
                     * @var FoundationEntity $entity
                     */
                    $entity->attributes($attributes);

                    // validator
                    $error = $this->validator->validate($entity, null, [Abs::VALIDATOR_GROUP_MODIFY]);
                    if (count($error)) {
                        $error = $this->error($error);
                        throw new ValidatorException(current($error));
                    }

                    $i++;
                    $em->persist($entity);

                    if ($i % $per === 0) {
                        $em->flush();
                        $em->clear();
                    }
                }

                $em->flush();
                $em->clear();

                return count($batch);
            },
            $throw
        );
    }

    /**
     * Modify (strict match one)
     *
     * @param array $criteria
     * @param array $attributes
     *
     * @return false|int
     * @throws
     */
    public function modifyOnlyOne(array $criteria, array $attributes)
    {
        $total = $this->count($criteria);
        if ($total !== 1) {
            return 0;
        }

        /**
         * @var FoundationEntity $entity
         */
        $entity = $this->findOneBy($criteria);
        $entity->attributes($attributes);

        // validator
        $error = $this->validator->validate($entity, null, [Abs::VALIDATOR_GROUP_MODIFY]);
        if (count($error)) {
            $error = $this->error($error);

            return $this->push(current($error));
        }

        $em = $this->em();
        $em->persist($entity);
        $em->flush();
        $em->clear();

        return 1;
    }

    /**
     * Get query builder
     *
     * @param array &$filter
     *
     * @return QueryBuilder
     * @throws
     */
    protected function getQueryBuilder(array &$filter)
    {
        extract($filter);

        /*
         * Create
         */

        $em = $this->em();
        $em->getConfiguration()->addCustomHydrationMode(ArrayHydration::HYDRATE_ARRAY, ArrayHydration::class);
        $model = $em->createQueryBuilder();

        $table = $from ?? $this->entity;

        // from sub query
        if (is_array($table)) {
            $fromModel = $this->getQueryBuilder($table);
            $table = "({$fromModel->getDQL()})";
            if (empty($alias)) {
                throw new RepositoryException('Variable `alias` must be configured manually');
            }
        } else {
            $alias = $alias ?? Helper::tableNameToAlias($table);
        }

        /*
         * From
         */

        $model->from($table, $alias);

        /*
         * Where
         */

        $where = $where ?? [];
        if (!is_array($where)) {
            throw new RepositoryException('Variable `where` should be array if configured');
        }

        $exprNamespace = Expr::class;
        $where = array_filter($where);

        foreach ($where as $expr) {

            if (!is_string($expr) && !is_object($expr)) {
                throw new RepositoryException("Items of variable `where` must string or object");
            }

            if (is_object($expr) && Helper::nsName(get_class($expr)) !== $exprNamespace) {
                throw new RepositoryException("Items of variable `where` must namespaces `{$exprNamespace}`");
            }

            $model->andWhere($expr);
        }

        /*
         * Set
         */

        $set = $set ?? [];
        if (!is_array($set)) {
            throw new RepositoryException('Variable `set` should be array if configured');
        }

        $set = array_filter($set);
        foreach ($set as $field => $value) {
            $model->set($field, $value);
        }

        /*
         * Group
         */

        if (isset($group)) {
            if (is_array($group)) {
                $group = implode(', ', $group);
            }
            $model->groupBy($group);
        }

        /*
         * Having
         */

        $having = $having ?? [];
        if (!is_array($having)) {
            throw new RepositoryException('Variable `having` should be array if configured');
        }

        $having = array_filter($having);

        foreach ($having as $expr) {

            if (!is_string($expr) && !is_object($expr)) {
                throw new RepositoryException("Items of variable `having` must string or object");
            }

            if (is_object($expr) && Helper::nsName(get_class($expr)) !== $exprNamespace) {
                throw new RepositoryException("Items of variable `having` must namespaces `{$exprNamespace}`");
            }

            $model->having($expr);
        }

        /*
         * Join
         */

        $join = $join ?? [];
        if (!is_array($join)) {
            throw new RepositoryException('Variable `join` should be array if configured');
        }

        $joinMode = ['left', 'inner'];
        $join = array_filter($join);

        foreach ($join as $aliasHandling => $item) {

            if (!is_array($item)) {
                throw new RepositoryException('Item of variable `join` should be two-dimensional array');
            }

            if (empty($item['entity'])) {
                throw new RepositoryException('Item `entity` of variable `join` must configure');
            }

            $entity = $item['entity'];
            $mode = $item['type'] ?? $joinMode[0];
            if (!in_array($mode, $joinMode)) {
                throw new RepositoryException('Item `type` of variable `join` invalid');
            }

            $mode = "{$mode}Join";
            $join[$aliasHandling]['alias'] = $aliasHandling;

            $onLeft = $item['left'] ?? [];
            $onRight = $item['right'] ?? [];
            $onOperator = $item['operator'] ?? [];

            if (!is_array($onLeft) || !is_array($onRight) || count($onLeft) != count($onRight)) {
                throw new RepositoryException(
                    'Items `left & right` of variable `join` should be array and same number'
                );
            }

            if (empty($onLeft)) {
                $joinTable = lcfirst(Helper::clsName($entity));
                array_push($onLeft, "{$alias}.{$joinTable}" . ucfirst($this->pk));
                array_push($onRight, "{$aliasHandling}.{$this->pk}");
            }

            $joinOn = [];
            foreach ($onLeft as $index => $left) {
                $operator = $onOperator[$index] ?? '=';
                $right = $onRight[$index];
                $joinOn[] = "{$left} {$operator} {$right}";
            }

            // join sub query
            if (is_array($entity)) {
                $joinModel = $this->getQueryBuilder($entity);
                $entity = "({$joinModel->getDQL()})";
            }

            $joinOn = implode(' AND ', $joinOn);
            $model->{$mode}($entity, $aliasHandling, Expr\Join::WITH, "({$joinOn})");
        }

        /*
         * Method
         */

        $method = $method ?? Abs::SELECT;

        /*
         * Select
         */

        if (!isset($select)) {
            $select = array_merge([$alias], array_keys($join));
        } elseif (!is_array($select)) {
            throw new RepositoryException('Variable `select` should be array if configured');
        }

        if ($method === Abs::SELECT) {

            $aliasEntity = array_column($join, 'entity', 'alias');
            $aliasEntity[$alias] = $this->entity;
            $aliasLength = count($aliasEntity);

            $selectHandling = [];
            $select = array_unique(array_filter($select));

            foreach ($select as $name) {
                if (!isset($aliasEntity[$name]) || $aliasLength == 1) {
                    array_push($selectHandling, $name);
                    continue;
                }

                $fields = array_keys(Helper::entityToArray(new $aliasEntity[$name]));
                $fields = Helper::arrayMap($fields, "{$name}.%s");
                $selectHandling = array_merge($selectHandling, $fields);
            }

            $model->select($selectHandling);

        } elseif ($method === Abs::DELETE) {

            /*
             * Delete
             */

            $model->delete($table, $alias);

        } elseif ($method === Abs::UPDATE) {

            /*
             * Update
             */

            $model->update($table, $alias);
        }

        /*
         * Sort to last when eq 0 or NULL
         */

        $sortMode = [Abs::SORT_ASC, Abs::SORT_DESC, null];

        $sort = $sort ?? [];
        if (!is_array($sort)) {
            throw new RepositoryException('Variable `sort` should be array if configured');
        }

        $sort = array_filter($sort);
        foreach ($sort as $field => $mode) {

            if (!in_array($mode, $sortMode)) {
                throw new RepositoryException("Item `{$field}` of variable `sort` invalid");
            }

            $index = ($mode == Abs::SORT_ASC ? PHP_INT_MAX : 0);
            $sortName = str_replace('.', '_', strtoupper($field) . "_FOR_SORT");
            $model->addSelect(
                "CASE WHEN {$field} = 0 OR {$field} IS NULL THEN {$index} ELSE {$field} END AS HIDDEN {$sortName}"
            );
            $model->addOrderBy($sortName, $mode);
        }

        /*
         * Order
         */

        $order = $order ?? [];
        if (!is_array($order)) {
            throw new RepositoryException('Variable `order` should be array if configured');
        }

        $order = array_filter($order);
        foreach ($order as $field => $mode) {

            if (!in_array($mode, $sortMode)) {
                throw new RepositoryException("Item `{$field}` of variable `sort` invalid");
            }

            $model->addOrderBy($field, $mode);
        }

        /*
         * Page
         */

        $limitDefaultMap = [
            Abs::SELECT => Abs::PAGE_DEFAULT_SIZE,
        ];

        $pageArgs = Helper::pageArgs(
            [
                'paging' => $paging ?? false,
                'page'   => $page ?? 1,
                'limit'  => $limit ?? ($limitDefaultMap[$method] ?? 0),
            ],
            Abs::PAGE_DEFAULT_SIZE
        );

        extract($pageArgs);

        /*
         * Args
         */

        $args = $args ?? [];
        if (!is_array($args)) {
            throw new RepositoryException('Variable `args` should be array if configured');
        }

        $typeMode = [true => Types::INTEGER, false => Types::STRING];
        $args = array_filter($args);

        foreach ($args as $key => $item) {
            if (!is_array($item) || !isset($item[0])) {
                throw new RepositoryException(
                    "Item of variable `args` value should be array and index 0 configured in key `{$key}`"
                );
            }

            $type = $item[1] ?? true;
            $model->setParameter($key, $item[0], $typeMode[$type] ?? $type);
        }

        /*
         * Custom
         */

        if (isset($query) && is_callable($query)) {
            call_user_func_array($query, [&$model]);
        }

        /*
         * Hint
         */

        $hint = $hint ?? false;
        if (!is_bool($hint) && !is_int($hint)) {
            throw new RepositoryException('Variable `hint` should be integer/boolean if configured');
        }

        if ($hint === true) {
            $hintModel = clone $model;
            [$sql, $sqlParams, $sqlTypes] = $this->getQL($hintModel, null, true);
            $sql = sprintf("SELECT COUNT(*) AS __count FROM (%s) AS __count_table", $sql);

            $count = $this->pdo()->fetchAssoc($sql, $sqlParams, $sqlTypes);
            $hint = intval(current($count));
        }

        /*
         * Additional parameters
         */

        if (!empty($parameters)) {
            $model->setParameters($parameters);
        }

        $debug = $debug ?? false;
        $filter = array_merge(
            $filter,
            compact('table', 'alias', 'select', 'limit', 'paging', 'page', 'offset', 'hint', 'debug')
        );

        return $model;
    }

    /**
     * Format for QL
     *
     * @param string $ql
     *
     * @return string
     */
    protected function formatQL(string $ql): string
    {
        $keywords = [
            'INSERT',
            'DELETE',
            'UPDATE',
            'SELECT',
            'FROM',
            'LEFT JOIN',
            'RIGHT JOIN',
            'SET',
            'WHERE',
            'GROUP BY',
            'HAVING',
            'ORDER BY',
            'LIMIT',
            'OFFSET',
        ];
        foreach ($keywords as $keyword) {
            $ql = str_replace("{$keyword} ", "\n{$keyword} ", $ql);
        }

        return $ql;
    }

    /**
     * Debug for QL
     *
     * @param QueryBuilder $model
     * @param Query        $query
     * @param bool         $isSQL
     */
    protected function debugQL(QueryBuilder $model, ?Query $query, bool $isSQL = false)
    {
        if ($isSQL) {
            $ql = $query ? $query->getSQL() : $model->getQuery()->getSQL();
        } else {
            $ql = $query ? $query->getDQL() : $model->getDQL();
        }

        $ql = $this->formatQL($ql);
        dump(ltrim($ql), $model->getParameters());
        exit(ErrorDebugExit::CODE);
    }

    /**
     * Get the QL
     *
     * @param QueryBuilder $model
     * @param Query        $query
     * @param bool         $isSQL
     *
     * @return array
     */
    protected function getQL(QueryBuilder $model, ?Query $query, bool $isSQL = false): array
    {
        if (!$isSQL) {
            if ($query) {
                return [$query->getDQL(), $query->getParameters(), []];
            } else {
                return [$model->getDQL(), $model->getParameters(), []];
            }
        }

        if ($query) {
            $sql = $query->getSQL();
            $params = $query->getParameters();
        } else {
            $sql = $model->getQuery()->getSQL();
            $params = $model->getParameters();
        }

        /**
         * @var Parameter[] $params
         */
        $sqlParams = [];
        $sqlTypes = [];
        foreach ($params as $item) {
            $sqlParams[] = $item->getValue();
            $sqlTypes[] = $item->getType();
        }

        return [$sql, $sqlParams, $sqlTypes];
    }

    /**
     * Set filters
     *
     * @param array ...$filter
     *
     * @return self
     * @noinspection PhpDocSignatureInspection
     */
    public function filters(array ...$filter): self
    {
        $this->filter = Helper::merge(...$filter);

        return $this;
    }

    /**
     * Get filters
     *
     * @param array ...$filter
     *
     * @return array
     * @noinspection PhpDocSignatureInspection
     */
    public function getFilters(array ...$filter): array
    {
        $filter = Helper::merge($this->filter, ...$filter);
        $this->filter = [];

        return $filter;
    }

    /**
     * Lister
     *
     * @param array $filter
     * @param bool  $getSQL
     * @param int   $hydrationMode
     *
     * @return array|object
     * @throws
     */
    public function lister(array $filter, bool $getSQL = false, $hydrationMode = AbstractQuery::HYDRATE_ARRAY)
    {
        $filter = $this->getFilters($filter, ['method' => Abs::SELECT]);
        $model = $this->getQueryBuilder($filter);
        $query = $model->getQuery();

        if (!empty($filter['hydrate'])) {
            $hydrationMode = $filter['hydrate'];
        }

        if (!$filter['paging']) {
            $query->setFirstResult($filter['offset']);
            if ($filter['limit']) {
                $query->setMaxResults($filter['limit']);
            }
            if ($filter['limit'] === 1) {
                return $query->getOneOrNullResult($hydrationMode);
            }

            if ($filter['debug']) {
                $this->debugQL($model, $query);
            }

            if ($getSQL) {
                return $this->getQL($model, $query, true);
            }

            return $query->getResult($hydrationMode);
        }

        $query->setHydrationMode($hydrationMode);
        if (is_int($filter['hint'])) {
            $query->setHint(self::HINT_COUNT, $filter['hint']);
        }

        return $this->knpPaginator($model, $query, $filter, $getSQL);
    }

    /**
     * Doctrine paginator
     *
     * @param QueryBuilder $model
     * @param Query        $query
     * @param array        $filter
     * @param bool         $getSQL
     *
     * @return array
     */
    protected function doctrinePaginator(QueryBuilder $model, Query $query, array $filter, bool $getSQL = false): array
    {
        $useOutputWalkers = false;

        $hasJoin = !!$model->getDQLPart('join');
        $paginator = new DoctrinePaginator($query, $hasJoin);
        $paginator->setUseOutputWalkers($useOutputWalkers);

        if ($query->hasHint(self::HINT_COUNT)) {
            $totalItem = $query->getHint(self::HINT_COUNT);
        } else {
            $totalItem = $paginator->count();
        }

        $query = $paginator
            ->getQuery()
            ->setFirstResult(($filter['page'] - 1) * $filter['limit'])
            ->setMaxResults($filter['limit']);

        if ($filter['debug']) {
            $this->debugQL($model, $query);
        }

        if ($getSQL) {
            return $this->getQL($model, $query, true);
        }

        $items = [];
        foreach ($paginator as $item) {
            array_push($items, $item);
        }

        return [
            Abs::PG_CURRENT_PAGE => $filter['page'],
            Abs::PG_PAGE_SIZE    => $filter['limit'],
            Abs::PG_TOTAL_PAGE   => ceil($totalItem / $filter['limit']),
            Abs::PG_TOTAL_ITEM   => $totalItem,
            Abs::PG_ITEMS        => $items,
        ];
    }

    /**
     * Knp paginator
     *
     * @param QueryBuilder $model
     * @param Query        $query
     * @param array        $filter
     * @param bool         $getSQL
     *
     * @return array
     */
    protected function knpPaginator(QueryBuilder $model, ?Query $query, array $filter, bool $getSQL = false): array
    {
        $options = ['distinct' => $filter['distinct'] ?? false];
        if (!empty($filter['group']) || !empty($filter['having'])) {
            $options = array_merge($options, ['distinct' => false, 'wrap-queries' => true]);
        }

        /**
         * @var KnpPaginator                         $paginator
         * @var AbstractPagination|SlidingPagination $pagination
         */
        $paginator = $this->container->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query ?: $model->getQuery(),
            $filter['page'],
            $filter['limit'],
            $options
        );

        if ($filter['debug']) {
            $this->debugQL($model, $query);
        }

        if ($getSQL) {
            return $this->getQL($model, $query, true);
        }

        // create item
        $totalItem = $pagination->getTotalItemCount();

        return [
            Abs::PG_CURRENT_PAGE => $filter['page'],
            Abs::PG_PAGE_SIZE    => $filter['limit'],
            Abs::PG_TOTAL_PAGE   => ceil($totalItem / $filter['limit']),
            Abs::PG_TOTAL_ITEM   => $totalItem,
            Abs::PG_ITEMS        => $pagination->getItems(),
        ];
    }

    /**
     * List key-value pair
     *
     * @param array           $valueFields
     * @param string          $key
     * @param callable|string $handler
     * @param array           ...$filter
     *
     * @return array
     * @noinspection PhpDocSignatureInspection
     */
    public function kvp(array $valueFields, string $key = Abs::PK, $handler = null, array ...$filter): array
    {
        array_unshift($valueFields, $key);
        $valueFields = Helper::arrayMap(
            $valueFields,
            function ($v) {
                return Helper::tableFieldAddAlias($v, 'kvp');
            }
        );

        if ($filter) {
            $this->filters(...$filter);
        }

        $list = $this->lister(
            [
                'limit'  => 0,
                'alias'  => 'kvp',
                'select' => $valueFields,
                'where'  => [$this->expr->eq('kvp.state', ':state')],
                'args'   => ['state' => [Abs::NORMAL]],
            ]
        );

        $list = Helper::arrayColumn($list, false, $key);

        if (empty($handler)) {
            $handler = array_fill(0, count($valueFields) - 1, '%s');
            $handler = implode(' ', $handler);
        }

        $listHandling = [];
        foreach ($list as $key => $item) {
            if (is_callable($handler)) {
                $listHandling[$key] = $handler($item, $key);
            } else {
                $listHandling[$key] = sprintf((string)$handler, ...array_values($item));
            }
        }

        return $listHandling;
    }

    /**
     * Updater
     *
     * @param array $filter
     *
     * @return false|int
     * @throws
     */
    public function updater(array $filter)
    {
        $filter = $this->getFilters($filter, ['method' => Abs::UPDATE]);
        $model = $this->getQueryBuilder($filter);
        $query = $model->getQuery();

        if ($filter['debug']) {
            $this->debugQL($model, $query);
        }

        try {
            return $query->getResult();
        } catch (Throwable $e) {
            return $this->push($e->getMessage(), Abs::TAG_UPDATE);
        }
    }

    /**
     * Deleter
     *
     * @param array $filter
     *
     * @return false|int
     * @throws
     */
    public function deleter(array $filter)
    {
        $filter = $this->getFilters($filter, ['method' => Abs::DELETE]);
        $model = $this->getQueryBuilder($filter);
        $query = $model->getQuery();

        if ($filter['debug']) {
            $this->debugQL($model, $query);
        }

        try {
            return $query->getResult();
        } catch (Throwable $e) {
            return $this->push($e->getMessage(), Abs::TAG_DELETE);
        }
    }
}
