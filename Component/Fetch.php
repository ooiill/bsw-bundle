<?php

namespace Leon\BswBundle\Component;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Leon\BswBundle\Module\Entity\Abs;
use Leon\BswBundle\Module\Error\Entity\ErrorDebugExit;
use Exception;

class Fetch
{
    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var Connection
     */
    protected $pdo;

    /**
     * @var string
     */
    protected $sql;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var array
     */
    protected $types = [];

    /**
     * @var string
     */
    protected $splitChar;

    /**
     * Fetch constructor.
     *
     * @param Connection $pdo
     * @param bool       $debug
     */
    public function __construct(Connection $pdo, bool $debug = false)
    {
        $this->pdo = $pdo;
        $this->debug = $debug;

        $this->splitChar = $this->debug ? Abs::ENTER : Helper::enSpace();
    }

    /**
     * Get source
     *
     * @return array
     */
    protected function get(): array
    {
        return [$this->sql, $this->params, $this->types];
    }

    /**
     * Reset source
     *
     * @return Fetch
     */
    protected function reset(): Fetch
    {
        $this->sql = null;
        $this->params = [];
        $this->types = [];

        return $this;
    }

    /**
     * Pagination
     *
     * @param int $page
     * @param int $limit
     *
     * @return array
     * @throws
     */
    protected function pagination(int $page, int $limit): array
    {
        $page = $page < 1 ? 1 : $page;
        $limit = abs($limit);

        if ($page < 1 || $limit < 1) {
            throw new Exception('Both `page` and `limit` should greater than 0');
        }

        $offset = ($page - 1) * $limit;

        $this->sql('LIMIT ?', $limit, true);
        $this->sql('OFFSET ?', $offset, true);

        return $this->get();
    }

    /**
     * Organize sql
     *
     * @param string $sql
     * @param mixed  $param
     * @param bool   $integer
     *
     * @return Fetch
     */
    public function sql(string $sql, $param = null, bool $integer = false)
    {
        $sql = trim($sql, $this->splitChar);
        $this->sql = trim("{$this->sql}{$this->splitChar}{$sql}", $this->splitChar);

        if (!is_null($param)) {
            $type = $integer ? Types::INTEGER : Types::STRING;
            if (is_array($param)) {
                $this->params = array_merge($this->params, $param);
                $this->types = array_merge($this->types, array_fill(0, count($param), $type));
            } else {
                array_push($this->params, $param);
                array_push($this->types, $type);
            }
        }

        return $this;
    }

    /**
     * Get collect
     *
     * @param callable $handler
     * @param int      $page
     * @param int      $hint
     * @param int      $limit
     *
     * @return array
     * @throws
     */
    public function collect(
        callable $handler = null,
        int $page = null,
        int $hint = null,
        int $limit = Abs::PAGE_DEFAULT_SIZE
    ) {

        $all = $this->get();
        if ($this->debug) {
            dump(...$all);
            exit(ErrorDebugExit::CODE);
        }

        /**
         * Handler for items
         *
         * @param array $items
         *
         * @return array
         */
        $handleItems = function (array $items) use ($handler) {
            $this->reset();
            if (!$handler) {
                return $items;
            }
            foreach ($items as &$item) {
                $item = call_user_func_array($handler, [$item]);
            }

            return $items;
        };

        if (is_null($page)) {
            return $handleItems($this->pdo->fetchAll(...$all));
        }

        // need page
        $pagination = $this->pagination($page, $limit);

        if ($hint) {
            $totalItem = $hint;
        } else {
            $allItems = $all;
            $allItems[0] = "SELECT COUNT(*) AS total FROM ({$allItems[0]}) AS _PAGE";
            $totalItem = $this->pdo->fetchAll(...$allItems);
            $totalItem = intval(current($totalItem)['total'] ?: 0);
        }

        return [
            Abs::PG_CURRENT_PAGE => $page,
            Abs::PG_PAGE_SIZE    => $limit,
            Abs::PG_TOTAL_PAGE   => ceil($totalItem / $limit),
            Abs::PG_TOTAL_ITEM   => $totalItem,
            Abs::PG_ITEMS        => $handleItems($this->pdo->fetchAll(...$pagination)),
        ];
    }
}